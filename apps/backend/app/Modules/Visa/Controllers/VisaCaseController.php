<?php

namespace App\Modules\Visa\Controllers;

use App\Core\ApiResponse;
use App\Exceptions\ApiException;
use App\Modules\Document\Models\Document;
use App\Modules\Lead\Models\Lead;
use App\Modules\Visa\Enums\VisaCaseStatus;
use App\Modules\Visa\Models\VisaCase;
use App\Modules\Visa\Models\VisaCaseDocument;
use App\Modules\Visa\Repositories\VisaCaseRepository;
use App\Modules\Visa\Requests\AddNoteRequest;
use App\Modules\Visa\Requests\AddPassengerRequest;
use App\Modules\Visa\Requests\AssignAgentRequest;
use App\Modules\Visa\Requests\BookAppointmentRequest;
use App\Modules\Visa\Requests\ChangeStatusRequest;
use App\Modules\Visa\Requests\CreateVisaCaseRequest;
use App\Modules\Visa\Requests\FilterVisaCasesRequest;
use App\Modules\Visa\Requests\UploadDocumentRequest;
use App\Modules\Visa\Requests\VerifyDocumentRequest;
use App\Modules\Visa\Services\VisaCaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VisaCaseController extends Controller
{
    public function __construct(
        protected VisaCaseService $visaService,
        protected VisaCaseRepository $repository,
    ) {}

    // =====================================================================
    // USER ENDPOINTS
    // =====================================================================

    /**
     * POST /api/v1/visa-cases
     * ایجاد پرونده ویزا توسط کاربر
     */
    public function store(CreateVisaCaseRequest $request): JsonResponse
    {
        if (!auth()->check()) {
            throw ApiException::unauthenticated();
        }

        $case = $this->visaService->createCase(
            userId: auth()->id(),
            visaTypeId: (int) $request->validated('visa_type_id'),
            data: $request->validated(),
        );

        return ApiResponse::created($case, 'پرونده ویزا با موفقیت ایجاد شد.')
            ->toResponse($request);
    }

    /**
     * GET /api/v1/visa-cases
     * لیست پرونده‌های کاربر
     */
    public function index(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            throw ApiException::unauthenticated();
        }

        $cases = VisaCase::forUser(auth()->id())
            ->with(['visaType.country:id,name,flag_emoji'])
            ->latest()
            ->paginate($request->get('per_page', 20));

        return ApiResponse::success($cases->items())
            ->withPagination($cases)
            ->toResponse($request);
    }

    /**
     * GET /api/v1/visa-cases/{case}
     * جزئیات پرونده (فقط برای مالک)
     */
    public function show(VisaCase $case): JsonResponse
    {
        $this->authorizeView($case);

        $case = $this->repository->findWithDetails($case->id);

        return ApiResponse::success($case)->toResponse(request());
    }

    /**
     * POST /api/v1/visa-cases/{case}/passengers
     * افزودن مسافر
     */
    public function addPassenger(AddPassengerRequest $request, VisaCase $case): JsonResponse
    {
        $this->authorizeEdit($case);

        $passenger = $this->visaService->addPassenger($case, $request->validated());

        return ApiResponse::created($passenger, 'مسافر به پرونده اضافه شد.')
            ->toResponse($request);
    }

    /**
     * PUT /api/v1/visa-cases/{case}/passengers/{passenger}
     * ویرایش مسافر
     */
    public function updatePassenger(Request $request, VisaCase $case, $passengerId): JsonResponse
    {
        $this->authorizeEdit($case);

        $passenger = $case->passengers()->findOrFail($passengerId);
        $passenger->update($request->validate([
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'first_name_en' => ['nullable', 'string', 'max:100'],
            'last_name_en' => ['nullable', 'string', 'max:100'],
            'national_id' => ['nullable', 'string', 'max:20'],
            'passport_number' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
        ]));

        return ApiResponse::success($passenger->fresh(), 'مسافر ویرایش شد.')
            ->toResponse($request);
    }

    /**
     * POST /api/v1/visa-cases/{case}/documents
     * اتصال مدرک به پرونده
     */
    public function attachDocument(UploadDocumentRequest $request, VisaCase $case): JsonResponse
    {
        $this->authorizeEdit($case);

        $document = Document::findOrFail($request->validated('document_id'));

        $caseDoc = $this->visaService->attachDocument(
            $case,
            $document,
            $request->validated('checklist_item_id'),
            $request->validated('passenger_id'),
            $request->validated('is_original') ?? true,
            $request->validated('needs_translation') ?? false,
        );

        return ApiResponse::created($caseDoc, 'مدرک به پرونده اضافه شد.')
            ->toResponse($request);
    }

    /**
     * GET /api/v1/visa-cases/{case}/checklist
     * وضعیت چک‌لیست پرونده
     */
    public function checklist(VisaCase $case): JsonResponse
    {
        $this->authorizeView($case);

        $status = $this->visaService->getChecklistStatus($case);

        return ApiResponse::success($status)->toResponse(request());
    }

    /**
     * POST /api/v1/visa-cases/{case}/notes
     * افزودن یادداشت عمومی توسط کاربر
     */
    public function addNote(AddNoteRequest $request, VisaCase $case): JsonResponse
    {
        $this->authorizeEdit($case);

        // کاربر فقط می‌تواند یادداشت عمومی اضافه کند
        $note = $this->visaService->addNote(
            $case,
            $request->validated('content'),
            $request->validated('type') ?? 'general',
            'public', // همیشه public برای کاربر
            auth()->user(),
            $request->validated('parent_id'),
        );

        return ApiResponse::created($note, 'یادداشت اضافه شد.')
            ->toResponse($request);
    }

    // =====================================================================
    // ADMIN ENDPOINTS
    // =====================================================================

    /**
     * GET /api/v1/admin/visa-cases
     * لیست همه پرونده‌ها با فیلتر
     */
    public function adminIndex(FilterVisaCasesRequest $request): JsonResponse
    {
        $cases = $this->repository->filter(
            $request->validated(),
            $request->get('per_page', 20)
        );

        return ApiResponse::success($cases->items())
            ->withPagination($cases)
            ->withMeta([
                'statistics' => $this->repository->getStatistics(),
            ])
            ->toResponse($request);
    }

    /**
     * GET /api/v1/admin/visa-cases/statistics
     * آمار داشبورد
     */
    public function statistics(): JsonResponse
    {
        $stats = $this->repository->getStatistics();
        return ApiResponse::success($stats)->toResponse(request());
    }

    /**
     * GET /api/v1/admin/visa-cases/{case}
     * جزئیات کامل پرونده (ادمین)
     */
    public function adminShow(VisaCase $case): JsonResponse
    {
        $case = $this->repository->findWithDetails($case->id);
        return ApiResponse::success($case)->toResponse(request());
    }

    /**
     * POST /api/v1/admin/visa-cases/{case}/status
     * تغییر وضعیت پرونده
     */
    public function changeStatus(ChangeStatusRequest $request, VisaCase $case): JsonResponse
    {
        $newStatus = VisaCaseStatus::from($request->validated('status'));

        $case = $this->visaService->changeStatus(
            $case,
            $newStatus,
            $request->validated('reason'),
            auth()->user(),
        );

        return ApiResponse::success($case, 'وضعیت پرونده تغییر کرد.')
            ->toResponse($request);
    }

    /**
     * POST /api/v1/admin/visa-cases/{case}/assign
     * اختصاص به کارشناس
     */
    public function assign(AssignAgentRequest $request, VisaCase $case): JsonResponse
    {
        $agent = \App\Models\User::findOrFail($request->validated('agent_id'));

        $case = $this->visaService->assignToAgent($case, $agent, auth()->user());

        return ApiResponse::success($case, 'پرونده به کارشناس اختصاص یافت.')
            ->toResponse($request);
    }

    /**
     * POST /api/v1/admin/visa-cases/{case}/documents/{doc}/verify
     * تأیید/رد مدرک
     */
    public function verifyDocument(VerifyDocumentRequest $request, VisaCase $case, VisaCaseDocument $doc): JsonResponse
    {
        $doc = $this->visaService->verifyDocument(
            $doc,
            $request->validated('status'),
            $request->validated('notes'),
            auth()->user(),
        );

        return ApiResponse::success($doc, 'مدرک بررسی شد.')
            ->toResponse($request);
    }

    /**
     * POST /api/v1/admin/visa-cases/{case}/appointment
     * تنظیم وقت سفارت
     */
    public function bookAppointment(BookAppointmentRequest $request, VisaCase $case): JsonResponse
    {
        $case = $this->visaService->bookAppointment(
            $case,
            $request->validated('datetime'),
            $request->validated('location'),
            $request->validated('reference'),
            auth()->user(),
        );

        return ApiResponse::success($case, 'وقت سفارت تنظیم شد.')
            ->toResponse($request);
    }

    /**
     * POST /api/v1/admin/visa-cases/{case}/notes
     * افزودن یادداشت داخلی (ادمین)
     */
    public function adminAddNote(AddNoteRequest $request, VisaCase $case): JsonResponse
    {
        $note = $this->visaService->addNote(
            $case,
            $request->validated('content'),
            $request->validated('type') ?? 'general',
            $request->validated('visibility') ?? 'internal',
            auth()->user(),
            $request->validated('parent_id'),
        );

        return ApiResponse::created($note, 'یادداشت اضافه شد.')
            ->toResponse($request);
    }

    /**
     * GET /api/v1/admin/visa-cases/{case}/timeline
     * تایم‌لاین کامل پرونده
     */
    public function timeline(VisaCase $case): JsonResponse
    {
        $timeline = $case->timeline()
            ->with('user:id,name')
            ->orderBy('occurred_at', 'desc')
            ->paginate(50);

        return ApiResponse::success($timeline->items())
            ->withPagination($timeline)
            ->toResponse(request());
    }

    /**
     * POST /api/v1/admin/visa-cases/from-lead/{lead}
     * تبدیل لید به پرونده ویزا
     */
    public function createFromLead(Request $request, Lead $lead): JsonResponse
    {
        $validated = $request->validate([
            'visa_type_id' => ['required', 'integer', 'exists:visa_types,id'],
        ]);

        $case = $this->visaService->createFromLead(
            $lead,
            (int) $validated['visa_type_id'],
        );

        return ApiResponse::created($case, 'پرونده ویزا از لید ایجاد شد.')
            ->toResponse($request);
    }

    // =====================================================================
    // Helpers
    // =====================================================================

    private function authorizeView(VisaCase $case): void
    {
        if (!auth()->check()) {
            throw ApiException::unauthenticated();
        }

        if ($case->user_id === auth()->id()) {
            return;
        }

        if (auth()->user()->hasPermission('visa.view')) {
            return;
        }

        throw ApiException::forbidden();
    }

    private function authorizeEdit(VisaCase $case): void
    {
        if (!auth()->check()) {
            throw ApiException::unauthenticated();
        }

        // اگر پرونده در وضعیت پایانی است، اجازه ویرایش نده
        if ($case->isTerminal()) {
            throw new ApiException(
                errorCode: \App\Core\ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'پرونده در وضعیت پایانی است و قابل ویرایش نیست.',
                httpStatus: 422,
            );
        }

        if ($case->user_id === auth()->id()) {
            return;
        }

        if (auth()->user()->hasPermission('visa.manage')) {
            return;
        }

        throw ApiException::forbidden();
    }
}
