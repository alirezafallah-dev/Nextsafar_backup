<?php

namespace App\Modules\Document\Controllers;

use App\Core\ApiResponse;
use App\Exceptions\ApiException;
use App\Modules\Document\Enums\DocumentCategory;
use App\Modules\Document\Models\Document;
use App\Modules\Document\Models\DocumentTempLink;
use App\Modules\Document\Requests\UploadDocumentRequest;
use App\Modules\Document\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DocumentController extends Controller
{
    public function __construct(
        protected DocumentService $documentService,
    ) {}

    // =====================================================================
    // USER ENDPOINTS
    // =====================================================================

    /**
     * POST /api/v1/documents
     * آپلود فایل
     */
    public function store(UploadDocumentRequest $request): JsonResponse
    {
        $document = $this->documentService->upload(
            $request->file('file'),
            $request->getCategory(),
            $request->getVisibility(),
            null,
            $request->input('title'),
            $request->input('description')
        );

        return ApiResponse::created($document->fresh(['accessLogs']), 'فایل با موفقیت آپلود شد.')
            ->toResponse($request);
    }

    /**
     * GET /api/v1/documents
     * لیست فایل‌های کاربر
     */
    public function index(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            throw ApiException::unauthenticated();
        }

        $query = Document::forUser(auth()->id())
            ->with(['documentable'])
            ->latest();

        if ($category = $request->get('category')) {
            $query->ofCategory(DocumentCategory::from($category));
        }

        $documents = $query->paginate($request->get('per_page', 20));

        return ApiResponse::success($documents->items())
            ->withPagination($documents)
            ->toResponse($request);
    }

    /**
     * GET /api/v1/documents/{document}
     * جزئیات فایل
     */
    public function show(Document $document): JsonResponse
    {
        $this->authorizeView($document);
        return ApiResponse::success($document->load(['accessLogs', 'tempLinks']))
            ->toResponse(request());
    }

    /**
     * GET /api/v1/documents/{document}/download
     * دانلود فایل
     */
    public function download(Document $document)
    {
        return $this->documentService->download($document);
    }

    /**
     * POST /api/v1/documents/{document}/temp-link
     * ایجاد لینک زمان‌دار
     */
    public function createTempLink(Request $request, Document $document): JsonResponse
    {
        $this->authorizeView($document);

        $validated = $request->validate([
            'expires_in_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'max_uses' => ['nullable', 'integer', 'min:1', 'max:100'],
            'allowed_user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $link = $this->documentService->createTempLink(
            $document,
            $validated['expires_in_minutes'] ?? 60,
            $validated['max_uses'] ?? 1,
            $validated['allowed_user_id'] ?? null
        );

        return ApiResponse::created([
            'url' => $link->url,
            'token' => $link->token,
            'expires_at' => $link->expires_at,
            'max_uses' => $link->max_uses,
        ], 'لینک زمان‌دار ایجاد شد.')
            ->toResponse($request);
    }

    /**
     * DELETE /api/v1/documents/{document}
     * حذف فایل
     */
    public function destroy(Document $document): JsonResponse
    {
        $this->documentService->delete($document);
        return ApiResponse::noContent()->toResponse(request());
    }

    // =====================================================================
    // PUBLIC ENDPOINTS
    // =====================================================================

    /**
     * GET /api/v1/documents/temp/{token}
     * دسترسی از طریق لینک زمان‌دار (public endpoint)
     */
    public function accessViaTempLink(string $token)
    {
        return $this->documentService->accessViaTempLink($token);
    }

    /**
     * GET /api/v1/documents/public/{uuid}
     * دسترسی به فایل عمومی با UUID (بدون auth)
     */
    public function accessPublic(string $uuid)
    {
        $document = Document::findByUuid($uuid);

        if (!$document || !$document->isPublic()) {
            throw ApiException::notFound('فایل');
        }

        return $this->documentService->download($document);
    }

    // =====================================================================
    // ADMIN ENDPOINTS
    // =====================================================================

    /**
     * GET /api/v1/admin/documents
     * لیست همه فایل‌ها (ادمین)
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = Document::with(['user:id,name', 'uploader:id,name', 'documentable'])
            ->latest();

        if ($category = $request->get('category')) {
            $query->ofCategory(DocumentCategory::from($category));
        }

        if ($visibility = $request->get('visibility')) {
            $query->where('visibility', $visibility);
        }

        if ($scanStatus = $request->get('scan_status')) {
            $query->where('scan_status', $scanStatus);
        }

        if ($userId = $request->get('user_id')) {
            $query->forUser((int) $userId);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'ilike', "%{$search}%")
                    ->orWhere('original_filename', 'ilike', "%{$search}%")
                    ->orWhere('title', 'ilike', "%{$search}%");
            });
        }

        $documents = $query->paginate($request->get('per_page', 50));

        return ApiResponse::success($documents->items())
            ->withPagination($documents)
            ->toResponse($request);
    }

    /**
     * GET /api/v1/admin/documents/statistics
     * آمار فایل‌ها
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total' => Document::count(),
            'total_size' => Document::sum('size'),
            'by_category' => Document::selectRaw('category, count(*) as count, sum(size) as total_size')
                ->groupBy('category')
                ->get()
                ->keyBy('category')
                ->toArray(),
            'by_visibility' => Document::selectRaw('visibility, count(*) as count')
                ->groupBy('visibility')
                ->pluck('count', 'visibility')
                ->toArray(),
            'scan_status' => Document::selectRaw('scan_status, count(*) as count')
                ->groupBy('scan_status')
                ->pluck('count', 'scan_status')
                ->toArray(),
            'pending_scan' => Document::pendingScan()->count(),
            'infected' => Document::where('scan_status', 'infected')->count(),
        ];

        return ApiResponse::success($stats)->toResponse(request());
    }

    /**
     * POST /api/v1/admin/documents/{document}/mark-clean
     * علامت‌گذاری به عنوان سالم
     */
    public function markClean(Document $document): JsonResponse
    {
        $this->documentService->markAsClean($document);
        return ApiResponse::success($document->fresh(), 'فایل سالم علامت‌گذاری شد.')
            ->toResponse(request());
    }

    /**
     * POST /api/v1/admin/documents/{document}/mark-infected
     * علامت‌گذاری به عنوان آلوده
     */
    public function markInfected(Request $request, Document $document): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $this->documentService->markAsInfected($document, $validated['reason']);
        return ApiResponse::success($document->fresh(), 'فایل آلوده علامت‌گذاری شد.')
            ->toResponse(request());
    }

    /**
     * GET /api/v1/admin/documents/{document}/access-logs
     * لاگ دسترسی‌ها
     */
    public function accessLogs(Document $document, Request $request): JsonResponse
    {
        $logs = $document->accessLogs()
            ->with('user:id,name')
            ->latest('accessed_at')
            ->paginate($request->get('per_page', 50));

        return ApiResponse::success($logs->items())
            ->withPagination($logs)
            ->toResponse($request);
    }

    /**
     * POST /api/v1/admin/documents/temp-links/{link}/revoke
     * باطل کردن لینک
     */
    public function revokeTempLink(DocumentTempLink $link): JsonResponse
    {
        $this->documentService->revokeTempLink($link);
        return ApiResponse::success($link, 'لینک باطل شد.')
            ->toResponse(request());
    }

    // =====================================================================
    // Helpers
    // =====================================================================

    private function authorizeView(Document $document): void
    {
        if ($document->isPublic()) {
            return;
        }

        if (!auth()->check()) {
            throw ApiException::unauthenticated();
        }

        $userId = auth()->id();

        if ($document->user_id === $userId) {
            return;
        }

        if (auth()->user()->hasPermission('documents.view_all')) {
            return;
        }

        throw ApiException::forbidden();
    }
}
