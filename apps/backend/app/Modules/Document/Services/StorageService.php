<?php

namespace App\Modules\Document\Services;

use App\Modules\Document\Enums\DocumentCategory;
use App\Modules\Document\Enums\DocumentVisibility;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * سرویس مدیریت ذخیره‌سازی فایل‌ها
 * 
 * مطابق بخش ۱۷ سند:
 * - فایل‌های عمومی در storage/public
 * - فایل‌های خصوصی در storage/private
 * - در آینده می‌تواند به S3/MinIO متصل شود
 */
class StorageService
{
    private const PUBLIC_DISK = 'public';
    private const PRIVATE_DISK = 'local'; // در آینده 's3'
    
    private const ALLOWED_EXTENSIONS = [
        // تصاویر
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
        // اسناد
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv',
        // سایر
        'zip', 'rar',
    ];

    private const ALLOWED_MIMES = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain', 'text/csv',
        'application/zip', 'application/x-rar-compressed',
    ];

    private const MAX_SIZE = 20 * 1024 * 1024; // 20 MB
    private const IMAGE_MAX_SIZE = 10 * 1024 * 1024; // 10 MB

    /**
     * اعتبارسنجی فایل قبل از آپلود
     */
    public function validate(UploadedFile $file, DocumentCategory $category): array
    {
        $errors = [];

        // بررسی حجم
        $maxSize = $this->isImageMime($file->getMimeType()) ? self::IMAGE_MAX_SIZE : self::MAX_SIZE;
        if ($file->getSize() > $maxSize) {
            $errors[] = 'حجم فایل بیش از حد مجاز است (حداکثر ' . ($maxSize / 1024 / 1024) . ' مگابایت)';
        }

        // بررسی extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            $errors[] = 'فرمت فایل مجاز نیست: ' . $extension;
        }

        // بررسی mime
        if (!in_array($file->getMimeType(), self::ALLOWED_MIMES)) {
            $errors[] = 'نوع فایل مجاز نیست: ' . $file->getMimeType();
        }

        // بررسی تطابق extension با mime
        if (!$this->extensionMatchesMime($extension, $file->getMimeType())) {
            $errors[] = 'فرمت و نوع فایل مطابقت ندارند';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * ذخیره فایل
     */
    public function store(UploadedFile $file, DocumentVisibility $visibility, DocumentCategory $category): array
    {
        $disk = $visibility === DocumentVisibility::PUBLIC ? self::PUBLIC_DISK : self::PRIVATE_DISK;
        $directory = $this->getDirectoryForCategory($category, $visibility);
        
        // نام امن (hash) برای جلوگیری از افشای اطلاعات
        $storedFilename = Str::random(32) . '.' . strtolower($file->getClientOriginalExtension());
        
        // checksum برای جلوگیری از تکرار
        $checksum = hash_file('sha256', $file->getPathname());
        
        $path = Storage::disk($disk)->putFileAs($directory, $file, $storedFilename);

        return [
            'disk' => $disk,
            'path' => $path,
            'stored_filename' => $storedFilename,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'extension' => strtolower($file->getClientOriginalExtension()),
            'size' => $file->getSize(),
            'checksum' => $checksum,
        ];
    }

    /**
     * حذف فایل از storage
     */
    public function delete(string $path, string $disk): bool
    {
        return Storage::disk($disk)->delete($path);
    }

    /**
     * دریافت محتوای فایل (برای فایل‌های خصوصی)
     */
    public function get(string $path, string $disk): ?string
    {
        if (!Storage::disk($disk)->exists($path)) {
            return null;
        }
        return Storage::disk($disk)->get($path);
    }

    /**
     * بررسی وجود فایل
     */
    public function exists(string $path, string $disk): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    /**
     * دریافت URL عمومی
     */
    public function publicUrl(string $path): string
    {
        return Storage::disk(self::PUBLIC_DISK)->url($path);
    }

    /**
     * دریافت مسیر کامل برای دانلود (فایل‌های خصوصی)
     */
    public function getFullPath(string $path, string $disk): string
    {
        return Storage::disk($disk)->path($path);
    }

    /**
     * بررسی تکراری بودن فایل (از روی checksum)
     */
    public function findDuplicate(string $checksum): ?array
    {
        return \App\Modules\Document\Models\Document::where('checksum', $checksum)
            ->whereNull('deleted_at')
            ->first(['id', 'code', 'uuid'])?->toArray();
    }

    // === Private Helpers ===

    private function getDirectoryForCategory(DocumentCategory $category, DocumentVisibility $visibility): string
    {
        $base = $visibility === DocumentVisibility::PUBLIC ? 'public' : 'private';
        
        return match ($category) {
            DocumentCategory::HOTEL => "{$base}/hotels/" . now()->format('Y/m'),
            DocumentCategory::TOUR => "{$base}/tours/" . now()->format('Y/m'),
            DocumentCategory::ARTICLE => "{$base}/articles/" . now()->format('Y/m'),
            DocumentCategory::VISA, 
            DocumentCategory::PASSPORT, 
            DocumentCategory::ID_CARD,
            DocumentCategory::PHOTO,
            DocumentCategory::BANK_STATEMENT,
            DocumentCategory::INSURANCE => "private/visa-docs/" . now()->format('Y/m'),
            DocumentCategory::INVOICE => "private/invoices/" . now()->format('Y/m'),
            DocumentCategory::VOUCHER => "private/vouchers/" . now()->format('Y/m'),
            default => "{$base}/general/" . now()->format('Y/m'),
        };
    }

    private function isImageMime(string $mime): bool
    {
        return str_starts_with($mime, 'image/');
    }

    private function extensionMatchesMime(string $extension, string $mime): bool
    {
        $map = [
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'webp' => ['image/webp'],
            'svg' => ['image/svg+xml'],
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'zip' => ['application/zip'],
        ];

        if (!isset($map[$extension])) {
            return true; // اگر در مپ نیست، رد نمی‌کنیم
        }

        return in_array($mime, $map[$extension]);
    }
}
