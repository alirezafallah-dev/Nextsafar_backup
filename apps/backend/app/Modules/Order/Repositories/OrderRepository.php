<?php

namespace App\Modules\Order\Repositories;

use App\Modules\Order\Models\Order;
use App\Modules\Order\Models\OrderItem;
use App\Modules\Order\Models\OrderPassenger;
use App\Modules\Order\Models\OrderStatusHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    /**
     * فیلتر پیشرفته سفارش‌ها (برای پنل ادمین)
     */
    public function filter(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = Order::with(['user:id,name,phone,email', 'assignedTo:id,name', 'lead:id,code,type'])
            ->withCount('items')
            ->withCount('passengers')
            ->latest();

        // Search by code, user name/phone, passenger name/national_id
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Status filter (multiple)
        if (!empty($filters['status'])) {
            $statuses = is_array($filters['status']) ? $filters['status'] : [$filters['status']];
            $query->whereIn('status', $statuses);
        }

        // Type filter
        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        // Source filter
        if (!empty($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        // User filter
        if (!empty($filters['user_id'])) {
            $query->forUser((int) $filters['user_id']);
        }

        // Assigned to filter
        if (!empty($filters['assigned_to'])) {
            if ($filters['assigned_to'] === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->assignedTo((int) $filters['assigned_to']);
            }
        }

        // From lead filter
        if (!empty($filters['from_lead'])) {
            $query->whereNotNull('lead_id');
        }

        // Date range
        if (!empty($filters['date_from']) || !empty($filters['date_to'])) {
            $query->dateRange($filters['date_from'] ?? null, $filters['date_to'] ?? null);
        }

        // Price range
        if (isset($filters['min_amount'])) {
            $query->where('total_amount', '>=', $filters['min_amount']);
        }
        if (isset($filters['max_amount'])) {
            $query->where('total_amount', '<=', $filters['max_amount']);
        }

        // Sort
        $sort = $filters['sort'] ?? 'newest';
        $query = match ($sort) {
            'newest' => $query->latest(),
            'oldest' => $query->oldest(),
            'amount_desc' => $query->orderByDesc('total_amount'),
            'amount_asc' => $query->orderBy('total_amount'),
            'priority' => $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')"),
            default => $query->latest(),
        };

        return $query->paginate($perPage);
    }

    /**
     * یافتن با جزئیات کامل
     */
    public function findWithDetails(int $id): ?Order
    {
        return Order::with([
            'user:id,name,phone,email',
            'lead:id,code,type,full_name,phone',
            'items' => function ($q) {
                $q->orderBy('id');
            },
            'passengers' => function ($q) {
                $q->orderBy('id');
            },
            'statusHistory.changedBy:id,name',
            'creator:id,name',
            'assignedTo:id,name',
        ])->find($id);
    }

    /**
     * یافتن بر اساس code
     */
    public function findByCode(string $code): ?Order
    {
        return Order::where('code', $code)->first();
    }

    /**
     * سفارش‌های کاربر مشخص
     */
    public function getForUser(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return Order::forUser($userId)
            ->with(['items', 'passengers'])
            ->withCount('items')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * سفارش‌های اختصاص داده شده به کاربر (کارشناس)
     */
    public function getAssignedTo(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return Order::assignedTo($userId)
            ->active()
            ->with(['user:id,name,phone', 'items'])
            ->withCount('items')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * ایجاد سفارش
     */
    public function create(array $data): Order
    {
        return Order::create($data);
    }

    /**
     * بروزرسانی سفارش
     */
    public function update(Order $order, array $data): Order
    {
        $order->update($data);
        return $order->fresh();
    }

    /**
     * افزودن آیتم
     */
    public function addItem(Order $order, array $itemData): OrderItem
    {
        $itemData['order_id'] = $order->id;
        $item = OrderItem::create($itemData);

        // محاسبه مجدد مجموع سفارش
        $this->recalculateTotals($order);

        return $item;
    }

    /**
     * بروزرسانی آیتم
     */
    public function updateItem(OrderItem $item, array $data): OrderItem
    {
        $item->update($data);
        $item->recalculateTotal();

        $this->recalculateTotals($item->order);

        return $item->fresh();
    }

    /**
     * حذف آیتم
     */
    public function removeItem(OrderItem $item): bool
    {
        $order = $item->order;

        // حذف مسافران مرتبط
        $item->passengers()->delete();

        $result = $item->delete();

        $this->recalculateTotals($order);

        return $result;
    }

    /**
     * افزودن مسافر
     */
    public function addPassenger(Order $order, array $passengerData): OrderPassenger
    {
        $passengerData['order_id'] = $order->id;
        $passenger = OrderPassenger::create($passengerData);

        // بروزرسانی شمارنده‌ها
        $this->recalculatePassengerCounts($order);

        return $passenger;
    }

    /**
     * حذف مسافر
     */
    public function removePassenger(OrderPassenger $passenger): bool
    {
        $order = $passenger->order;
        $result = $passenger->delete();

        $this->recalculatePassengerCounts($order);

        return $result;
    }

    /**
     * ثبت تاریخچه تغییر وضعیت
     */
    public function recordStatusChange(
        Order $order,
        string $newStatus,
        ?string $reason = null,
        $changedBy = null,
        string $source = OrderStatusHistory::SOURCE_MANUAL,
        ?array $metadata = null
    ): OrderStatusHistory {
        return OrderStatusHistory::record(
            $order,
            $newStatus,
            $reason,
            $changedBy,
            $source,
            $metadata
        );
    }

    /**
     * محاسبه مجدد مبالغ سفارش
     */
    public function recalculateTotals(Order $order): Order
    {
        $subtotal = $order->items()->sum('total_price');
        $discountAmount = $order->discount_amount ?? 0;
        $taxAmount = $order->tax_amount ?? 0;

        $order->update([
            'subtotal' => $subtotal,
            'total_amount' => $subtotal - $discountAmount + $taxAmount,
        ]);

        return $order->fresh();
    }

    /**
     * محاسبه مجدد شمارنده مسافران
     */
    public function recalculatePassengerCounts(Order $order): Order
    {
        $counts = $order->passengers()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        $order->update([
            'adults_count' => $counts['adult'] ?? 0,
            'children_count' => $counts['child'] ?? 0,
            'infants_count' => $counts['infant'] ?? 0,
        ]);

        return $order->fresh();
    }

    /**
     * آمار داشبورد
     */
    public function getStatistics(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $query = Order::query();

        if ($dateFrom) $query->whereDate('created_at', '>=', $dateFrom);
        if ($dateTo) $query->whereDate('created_at', '<=', $dateTo);

        $baseQuery = clone $query;

        return [
            'total_orders' => $query->count(),
            'total_revenue' => (clone $baseQuery)->paid()->sum('total_amount'),
            'pending_payment' => (clone $baseQuery)->pendingPayment()->count(),
            'active' => (clone $baseQuery)->active()->count(),
            'confirmed' => (clone $baseQuery)->byStatus(Order::STATUS_CONFIRMED)->count(),
            'cancelled' => (clone $baseQuery)->byStatus(Order::STATUS_CANCELLED)->count(),
            'refunded' => (clone $baseQuery)->byStatus(Order::STATUS_REFUNDED)->count(),
            'refund_requested' => (clone $baseQuery)->byStatus(Order::STATUS_REFUND_REQUESTED)->count(),

            'by_type' => (clone $baseQuery)
                ->selectRaw('type, count(*) as count, sum(total_amount) as revenue')
                ->groupBy('type')
                ->get()
                ->keyBy('type')
                ->toArray(),

            'by_status' => (clone $baseQuery)
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),

            'by_source' => (clone $baseQuery)
                ->selectRaw('source, count(*) as count')
                ->groupBy('source')
                ->pluck('count', 'source')
                ->toArray(),

            'avg_order_value' => (clone $baseQuery)->paid()->avg('total_amount') ?? 0,
            'total_passengers' => (clone $baseQuery)->paid()->sum(DB::raw('adults_count + children_count + infants_count')),

            'today' => (clone $baseQuery)->whereDate('created_at', today())->count(),
            'this_week' => (clone $baseQuery)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'this_month' => (clone $baseQuery)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
        ];
    }

    /**
     * حذف کامل سفارش (فقط برای draft)
     */
    public function delete(Order $order): bool
    {
        if ($order->status !== Order::STATUS_DRAFT) {
            return false;
        }

        return $order->delete();
    }
}
