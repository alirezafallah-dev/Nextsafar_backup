<?php

namespace App\Modules\Notification\Controllers;

use App\Core\ApiResponse;
use App\Modules\Notification\Enums\NotificationChannel;
use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Repositories\NotificationRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserNotificationController extends Controller
{
    public function __construct(
        protected NotificationRepository $repository
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * GET /api/v1/notifications
     * لیست اعلان‌های کاربر
     */
    public function index(Request $request)
    {
        $notifications = $this->repository->getUserNotifications(
            userId: $request->user()->id,
            filters: $request->only(['channel', 'is_read', 'date_from', 'date_to']),
            perPage: $request->get('per_page', 20)
        );

        return ApiResponse::paginated($notifications);
    }

    /**
     * GET /api/v1/notifications/unread-count
     * تعداد اعلان‌های خوانده نشده
     */
    public function unreadCount(Request $request)
    {
        $count = $this->repository->getUnreadCount($request->user()->id);

        return ApiResponse::success(['unread_count' => $count]);
    }

    /**
     * POST /api/v1/notifications/{id}/mark-read
     * علامت‌گذاری یک اعلان به عنوان خوانده شده
     */
    public function markAsRead(Request $request, int $id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->where('channel', NotificationChannel::INTERNAL)
            ->firstOrFail();

        $notification->markAsRead();

        return ApiResponse::success(['message' => 'Notification marked as read']);
    }

    /**
     * POST /api/v1/notifications/mark-all-read
     * علامت‌گذاری همه اعلان‌ها به عنوان خوانده شده
     */
    public function markAllAsRead(Request $request)
    {
        $count = $this->repository->markAllAsRead($request->user()->id);

        return ApiResponse::success([
            'message' => "{$count} notifications marked as read",
            'marked_count' => $count,
        ]);
    }

    /**
     * DELETE /api/v1/notifications/{id}
     * حذف اعلان
     */
    public function destroy(Request $request, int $id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $notification->delete();

        return ApiResponse::success(['message' => 'Notification deleted']);
    }
}
