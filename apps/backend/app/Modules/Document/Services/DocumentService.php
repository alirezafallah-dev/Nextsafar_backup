<?php

namespace App\Modules\Document\Services;

use App\Core\Logging\AppLogger;
use App\Exceptions\ApiException;
use App\Core\ApiErrorCode;
use App\Modules\Document\Enums\DocumentCategory;
use App\Modules\Document\Enums\DocumentVisibility;
use App\Modules\Document\Enums\ScanStatus;
use App\Modules\Document\Models\Document;
use App\Modules\Document\Models\DocumentAccessLog;
use App\Modules\Document\Models\DocumentTempLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentService
{
    public function __construct(
        protected StorageService $storage,
    ) {}

    // =====================================================================
    // ۱. آپلود فایل
    // =====================================================================

    /**
     * آپلود فایل با اعتبارسنجی کامل
     */
    public function upload(
        UploadedFile $file,
        DocumentCategory $category,
        DocumentVisibility $visibility,
        ?Model $attachTo = null,
        ?string $title = null,
        ?string $description = null,
        ?int $userId = null,
        array $metadata = []
    ): Document {
        // اعتبارسنجی
        $validation = $this->storage->validate($file, $category);
        if (!$validation['valid']) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'فایل نامعتبر است.',
                httpStatus: 422,
                details: ['errors' => $validation['errors']],
            );
        }

        return DB::transaction(function () use ($file, $category, $visibility, $attachTo, $title, $description, $userId, $metadata) {
            $storageData = $this->storage->store($file, $visibility, $category);

            $document = Document::create(array_merge($storageData, [
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'code' => Document::generateUniqueCode(),
                'category' => $category,
                'visibility' => $visibility,
                'documentable_type' => $attachTo ? get_class($attachTo) : null,
                'documentable_id' => $attachTo?->id,
                'user_id' => $userId ?? auth()->id(),
                'uploaded_by' => auth()->id(),
                'title' => $title ?? $file->getClientOriginalName(),
                'description' => $description,
                'metadata' => $metadata,
                'scan_status' => ScanStatus::PENDING,
            ]));

            // ثبت لاگ آپلود (قانون ۱۶.۳)
            DocumentAccessLog::log($document, 'upload');

            AppLogger::api()->info('document.uploaded', [
                'document_id' => $document->id,
                'code' => $document->code,
                'category' => $category->value,
                'visibility' => $visibility->value,
                'user_id' => $document->user_id,
                'size' => $document->size,
            ]);

            // TODO: Dispatch job for file scanning
            // dispatch(new ScanDocumentJob($document));

            return $document;
        });
    }

    // =====================================================================
    // ۲. دانلود / دسترسی به فایل
    // =====================================================================

    /**
     * دانلود فایل (عمومی یا با تأیید دسترسی)
     */
    public function download(Document $document, ?int $userId = null): BinaryFileResponse|StreamedResponse
    {
        // اعتبارسنجی دسترسی
        $this->validateAccess($document, $userId);

        // بررسی سالم بودن فایل
        if ($document->isInfected()) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'این فایل به عنوان آلوده شناسایی شده و قابل دانلود نیست.',
                httpStatus: 403,
            );
        }

        // بررسی وجود فایل
        if (!$this->storage->exists($document->path, $document->disk)) {
            throw ApiException::notFound('فایل');
        }

        // ثبت لاگ دسترسی (قانون ۱۶.۳ قانون ۲)
        DocumentAccessLog::log($document, 'download', $userId);
        $document->incrementDownloadCount();

        // اگر عمومی است، ریدایرکت به URL عمومی
        if ($document->isPublic()) {
            return response()->file(
                $this->storage->getFullPath($document->path, $document->disk),
                [
                    'Content-Type' => $document->mime_type,
                    'Content-Disposition' => 'inline; filename="' . $document->original_filename . '"',
                ]
            );
        }

        // اگر خصوصی است، stream response با نام امن
        return response()->download(
            $this->storage->getFullPath($document->path, $document->disk),
            $document->original_filename,
            [
                'Content-Type' => $document->mime_type,
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
            ]
        );
    }

    /**
     * دریافت URL عمومی (فقط برای فایل‌های عمومی)
     */
    public function getPublicUrl(Document $document): ?string
    {
        if (!$document->isPublic()) {
            return null;
        }
        return $this->storage->publicUrl($document->path);
    }

    // =====================================================================
    // ۳. لینک‌های زمان‌دار (قانون ۱۷.۲)
    // =====================================================================

    /**
     * ایجاد لینک زمان‌دار برای فایل خصوصی
     */
    public function createTempLink(
        Document $document,
        int $expiresInMinutes = 60,
        int $maxUses = 1,
        ?int $allowedUserId = null,
        ?string $allowedIp = null
    ): DocumentTempLink {
        if ($document->isPublic()) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'فایل‌های عمومی نیازی به لینک زمان‌دار ندارند.',
                httpStatus: 422,
            );
        }

        $link = DocumentTempLink::create([
            'document_id' => $document->id,
            'created_by' => auth()->id(),
            'max_uses' => min($maxUses, 100), // حداکثر ۱۰۰ استفاده
            'expires_at' => now()->addMinutes(min($expiresInMinutes, 1440)), // حداکثر ۲۴ ساعت
            'allowed_user_id' => $allowedUserId,
            'allowed_ip' => $allowedIp,
        ]);

        AppLogger::security()->info('document.temp_link_created', [
            'document_id' => $document->id,
            'document_code' => $document->code,
            'expires_at' => $link->expires_at,
            'max_uses' => $link->max_uses,
            'created_by' => auth()->id(),
        ]);

        return $link;
    }

    /**
     * استفاده از لینک زمان‌دار
     */
    public function accessViaTempLink(string $token): BinaryFileResponse|StreamedResponse
    {
        $link = DocumentTempLink::where('token', $token)->first();

        if (!$link) {
            throw ApiException::notFound('لینک');
        }

        if (!$link->canBeUsedBy(auth()->id(), request()->ip())) {
            // ثبت لاگ دسترسی ناموفق
            DocumentAccessLog::log(
                $link->document,
                'download',
                auth()->id(),
                'denied',
                'لینک منقضی شده یا نامعتبر است',
                $token
            );

            throw new ApiException(
                errorCode: ApiErrorCode::FORBIDDEN,
                userMessage: 'لینک منقضی شده یا نامعتبر است.',
                httpStatus: 403,
            );
        }

        // ثبت لاگ و استفاده
        DocumentAccessLog::log(
            $link->document,
            'download',
            auth()->id(),
            'success',
            null,
            $token
        );
        $link->useOnce();

        return $this->download($link->document, auth()->id());
    }

    /**
     * باطل کردن لینک
     */
    public function revokeTempLink(DocumentTempLink $link): void
    {
        $link->revoke();

        AppLogger::security()->info('document.temp_link_revoked', [
            'document_id' => $link->document_id,
            'token_prefix' => substr($link->token, 0, 8),
            'revoked_by' => auth()->id(),
        ]);
    }

    // =====================================================================
    // ۴. حذف فایل
    // =====================================================================

    /**
     * حذف فایل
     */
    public function delete(Document $document): bool
    {
        // بررسی دسترسی
        if (!$this->canDelete($document)) {
            throw ApiException::forbidden();
        }

        return DB::transaction(function () use ($document) {
            // حذف از storage
            $this->storage->delete($document->path, $document->disk);

            // باطل کردن همه لینک‌های زمان‌دار
            $document->tempLinks()->update(['is_revoked' => true]);

            // ثبت لاگ
            DocumentAccessLog::log($document, 'delete', auth()->id());

            // حذف (soft)
            $result = $document->delete();

            AppLogger::api()->info('document.deleted', [
                'document_id' => $document->id,
                'code' => $document->code,
                'deleted_by' => auth()->id(),
            ]);

            return $result;
        });
    }

    // =====================================================================
    // ۵. اسکن و اعتبارسنجی
    // =====================================================================

    /**
     * علامت‌گذاری فایل به عنوان سالم
     */
    public function markAsClean(Document $document): void
    {
        $document->update([
            'scan_status' => ScanStatus::CLEAN,
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    /**
     * علامت‌گذاری فایل به عنوان آلوده
     */
    public function markAsInfected(Document $document, string $reason): void
    {
        $document->update([
            'scan_status' => ScanStatus::INFECTED,
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        AppLogger::security()->warning('document.marked_infected', [
            'document_id' => $document->id,
            'code' => $document->code,
            'reason' => $reason,
        ]);
    }

    // =====================================================================
    // ۶. Helpers
    // =====================================================================

    private function validateAccess(Document $document, ?int $userId): void
    {
        // فایل‌های عمومی: همه دسترسی دارند
        if ($document->isPublic()) {
            return;
        }

        // فایل‌های خصوصی: نیاز به احراز هویت
        if (!$userId && !auth()->check()) {
            throw ApiException::unauthenticated();
        }

        $userId = $userId ?? auth()->id();

        // مالک فایل دسترسی دارد
        if ($document->user_id === $userId) {
            return;
        }

        // ادمین با دسترسی مناسب
        $user = \App\Models\User::find($userId);
        if ($user && $user->hasPermission('documents.view_all')) {
            return;
        }

        // اگر documentable دارد، مالک آن دسترسی دارد
        if ($document->documentable && method_exists($document->documentable, 'user_id')) {
            if ($document->documentable->user_id === $userId) {
                return;
            }
        }

        DocumentAccessLog::log($document, 'view', $userId, 'denied', 'عدم دسترسی');

        throw ApiException::forbidden();
    }

    private function canDelete(Document $document): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $userId = auth()->id();

        // مالک فایل
        if ($document->user_id === $userId) {
            return true;
        }

        // ادمین
        return auth()->user()->hasPermission('documents.delete_all');
    }
}
