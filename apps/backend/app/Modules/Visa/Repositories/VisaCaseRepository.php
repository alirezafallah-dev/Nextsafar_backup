<?php

namespace App\Modules\Visa\Repositories;

use App\Modules\Visa\Enums\VisaCaseStatus;
use App\Modules\Visa\Models\VisaCase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VisaCaseRepository
{
    /**
     * فیلتر پیشرفته پرونده‌ها (برای پنل ادمین)
     */
    public function filter(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = VisaCase::with(['user:id,name,phone,email', 'visaType:id,title,country_id', 'visaType.country:id,name,flag_emoji', 'assignedAgent:id,name'])
            ->latest();

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // User filter
        if (!empty($filters['user_id'])) {
            $query->forUser((int) $filters['user_id']);
        }

        // Visa type filter
        if (!empty($filters['visa_type_id'])) {
            $query->where('visa_type_id', (int) $filters['visa_type_id']);
        }

        // Country filter
        if (!empty($filters['country_id'])) {
            $query->byCountry((int) $filters['country_id']);
        }

        // Assigned to filter
        if (!empty($filters['assigned_to'])) {
            if ($filters['assigned_to'] === 'unassigned') {
                $query->unassigned();
            } else {
                $query->assignedTo((int) $filters['assigned_to']);
            }
        }

        // Search
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Date range
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Priority filter
        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        // Overdue filter
        if (isset($filters['overdue']) && $filters['overdue']) {
            $query->overdue();
        }

        // Sort
        $sort = $filters['sort'] ?? 'newest';
        $query = match ($sort) {
            'newest' => $query->latest(),
            'oldest' => $query->oldest(),
            'sla' => $query->orderByRaw('CASE WHEN is_overdue = true THEN 0 ELSE 1 END')
                ->orderBy('next_follow_up_at'),
            'priority' => $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')"),
            default => $query->latest(),
        };

        return $query->paginate($perPage);
    }

    /**
     * یافتن با جزئیات کامل
     */
    public function findWithDetails(int $id): ?VisaCase
    {
        return VisaCase::with([
            'user:id,name,phone,email',
            'visaType' => function ($q) {
                $q->with('country:id,name,flag_emoji', 'checklist');
            },
            'passengers',
            'documents' => function ($q) {
                $q->with(['document', 'checklistItem', 'passenger', 'verifier:id,name']);
            },
            'timeline' => function ($q) {
                $q->orderBy('occurred_at', 'desc');
            },
            'notes' => function ($q) {
                $q->orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc');
            },
            'assignedAgent:id,name',
            'order:id,code,status',
            'lead:id,code,full_name',
        ])->find($id);
    }

    /**
     * آمار داشبورد
     */
    public function getStatistics(): array
    {
        $baseQuery = VisaCase::query();

        return [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->active()->count(),
            'terminal' => (clone $baseQuery)->terminal()->count(),
            'successful' => (clone $baseQuery)->successful()->count(),
            'overdue' => (clone $baseQuery)->overdue()->count(),
            'requires_action' => (clone $baseQuery)->requiresAction()->count(),
            'by_status' => (clone $baseQuery)
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'by_country' => (clone $baseQuery)
                ->join('visa_types', 'visa_cases.visa_type_id', '=', 'visa_types.id')
                ->join('countries', 'visa_types.country_id', '=', 'countries.id')
                ->selectRaw('countries.name, countries.flag_emoji, count(*) as count')
                ->groupBy('countries.id', 'countries.name', 'countries.flag_emoji')
                ->get()
                ->toArray(),
            'by_agent' => (clone $baseQuery)
                ->whereNotNull('assigned_to')
                ->join('users', 'visa_cases.assigned_to', '=', 'users.id')
                ->selectRaw('users.name, count(*) as count')
                ->groupBy('users.id', 'users.name')
                ->get()
                ->toArray(),
            'unassigned' => (clone $baseQuery)->unassigned()->count(),
            'today' => (clone $baseQuery)->whereDate('created_at', today())->count(),
            'this_week' => (clone $baseQuery)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'this_month' => (clone $baseQuery)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
            'avg_completion_days' => (clone $baseQuery)
                ->whereNotNull('completed_at')
                ->selectRaw('AVG(EXTRACT(EPOCH FROM (completed_at - created_at)) / 86400) as avg_days')
                ->value('avg_days') ?? 0,
        ];
    }
}
