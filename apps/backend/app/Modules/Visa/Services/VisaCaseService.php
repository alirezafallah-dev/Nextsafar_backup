<?php

namespace App\Modules\Visa\Services;

use App\Core\Logging\AppLogger;
use App\Exceptions\ApiException;
use App\Core\ApiErrorCode;
use App\Models\User;
use App\Modules\Document\Models\Document;
use App\Modules\Lead\Models\Lead;
use App\Modules\Visa\Enums\VisaCaseStatus;
use App\Modules\Visa\Events\VisaCaseCompleted;
use App\Modules\Visa\Events\VisaCaseCreated;
use App\Modules\Visa\Events\VisaCaseStatusChanged;
use App\Modules\Visa\Events\VisaDocumentVerified;
use App\Modules\Visa\Models\VisaCase;
use App\Modules\Visa\Models\VisaCaseDocument;
use App\Modules\Visa\Models\VisaCaseNote;
use App\Modules\Visa\Models\VisaCasePassenger;
use App\Modules\Visa\Models\VisaCaseTimeline;
use App\Modules\Visa\Models\VisaDocumentChecklist;
use App\Modules\Visa\Models\VisaType;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Visa Case Service
 * 
 * طبق بخش ۱۶ سند:
 * - مدیریت پرونده‌های ویزا
 * - اتصال به Document module (فاز ۵.۱)
 * - محاسبه درصد تکمیل مدارک
 * - State machine با ۱۴ وضعیت
 * - تایم‌لاین کامل
 */
class VisaCaseService
{
    // =====================================================================
    // ۱. ایجاد پرونده
    // =====================================================================

    /**
     * ایجاد پرونده ویزا
     */
    public function createCase(
        int $userId,
        int $visaTypeId,
        array $data = [],
        ?int $orderId = null,
        ?int $leadId = null,
    ): VisaCase {
        $visaType = VisaType::findOrFail($visaTypeId);

        return DB::transaction(function () use ($userId, $visaType, $data, $orderId, $leadId) {
            // محاسبه قیمت
            $serviceFee = $data['service_fee'] ?? $visaType->base_price;
            $embassyFee = $data['embassy_fee'] ?? $visaType->embassy_fee;
            $totalAmount = $serviceFee + $embassyFee;

            $case = VisaCase::create(array_merge($data, [
                'code' => VisaCase::generateUniqueCode(),
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'user_id' => $userId,
                'visa_type_id' => $visaType->id,
                'order_id' => $orderId,
                'lead_id' => $leadId,
                'status' => VisaCaseStatus::REGISTERED,
                'service_fee' => $serviceFee,
                'embassy_fee' => $embassyFee,
                'total_amount' => $totalAmount,
                'paid_amount' => $data['paid_amount'] ?? 0,
                'payment_status' => 'unpaid',
                'sla_days' => $data['sla_days'] ?? ($visaType->processing_days_max ?? 30),
                'total_checklist_items' => $visaType->checklist()->count(),
                'uploaded_documents_count' => 0,
                'approved_documents_count' => 0,
                'documents_completion_percentage' => 0,
            ]));

            // ثبت در تایم‌لاین
            VisaCaseTimeline::record(
                $case,
                'status_change',
                'پرونده ایجاد شد',
                "پرونده ویزا {$visaType->country->name} ایجاد شد",
                [
                    'visa_type' => $visaType->title,
                    'country' => $visaType->country->name,
                ],
                'both'
            );

            // Dispatch event
            event(new VisaCaseCreated($case));

            AppLogger::booking()->info('visa_case.created', [
                'case_code' => $case->code,
                'visa_type' => $visaType->title,
                'user_id' => $userId,
            ]);

            return $case->fresh();
        });
    }

    /**
     * ایجاد پرونده از Lead (تبدیل Lead به Visa Case)
     */
    public function createFromLead(Lead $lead, int $visaTypeId, array $data = []): VisaCase
    {
        if ($lead->type !== 'visa_request') {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'فقط لیدهای مربوط به ویزا قابل تبدیل هستند.',
                httpStatus: 422,
            );
        }

        $case = $this->createCase(
            userId: $lead->user_id ?? $this->findOrCreateUserFromLead($lead),
            visaTypeId: $visaTypeId,
            data: array_merge($data, [
                'customer_notes' => $lead->message,
            ]),
            leadId: $lead->id,
        );

        // به‌روزرسانی Lead
        $lead->update([
            'status' => 'converted_to_booking',
            'converted_at' => now(),
        ]);

        return $case;
    }

    // =====================================================================
    // ۲. مدیریت وضعیت (State Machine)
    // =====================================================================

    /**
     * تغییر وضعیت پرونده
     * 
     * قانون ۱۶.۲: هر تغییر وضعیت باید:
     * 1. از state machine مجاز باشد
     * 2. در تایم‌لاین ثبت شود
     * 3. Event dispatch شود
     */
    public function changeStatus(
        VisaCase $case,
        VisaCaseStatus $newStatus,
        ?string $reason = null,
        ?User $changedBy = null,
    ): VisaCase {
        $oldStatus = $case->status;

        if ($oldStatus === $newStatus) {
            return $case;
        }

        // بررسی مجاز بودن انتقال
        if (!$case->canTransitionTo($newStatus)) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: "انتقال از '{$oldStatus->label()}' به '{$newStatus->label()}' مجاز نیست.",
                httpStatus: 422,
                details: [
                    'current' => $oldStatus->value,
                    'requested' => $newStatus->value,
                    'allowed' => array_map(fn($s) => $s->value, $oldStatus->allowedTransitions()),
                ],
            );
        }

        return DB::transaction(function () use ($case, $newStatus, $oldStatus, $reason, $changedBy) {
            // تنظیم timestamp های خاص
            $updates = ['status' => $newStatus];

            if ($newStatus === VisaCaseStatus::DOCUMENTS_COMPLETE && !$case->documents_completed_at) {
                $updates['documents_completed_at'] = now();
            }
            if ($newStatus === VisaCaseStatus::SUBMITTED && !$case->submitted_to_embassy_at) {
                $updates['submitted_to_embassy_at'] = now();
            }
            if ($newStatus === VisaCaseStatus::NEEDS_BIOMETRICS && !$case->biometrics_completed_at) {
                $updates['biometrics_completed_at'] = now();
            }
            if ($newStatus === VisaCaseStatus::APPROVED || $newStatus === VisaCaseStatus::REJECTED) {
                $updates['result_received_at'] = now();
            }
            if ($newStatus === VisaCaseStatus::COMPLETED) {
                $updates['completed_at'] = now();
            }
            if ($newStatus === VisaCaseStatus::CANCELLED) {
                $updates['cancelled_at'] = now();
                $updates['cancellation_reason'] = $reason;
            }
            if ($newStatus === VisaCaseStatus::REJECTED) {
                $updates['rejection_reason'] = $reason;
            }

            $case->update($updates);

            // ثبت در تایم‌لاین
            VisaCaseTimeline::create([
                'visa_case_id' => $case->id,
                'event_type' => 'status_change',
                'title' => "تغییر وضعیت: {$oldStatus->label()} → {$newStatus->label()}",
                'description' => $reason,
                'old_status' => $oldStatus->value,
                'new_status' => $newStatus->value,
                'user_id' => $changedBy?->id ?? auth()->id(),
                'user_name' => $changedBy?->name ?? auth()->user()?->name ?? 'System',
                'user_type' => $changedBy ? 'agent' : 'system',
                'visibility' => 'both',
                'occurred_at' => now(),
            ]);

            // Dispatch event
            event(new VisaCaseStatusChanged($case, $oldStatus, $newStatus, $reason));

            // اگر پرونده به پایان رسید، event تکمیل را dispatch کن
            if ($newStatus->isTerminal()) {
                event(new VisaCaseCompleted($case, $newStatus->isSuccessful()));
            }

            AppLogger::booking()->info('visa_case.status_changed', [
                'case_code' => $case->code,
                'old' => $oldStatus->value,
                'new' => $newStatus->value,
                'reason' => $reason,
                'by' => $changedBy?->id,
            ]);

            return $case->fresh();
        });
    }

    // =====================================================================
    // ۳. مدیریت مدارک (اتصال به Document module)
    // =====================================================================

    /**
     * اتصال یک Document به پرونده (برای یک آیتم چک‌لیست)
     */
    public function attachDocument(
        VisaCase $case,
        Document $document,
        ?int $checklistItemId = null,
        ?int $passengerId = null,
        bool $isOriginal = true,
        bool $needsTranslation = false,
    ): VisaCaseDocument {
        // بررسی اینکه این Document برای این کاربر است
        if ($document->user_id !== $case->user_id) {
            throw ApiException::forbidden('این فایل متعلق به این کاربر نیست.');
        }

        return DB::transaction(function () use ($case, $document, $checklistItemId, $passengerId, $isOriginal, $needsTranslation) {
            // بررسی تکراری نبودن
            $existing = VisaCaseDocument::where('visa_case_id', $case->id)
                ->where('document_id', $document->id)
                ->first();

            if ($existing) {
                throw new ApiException(
                    errorCode: ApiErrorCode::VALIDATION_FAILED,
                    userMessage: 'این مدرک قبلاً به پرونده اضافه شده است.',
                    httpStatus: 422,
                );
            }

            $caseDoc = VisaCaseDocument::create([
                'visa_case_id' => $case->id,
                'document_id' => $document->id,
                'checklist_item_id' => $checklistItemId,
                'passenger_id' => $passengerId,
                'verification_status' => 'pending',
                'is_original' => $isOriginal,
                'needs_translation' => $needsTranslation,
                'translation_status' => $needsTranslation ? 'pending' : 'none',
            ]);

            // ثبت در تایم‌لاین
            VisaCaseTimeline::create([
                'visa_case_id' => $case->id,
                'event_type' => 'document_uploaded',
                'title' => "مدرک آپلود شد: {$document->title}",
                'description' => $document->category->label(),
                'related_type' => VisaCaseDocument::class,
                'related_id' => $caseDoc->id,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Customer',
                'user_type' => auth()->check() ? 'customer' : 'system',
                'visibility' => 'both',
                'occurred_at' => now(),
            ]);

            // به‌روزرسانی شمارنده‌های پرونده
            $this->recalculateDocumentsStats($case);

            return $caseDoc;
        });
    }

    /**
     * تأیید/رد/درخواست اصلاح مدرک
     */
    public function verifyDocument(
        VisaCaseDocument $caseDoc,
        string $status, // approved, needs_correction, rejected
        ?string $notes = null,
        ?User $verifiedBy = null,
    ): VisaCaseDocument {
        if (!in_array($status, ['approved', 'needs_correction', 'rejected'])) {
            throw new ApiException(
                errorCode: ApiErrorCode::INVALID_INPUT,
                userMessage: 'وضعیت تأیید نامعتبر است.',
                httpStatus: 422,
            );
        }

        $verifier = $verifiedBy ?? auth()->user();

        return DB::transaction(function () use ($caseDoc, $status, $notes, $verifier) {
            $caseDoc->update([
                'verification_status' => $status,
                'verified_by' => $verifier?->id,
                'verified_at' => now(),
                'verification_notes' => $notes,
            ]);

            // ثبت در تایم‌لاین
            $eventType = match ($status) {
                'approved' => 'document_verified',
                'needs_correction', 'rejected' => 'document_rejected',
            };

            VisaCaseTimeline::create([
                'visa_case_id' => $caseDoc->visa_case_id,
                'event_type' => $eventType,
                'title' => "مدرک {$this->getVerificationLabel($status)}: {$caseDoc->document->title}",
                'description' => $notes,
                'related_type' => VisaCaseDocument::class,
                'related_id' => $caseDoc->id,
                'user_id' => $verifier?->id,
                'user_name' => $verifier?->name ?? 'System',
                'user_type' => 'agent',
                'visibility' => 'both',
                'occurred_at' => now(),
            ]);

            // به‌روزرسانی شمارنده‌ها
            $this->recalculateDocumentsStats($caseDoc->visaCase);

            // Dispatch event
            event(new VisaDocumentVerified($caseDoc, $status, $notes));

            return $caseDoc->fresh();
        });
    }

    /**
     * محاسبه مجدد آمار مدارک پرونده
     */
    public function recalculateDocumentsStats(VisaCase $case): VisaCase
    {
        $totalChecklistItems = $case->visaType->checklist()->count();

        $uploaded = VisaCaseDocument::where('visa_case_id', $case->id)
            ->distinct('checklist_item_id')
            ->count('checklist_item_id');

        $approved = VisaCaseDocument::where('visa_case_id', $case->id)
            ->where('verification_status', 'approved')
            ->distinct('checklist_item_id')
            ->count('checklist_item_id');

        $percentage = $totalChecklistItems > 0
            ? (int) round(($approved / $totalChecklistItems) * 100)
            : 0;

        $case->update([
            'total_checklist_items' => $totalChecklistItems,
            'uploaded_documents_count' => $uploaded,
            'approved_documents_count' => $approved,
            'documents_completion_percentage' => $percentage,
        ]);

        // اگر مدارک کامل شد و وضعیت فعلی DOCUMENTS_INCOMPLETE است، خودکار منتقل کن
        if ($percentage >= 100 && $case->status === VisaCaseStatus::DOCUMENTS_INCOMPLETE) {
            $this->changeStatus($case, VisaCaseStatus::DOCUMENTS_COMPLETE, 'مدارک کامل شد');
        }

        return $case->fresh();
    }

    /**
     * دریافت وضعیت چک‌لیست پرونده (برای نمایش به کاربر/ادمین)
     */
    public function getChecklistStatus(VisaCase $case): array
    {
        $checklistItems = $case->visaType->checklist()
            ->orderBy('sort_order')
            ->get();

        $attachedDocuments = $case->documents()
            ->with(['document', 'passenger'])
            ->get()
            ->groupBy('checklist_item_id');

        $result = [];
        foreach ($checklistItems as $item) {
            $attached = $attachedDocuments->get($item->id, collect());

            $result[] = [
                'checklist_item' => $item,
                'is_required' => $item->is_required,
                'importance' => $item->importance,
                'documents' => $attached->map(fn($doc) => [
                    'id' => $doc->id,
                    'document' => $doc->document,
                    'passenger' => $doc->passenger,
                    'verification_status' => $doc->verification_status,
                    'verified_at' => $doc->verified_at,
                    'verification_notes' => $doc->verification_notes,
                ]),
                'status' => $this->calculateChecklistItemStatus($attached),
            ];
        }

        return [
            'items' => $result,
            'summary' => [
                'total' => $checklistItems->count(),
                'required' => $checklistItems->where('is_required', true)->count(),
                'uploaded' => $case->uploaded_documents_count,
                'approved' => $case->approved_documents_count,
                'completion_percentage' => $case->documents_completion_percentage,
            ],
        ];
    }

    // =====================================================================
    // ۴. مدیریت مسافران
    // =====================================================================

    /**
     * افزودن مسافر به پرونده
     */
    public function addPassenger(VisaCase $case, array $data): VisaCasePassenger
    {
        $data['visa_case_id'] = $case->id;

        // اگر اولین مسافر است، او را متقاضی اصلی قرار بده
        if ($case->passengers()->count() === 0) {
            $data['is_main_applicant'] = true;
        }

        $passenger = VisaCasePassenger::create($data);

        VisaCaseTimeline::create([
            'visa_case_id' => $case->id,
            'event_type' => 'passenger_added',
            'title' => "مسافر اضافه شد: {$passenger->full_name}",
            'related_type' => VisaCasePassenger::class,
            'related_id' => $passenger->id,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?? 'Customer',
            'user_type' => auth()->check() ? (auth()->user()->hasPermission('visa.manage') ? 'agent' : 'customer') : 'system',
            'visibility' => 'both',
            'occurred_at' => now(),
        ]);

        return $passenger;
    }

    // =====================================================================
    // ۵. یادداشت‌ها
    // =====================================================================

    /**
     * افزودن یادداشت به پرونده
     */
    public function addNote(
        VisaCase $case,
        string $content,
        string $type = 'general',
        string $visibility = 'internal',
        ?User $author = null,
        ?int $parentId = null,
    ): VisaCaseNote {
        $author = $author ?? auth()->user();

        $note = VisaCaseNote::create([
            'visa_case_id' => $case->id,
            'type' => $type,
            'content' => $content,
            'visibility' => $visibility,
            'author_id' => $author?->id,
            'author_name' => $author?->name ?? 'System',
            'author_type' => $author ? ($author->hasPermission('visa.manage') ? 'agent' : 'customer') : 'system',
            'parent_id' => $parentId,
        ]);

        VisaCaseTimeline::create([
            'visa_case_id' => $case->id,
            'event_type' => 'note_added',
            'title' => 'یادداشت جدید',
            'description' => mb_substr($content, 0, 100) . (mb_strlen($content) > 100 ? '...' : ''),
            'related_type' => VisaCaseNote::class,
            'related_id' => $note->id,
            'user_id' => $author?->id,
            'user_name' => $author?->name ?? 'System',
            'user_type' => $author ? 'agent' : 'system',
            'visibility' => $visibility,
            'occurred_at' => now(),
        ]);

        return $note;
    }

    // =====================================================================
    // ۶. وقت سفارت و اختصاص
    // =====================================================================

    /**
     * تنظیم وقت سفارت
     */
    public function bookAppointment(
        VisaCase $case,
        $datetime,
        string $location,
        ?string $reference = null,
        ?User $bookedBy = null,
    ): VisaCase {
        $case->update([
            'appointment_datetime' => $datetime,
            'appointment_location' => $location,
            'appointment_reference' => $reference,
        ]);

        VisaCaseTimeline::create([
            'visa_case_id' => $case->id,
            'event_type' => 'appointment_booked',
            'title' => "وقت سفارت: {$datetime}",
            'description' => "مکان: {$location}" . ($reference ? " | کد: {$reference}" : ''),
            'user_id' => $bookedBy?->id ?? auth()->id(),
            'user_name' => $bookedBy?->name ?? auth()->user()?->name ?? 'System',
            'user_type' => 'agent',
            'visibility' => 'both',
            'occurred_at' => now(),
        ]);

        // اگر وضعیت NEEDS_APPOINTMENT است، به APPOINTMENT_BOOKED منتقل کن
        if ($case->status === VisaCaseStatus::NEEDS_APPOINTMENT) {
            $this->changeStatus($case, VisaCaseStatus::APPOINTMENT_BOOKED, "وقت سفارت: {$datetime}");
        }

        return $case->fresh();
    }

    /**
     * اختصاص پرونده به کارشناس
     */
    public function assignToAgent(VisaCase $case, User $agent, ?User $assignedBy = null): VisaCase
    {
        $case->update([
            'assigned_to' => $agent->id,
            'assigned_by' => $assignedBy?->id ?? auth()->id(),
            'assigned_at' => now(),
        ]);

        VisaCaseTimeline::create([
            'visa_case_id' => $case->id,
            'event_type' => 'assigned',
            'title' => "پرونده به {$agent->name} اختصاص یافت",
            'user_id' => $assignedBy?->id ?? auth()->id(),
            'user_name' => $assignedBy?->name ?? auth()->user()?->name ?? 'System',
            'user_type' => 'admin',
            'visibility' => 'internal',
            'occurred_at' => now(),
        ]);

        return $case->fresh();
    }

    // =====================================================================
    // ۷. Helpers
    // =====================================================================

    private function calculateChecklistItemStatus($attached): string
    {
        if ($attached->isEmpty()) {
            return 'missing';
        }

        $hasApproved = $attached->contains('verification_status', 'approved');
        if ($hasApproved) {
            return 'complete';
        }

        $hasPending = $attached->contains('verification_status', 'pending');
        if ($hasPending) {
            return 'pending_review';
        }

        return 'needs_correction';
    }

    private function getVerificationLabel(string $status): string
    {
        return match ($status) {
            'approved' => 'تأیید شد',
            'needs_correction' => 'نیازمند اصلاح',
            'rejected' => 'رد شد',
            default => $status,
        };
    }

    private function findOrCreateUserFromLead(Lead $lead): int
    {
        if ($lead->user_id) {
            return $lead->user_id;
        }

        // اگر لید بدون کاربر است، یک کاربر جدید بساز
        $user = User::create([
            'name' => $lead->full_name,
            'phone' => $lead->phone,
            'email' => $lead->email,
            'password' => bcrypt(str()->random(16)),
            'is_active' => true,
        ]);

        $lead->update(['user_id' => $user->id]);

        return $user->id;
    }
}
