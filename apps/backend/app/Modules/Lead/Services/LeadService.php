<?php

namespace App\Modules\Lead\Services;

use App\Core\Logging\AppLogger;
use App\Exceptions\ApiException;
use App\Core\ApiErrorCode;
use App\Modules\Lead\Models\Lead;
use App\Modules\Lead\Events\LeadCreated;
use App\Modules\Lead\Events\LeadStatusChanged;
use App\Modules\Lead\Models\LeadNote;
use App\Modules\Lead\Repositories\LeadRepository;
use Illuminate\Support\Facades\DB;

class LeadService
{
    public function __construct(
        protected LeadRepository $repository,
    ) {}

    /**
     * Create a new lead from public form
     * Includes duplicate detection (قانون ۱۰.۴.۳)
     */
    public function createFromPublicForm(array $data): Lead
    {
        // اعتبارسنجی شماره (قانون ۱۰.۴.۵)
        $phone = $this->normalizePhone($data['phone']);
        if (!$this->isValidIranianPhone($phone)) {
            throw new ApiException(
                errorCode: ApiErrorCode::INVALID_INPUT,
                userMessage: 'شماره تماس معتبر نیست.',
                httpStatus: 422,
                details: ['phone' => $phone],
            );
        }

        $data['phone'] = $phone;

        // تشخیص تکراری (قانون ۱۰.۴.۳)
        $duplicates = Lead::findDuplicates($phone);
        $isDuplicate = false;
        $originalLead = null;

        if ($duplicates->count() > 0) {
            // اگر در ۳۰ روز گذشته درخواست مشابه داشته، تکراری محسوب شود
            $originalLead = $duplicates->first();
            
            // اگر درخواست قبلی تبدیل به سفارش یا بسته شده، تکراری حساب نکنیم
            if (!in_array($originalLead->status, [Lead::STATUS_CONVERTED, Lead::STATUS_CLOSED, Lead::STATUS_CANCELLED])) {
                $isDuplicate = true;
            }
        }

        $data['is_duplicate'] = $isDuplicate;
        if ($isDuplicate && $originalLead) {
            $data['duplicate_of'] = $originalLead->id;
            $data['status'] = Lead::STATUS_DUPLICATE;
        }

        return DB::transaction(function () use ($data, $isDuplicate, $originalLead) {
            $lead = $this->repository->create($data);

            // ثبت تاریخچه
            $this->repository->addStatusHistory($lead, Lead::STATUS_NEW, 'ثبت از فرم عمومی');

            // اگر تکراری است، یادداشت اضافه شود
            if ($isDuplicate && $originalLead) {
                $this->repository->addNote($lead, [
                    'type' => LeadNote::TYPE_SYSTEM,
                    'content' => "این درخواست تکراری تشخیص داده شد. لید اصلی: #{$originalLead->code}",
                    'visibility' => 'internal',
                    'author_name' => 'System',
                ]);

                // یادداشت به لید اصلی هم اضافه شود
                $this->repository->addNote($originalLead, [
                    'type' => LeadNote::TYPE_SYSTEM,
                    'content' => "درخواست تکراری جدید ثبت شد: #{$lead->code}",
                    'visibility' => 'internal',
                    'author_name' => 'System',
                ]);
            }

            AppLogger::api()->info('lead.created', [
                'lead_id' => $lead->id,
                'code' => $lead->code,
                'type' => $lead->type,
                'is_duplicate' => $isDuplicate,
                'source' => $lead->source,
            ]);

            // TODO: در فاز بعدی، dispatch job برای اطلاع‌رسانی
            // SendNotificationJob::dispatch('lead.created', ['lead_id' => $lead->id]);

            return $lead;
        });
    }

    /**
     * Change lead status with history logging (قانون ۱۰.۴.۲)
     */
    public function changeStatus(Lead $lead, string $newStatus, ?string $reason = null): Lead
    {
        if (!in_array($newStatus, Lead::ALL_STATUSES)) {
            throw new ApiException(
                errorCode: ApiErrorCode::INVALID_INPUT,
                userMessage: 'وضعیت نامعتبر است.',
                httpStatus: 422,
            );
        }

        if ($lead->status === $newStatus) {
            return $lead;
        }

        $oldStatus = $lead->status;

        return DB::transaction(function () use ($lead, $newStatus, $oldStatus, $reason) {
            // ثبت تاریخچه قبل از تغییر
            $this->repository->addStatusHistory(
                $lead,
                $newStatus,
                $reason,
                auth()->id()
            );

            $lead->status = $newStatus;

            // اگر تبدیل به سفارش شد
            if ($newStatus === Lead::STATUS_CONVERTED) {
                $lead->converted_at = now();
            }

            $lead->save();


        event(new LeadStatusChanged($lead, $oldStatus, $newStatus, $reason));
            AppLogger::admin()->info('lead.status_changed', [
                'lead_id' => $lead->id,
                'code' => $lead->code,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by' => auth()->id(),
                'reason' => $reason,
            ]);

            return $lead->fresh();
        });
    }

    /**
     * Assign lead to user
     */
    public function assignTo(Lead $lead, int $userId, ?string $reason = null): Lead
    {
        $user = \App\Models\User::find($userId);
        if (!$user) {
            throw ApiException::notFound('کارشناس');
        }

        $lead->assigned_to = $userId;
        $lead->assigned_by = auth()->id();
        $lead->assigned_at = now();
        $lead->save();

        // ثبت یادداشت سیستمی
        $this->repository->addNote($lead, [
            'type' => LeadNote::TYPE_SYSTEM,
            'content' => "درخواست به {$user->name} اختصاص داده شد.",
            'visibility' => 'internal',
            'author_id' => auth()->id(),
            'author_name' => auth()->user()?->name ?? 'System',
        ]);

        AppLogger::admin()->info('lead.assigned', [
            'lead_id' => $lead->id,
            'code' => $lead->code,
            'assigned_to' => $userId,
            'assigned_by' => auth()->id(),
        ]);

        return $lead->fresh(['assignedTo']);
    }

    /**
     * Record contact with lead
     */
    public function recordContact(Lead $lead, string $contactType, ?string $content = null): LeadNote
    {
        $lead->recordContact();

        $note = $this->repository->addNote($lead, [
            'type' => $contactType,
            'content' => $content ?? 'تماس ثبت شد',
            'visibility' => 'internal',
            'author_id' => auth()->id(),
            'author_name' => auth()->user()?->name ?? 'System',
        ]);

        // تغییر وضعیت به contacted (اگر اولین تماس است)
        if ($lead->status === Lead::STATUS_NEW) {
            $this->changeStatus($lead, Lead::STATUS_CONTACTED, 'اولین تماس');
        }

        return $note;
    }

    /**
     * Schedule follow-up
     */
    public function scheduleFollowUp(Lead $lead, $dateTime, ?string $note = null): Lead
    {
        $lead->scheduleFollowUp($dateTime);

        if ($note) {
            $this->repository->addNote($lead, [
                'type' => LeadNote::TYPE_TASK,
                'content' => "پیگیری بعدی در تاریخ {$dateTime->format('Y-m-d H:i')}: {$note}",
                'visibility' => 'internal',
                'author_id' => auth()->id(),
                'author_name' => auth()->user()?->name ?? 'System',
            ]);
        }

        AppLogger::admin()->info('lead.follow_up_scheduled', [
            'lead_id' => $lead->id,
            'follow_up_at' => $dateTime,
        ]);

        return $lead->fresh();
    }

    /**
     * Convert lead to booking
     * TODO: در فاز بعدی با ماژول سفارش یکپارچه می‌شود
     */
    public function convertToBooking(Lead $lead, array $bookingData): array
    {
        // فعلاً فقط وضعیت را تغییر می‌دهیم
        $this->changeStatus($lead, Lead::STATUS_CONVERTED, 'تبدیل به سفارش');

        $lead->converted_at = now();
        $lead->save();

        $this->repository->addNote($lead, [
            'type' => LeadNote::TYPE_SYSTEM,
            'content' => 'این درخواست به سفارش تبدیل شد. (ماژول سفارش در فاز بعدی اضافه می‌شود)',
            'visibility' => 'internal',
            'author_id' => auth()->id(),
            'author_name' => auth()->user()?->name ?? 'System',
        ]);

        return [
            'lead' => $lead->fresh(),
            'booking_id' => null, // TODO: در فاز بعدی
            'message' => 'درخواست به سفارش تبدیل شد.',
        ];
    }

    /**
     * Add note to lead
     */
    public function addNote(Lead $lead, array $data): LeadNote
    {
        $data['author_id'] = auth()->id();
        $data['author_name'] = auth()->user()?->name ?? 'System';
        $data['visibility'] = $data['visibility'] ?? 'internal';

        return $this->repository->addNote($lead, $data);
    }

    /**
     * Update lead data
     */
    public function update(Lead $lead, array $data): Lead
    {
        // نرمال‌سازی شماره تلفن در صورت وجود
        if (isset($data['phone'])) {
            $data['phone'] = $this->normalizePhone($data['phone']);
        }

        return $this->repository->update($lead, $data);
    }

    /**
     * Delete lead (soft)
     */
    public function delete(Lead $lead): bool
    {
        $result = $this->repository->delete($lead);

        if ($result) {
            AppLogger::admin()->info('lead.deleted', [
                'lead_id' => $lead->id,
                'code' => $lead->code,
            ]);
        }

        return $result;
    }

    /**
     * Auto-assign lead based on type (Round Robin)
     * TODO: در فاز بعدی الگوریتم پیچیده‌تر اضافه می‌شود
     */
    public function autoAssign(Lead $lead): ?Lead
    {
        $roleMap = [
            Lead::TYPE_TOUR => 'tour_expert',
            Lead::TYPE_HOTEL => 'hotel_expert',
            Lead::TYPE_VISA => 'visa_expert',
            Lead::TYPE_CONSULTATION => 'support',
            Lead::TYPE_CALL => 'support',
            Lead::TYPE_PRICE => 'support',
        ];

        $targetRole = $roleMap[$lead->type] ?? 'support';

        // پیدا کردن کاربر فعال با این نقش که کمترین لید فعال را دارد
        $availableUser = \App\Models\User::whereHas('roles', function ($q) use ($targetRole) {
            $q->where('slug', $targetRole);
        })
            ->where('is_active', true)
            ->withCount(['leads' => function ($q) {
                $q->open();
            }])
            ->orderBy('leads_count')
            ->first();

        if ($availableUser) {
            return $this->assignTo($lead, $availableUser->id, 'اختصاص خودکار توسط سیستم');
        }

        return null;
    }

    // === Private Helpers ===

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // تبدیل +98, 0098, 98 به 0
        if (str_starts_with($phone, '98') && strlen($phone) === 12) {
            $phone = '0' . substr($phone, 2);
        } elseif (str_starts_with($phone, '0098') && strlen($phone) === 14) {
            $phone = '0' . substr($phone, 4);
        }

        return $phone;
    }

    private function isValidIranianPhone(string $phone): bool
    {
        return preg_match('/^09[0-9]{9}$/', $phone) === 1;
    }
}
