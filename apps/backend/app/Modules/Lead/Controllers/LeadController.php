<?php

namespace App\Modules\Lead\Controllers;

use App\Core\ApiResponse;
use App\Exceptions\ApiException;
use App\Modules\Lead\Models\Lead;
use App\Modules\Lead\Repositories\LeadRepository;
use App\Modules\Lead\Services\LeadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LeadController extends Controller
{
    public function __construct(
        protected LeadService $leadService,
        protected LeadRepository $repository,
    ) {}

    /**
     * POST /api/v1/leads
     * Public endpoint: Create lead from form (no auth required)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:' . implode(',', Lead::ALL_TYPES)],
            'full_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'national_id' => ['nullable', 'string', 'max:20'],
            'message' => ['nullable', 'string', 'max:5000'],
            'tour_id' => ['nullable', 'integer', 'exists:tours,id'],
            'hotel_id' => ['nullable', 'integer', 'exists:hotels,id'],
            'destination' => ['nullable', 'string', 'max:200'],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today'],
            'return_date' => ['nullable', 'date', 'after_or_equal:preferred_date'],
            'adults' => ['nullable', 'integer', 'min:1', 'max:20'],
            'children' => ['nullable', 'integer', 'min:0', 'max:20'],
            'infants' => ['nullable', 'integer', 'min:0', 'max:20'],
            'budget' => ['nullable', 'integer', 'min:0'],
            'preferred_contact_time' => ['nullable', 'string', 'max:100'],
            'source' => ['nullable', 'string', 'max:50'],
            'utm_source' => ['nullable', 'string', 'max:100'],
            'utm_medium' => ['nullable', 'string', 'max:100'],
            'utm_campaign' => ['nullable', 'string', 'max:100'],
            'landing_page' => ['nullable', 'url', 'max:500'],
        ]);

        // Set IP and user agent
        $validated['ip_address'] = $request->ip();
        $validated['user_agent'] = substr($request->userAgent() ?? '', 0, 500);

        // User ID if logged in
        if (auth()->check()) {
            $validated['user_id'] = auth()->id();
        }

        $lead = $this->leadService->createFromPublicForm($validated);

        $message = $lead->is_duplicate
            ? 'درخواست شما قبلاً ثبت شده است. کارشناسان ما به زودی با شما تماس خواهند گرفت.'
            : 'درخواست شما با موفقیت ثبت شد. کارشناسان ما به زودی با شما تماس خواهند گرفت.';

        return ApiResponse::created([
            'code' => $lead->code,
            'is_duplicate' => $lead->is_duplicate,
        ], $message)->toResponse($request);
    }

    /**
     * GET /api/v1/admin/leads
     * Admin: List leads with filters
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'in:' . implode(',', Lead::ALL_TYPES)],
            'status' => ['nullable'],
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
            'assigned_to' => ['nullable'],
            'source' => ['nullable', 'string', 'max:50'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'due_follow_up' => ['nullable', 'boolean'],
            'open_only' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'in:newest,oldest,priority,follow_up'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $leads = $this->repository->filter($validated, $validated['per_page'] ?? 20);

        return ApiResponse::success($leads->items())
            ->withPagination($leads)
            ->withMeta([
                'statistics' => $this->repository->getStatistics(),
            ])
            ->toResponse($request);
    }

    /**
     * GET /api/v1/admin/leads/{lead}
     * Admin: Get lead details
     */
    public function show(Lead $lead): JsonResponse
    {
        $lead = $this->repository->findWithDetails($lead->id);
        return ApiResponse::success($lead)->toResponse(request());
    }

    /**
     * PUT /api/v1/admin/leads/{lead}
     * Admin: Update lead
     */
    public function update(Request $request, Lead $lead): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => ['sometimes', 'string', 'max:150'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'message' => ['nullable', 'string', 'max:5000'],
            'tour_id' => ['nullable', 'integer', 'exists:tours,id'],
            'hotel_id' => ['nullable', 'integer', 'exists:hotels,id'],
            'destination' => ['nullable', 'string', 'max:200'],
            'preferred_date' => ['nullable', 'date'],
            'return_date' => ['nullable', 'date'],
            'adults' => ['nullable', 'integer', 'min:1'],
            'children' => ['nullable', 'integer', 'min:0'],
            'infants' => ['nullable', 'integer', 'min:0'],
            'budget' => ['nullable', 'integer', 'min:0'],
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
        ]);

        $lead = $this->leadService->update($lead, $validated);
        return ApiResponse::success($lead, 'درخواست بروزرسانی شد.')->toResponse($request);
    }

    /**
     * DELETE /api/v1/admin/leads/{lead}
     * Admin: Delete lead
     */
    public function destroy(Lead $lead): JsonResponse
    {
        $this->leadService->delete($lead);
        return ApiResponse::noContent()->toResponse(request());
    }

    /**
     * POST /api/v1/admin/leads/{lead}/status
     * Admin: Change lead status
     */
    public function changeStatus(Request $request, Lead $lead): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', Lead::ALL_STATUSES)],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $lead = $this->leadService->changeStatus(
            $lead,
            $validated['status'],
            $validated['reason'] ?? null
        );

        return ApiResponse::success($lead, 'وضعیت درخواست تغییر کرد.')->toResponse($request);
    }

    /**
     * POST /api/v1/admin/leads/{lead}/assign
     * Admin: Assign lead to user
     */
    public function assign(Request $request, Lead $lead): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $lead = $this->leadService->assignTo(
            $lead,
            $validated['user_id'],
            $validated['reason'] ?? null
        );

        return ApiResponse::success($lead, 'درخواست اختصاص داده شد.')->toResponse($request);
    }

    /**
     * POST /api/v1/admin/leads/{lead}/contact
     * Admin: Record contact with lead
     */
    public function recordContact(Request $request, Lead $lead): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:call,email,sms,meeting'],
            'content' => ['nullable', 'string', 'max:5000'],
        ]);

        $note = $this->leadService->recordContact(
            $lead,
            $validated['type'],
            $validated['content'] ?? null
        );

        return ApiResponse::success($note, 'تماس ثبت شد.')->toResponse($request);
    }

    /**
     * POST /api/v1/admin/leads/{lead}/notes
     * Admin: Add note
     */
    public function addNote(Request $request, Lead $lead): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['nullable', 'in:note,task,call,email,sms,meeting'],
            'content' => ['required', 'string', 'max:5000'],
            'visibility' => ['nullable', 'in:internal,public'],
            'is_pinned' => ['nullable', 'boolean'],
        ]);

        $note = $this->leadService->addNote($lead, $validated);
        return ApiResponse::created($note, 'یادداشت اضافه شد.')->toResponse($request);
    }

    /**
     * POST /api/v1/admin/leads/{lead}/follow-up
     * Admin: Schedule follow-up
     */
    public function scheduleFollowUp(Request $request, Lead $lead): JsonResponse
    {
        $validated = $request->validate([
            'next_follow_up_at' => ['required', 'date', 'after:now'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $lead = $this->leadService->scheduleFollowUp(
            $lead,
            $validated['next_follow_up_at'],
            $validated['note'] ?? null
        );

        return ApiResponse::success($lead, 'زمان پیگیری بعدی تنظیم شد.')->toResponse($request);
    }

    /**
     * GET /api/v1/admin/leads/my
     * Admin: Get my assigned leads
     */
    public function myLeads(Request $request): JsonResponse
    {
        $leads = $this->repository->getAssignedTo(auth()->id(), $request->get('per_page', 20));

        return ApiResponse::success($leads->items())
            ->withPagination($leads)
            ->toResponse($request);
    }

    /**
     * GET /api/v1/admin/leads/due-follow-up
     * Admin: Get leads due for follow-up
     */
    public function dueFollowUp(): JsonResponse
    {
        $leads = $this->repository->getDueForFollowUp(auth()->id());
        return ApiResponse::success($leads)->toResponse(request());
    }

    /**
     * GET /api/v1/admin/leads/statistics
     * Admin: Get dashboard statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = $this->repository->getStatistics();
        return ApiResponse::success($stats)->toResponse(request());
    }

    /**
     * POST /api/v1/admin/leads/{lead}/convert
     * Admin: Convert to booking (placeholder)
     */
    public function convertToBooking(Lead $lead): JsonResponse
    {
        $result = $this->leadService->convertToBooking($lead, []);
        return ApiResponse::success($result, $result['message'])->toResponse(request());
    }
}
