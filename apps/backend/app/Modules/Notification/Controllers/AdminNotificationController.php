<?php

namespace App\Modules\Notification\Controllers;

use App\Core\ApiResponse;
use App\Modules\Notification\Enums\NotificationChannel;
use App\Modules\Notification\Enums\NotificationEvent;
use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Models\NotificationTemplate;
use App\Modules\Notification\Repositories\NotificationRepository;
use App\Modules\Notification\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class AdminNotificationController extends Controller
{
    public function __construct(
        protected NotificationRepository $repository,
        protected NotificationService $service
    ) {
        $this->middleware(['auth:sanctum', 'permission:notifications.manage']);
    }

    /**
     * GET /api/v1/admin/notifications
     * لیست همه اعلان‌ها با فیلتر
     */
    public function index(Request $request)
    {
        $notifications = $this->repository->filter(
            filters: $request->only([
                'channel', 'status', 'event_type', 'user_id',
                'date_from', 'date_to', 'search'
            ]),
            perPage: $request->get('per_page', 50)
        );

        return ApiResponse::paginated($notifications);
    }

    /**
     * GET /api/v1/admin/notifications/statistics
     * آمار اعلان‌ها
     */
    public function statistics()
    {
        $stats = $this->repository->getStatistics();

        return ApiResponse::success($stats);
    }

    /**
     * POST /api/v1/admin/notifications/send
     * ارسال دستی اعلان
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'event' => ['required', Rule::in(array_column(NotificationEvent::cases(), 'value'))],
            'user_id' => ['required', 'exists:users,id'],
            'variables' => ['nullable', 'array'],
            'channels' => ['nullable', 'array'],
            'channels.*' => [Rule::in(array_column(NotificationChannel::cases(), 'value'))],
        ]);

        $user = \App\Models\User::findOrFail($validated['user_id']);
        $event = NotificationEvent::from($validated['event']);
        $variables = $validated['variables'] ?? [];

        if (!empty($validated['channels'])) {
            // ارسال به کانال‌های خاص
            $notifications = [];
            foreach ($validated['channels'] as $channelValue) {
                $channel = NotificationChannel::from($channelValue);
                $notifications[] = $this->service->send(
                    channel: $channel,
                    user: $user,
                    event: $event,
                    variables: $variables,
                );
            }
        } else {
            // ارسال به کانال‌های پیش‌فرض
            $notifications = $this->service->sendForEvent(
                event: $event,
                user: $user,
                variables: $variables,
            );
        }

        return ApiResponse::success([
            'message' => count($notifications) . ' notifications sent',
            'notifications' => $notifications,
        ]);
    }

    /**
     * POST /api/v1/admin/notifications/{id}/retry
     * تلاش مجدد برای ارسال اعلان ناموفق
     */
    public function retry(int $id)
    {
        $notification = Notification::findOrFail($id);

        if (!$notification->canRetry()) {
            return ApiResponse::error('This notification cannot be retried', 422);
        }

        $this->service->retry($notification);

        return ApiResponse::success(['message' => 'Notification queued for retry']);
    }

    /**
     * POST /api/v1/admin/notifications/{id}/cancel
     * لغو اعلان
     */
    public function cancel(int $id)
    {
        $notification = Notification::findOrFail($id);

        if ($notification->isTerminal()) {
            return ApiResponse::error('Cannot cancel terminal notification', 422);
        }

        $notification->markAsCancelled();

        return ApiResponse::success(['message' => 'Notification cancelled']);
    }

    /**
     * GET /api/v1/admin/notifications/templates
     * لیست قالب‌ها
     */
    public function templates(Request $request)
    {
        $templates = NotificationTemplate::query()
            ->when($request->event_type, fn($q) => $q->where('event_type', $request->event_type))
            ->when($request->channel, fn($q) => $q->where('channel', $request->channel))
            ->orderBy('event_type')
            ->orderBy('channel')
            ->get();

        return ApiResponse::success($templates);
    }

    /**
     * PUT /api/v1/admin/notifications/templates/{id}
     * ویرایش قالب
     */
    public function updateTemplate(Request $request, int $id)
    {
        $template = NotificationTemplate::findOrFail($id);

        if ($template->is_system) {
            return ApiResponse::error('System templates cannot be modified', 403);
        }

        $validated = $request->validate([
            'subject' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'is_active' => ['boolean'],
        ]);

        $template->update($validated);

        return ApiResponse::success($template);
    }
}
