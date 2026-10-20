<?php

namespace App\Modules\Lead\Repositories;

use App\Modules\Lead\Models\Lead;
use App\Modules\Lead\Models\LeadNote;
use App\Modules\Lead\Models\LeadStatusHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class LeadRepository
{
    /**
     * Get leads with advanced filtering (admin panel)
     */
    public function filter(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = Lead::with(['tour:id,title', 'hotel:id,name', 'assignedTo:id,name'])
            ->notDuplicate()
            ->latest();

        // Search
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Type filter
        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        // Status filter (multiple allowed)
        if (!empty($filters['status'])) {
            $statuses = is_array($filters['status']) ? $filters['status'] : [$filters['status']];
            $query->whereIn('status', $statuses);
        }

        // Priority filter
        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        // Assigned filter
        if (!empty($filters['assigned_to'])) {
            if ($filters['assigned_to'] === 'unassigned') {
                $query->unassigned();
            } else {
                $query->assignedTo((int) $filters['assigned_to']);
            }
        }

        // Source filter
        if (!empty($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        // Date range filter
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Due for follow-up
        if (!empty($filters['due_follow_up'])) {
            $query->dueForFollowUp();
        }

        // Only open leads
        if (!empty($filters['open_only'])) {
            $query->open();
        }

        // Sort
        $sort = $filters['sort'] ?? 'newest';
        $query = match ($sort) {
            'newest' => $query->latest(),
            'oldest' => $query->oldest(),
            'priority' => $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')"),
            'follow_up' => $query->orderBy('next_follow_up_at'),
            default => $query->latest(),
        };

        return $query->paginate($perPage);
    }

    /**
     * Get a lead with all details
     */
    public function findWithDetails(int $id): ?Lead
    {
        return Lead::with([
            'tour:id,title,code,destination_country',
            'hotel:id,name,city,country',
            'user:id,name,phone,email',
            'assignedTo:id,name',
            'statusHistory.changedBy:id,name',
            'notes.author:id,name',
            'attachments',
        ])->find($id);
    }

    /**
     * Get leads assigned to specific user
     */
    public function getAssignedTo(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return Lead::assignedTo($userId)
            ->open()
            ->with(['tour:id,title', 'hotel:id,name'])
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get leads due for follow-up
     */
    public function getDueForFollowUp(int $userId = null): Collection
    {
        $query = Lead::dueForFollowUp()
            ->with(['tour:id,title', 'hotel:id,name'])
            ->orderBy('next_follow_up_at');

        if ($userId) {
            $query->assignedTo($userId);
        }

        return $query->limit(50)->get();
    }

    /**
     * Get new unassigned leads
     */
    public function getNewUnassigned(int $limit = 50): Collection
    {
        return Lead::new()
            ->unassigned()
            ->with(['tour:id,title', 'hotel:id,name'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Create a lead
     */
    public function create(array $data): Lead
    {
        return Lead::create($data);
    }

    /**
     * Update a lead
     */
    public function update(Lead $lead, array $data): Lead
    {
        $lead->update($data);
        return $lead->fresh();
    }

    /**
     * Soft delete a lead
     */
    public function delete(Lead $lead): bool
    {
        return $lead->delete();
    }

    /**
     * Add note to lead
     */
    public function addNote(Lead $lead, array $data): LeadNote
    {
        return $lead->notes()->create($data);
    }

    /**
     * Add status history
     */
    public function addStatusHistory(Lead $lead, string $newStatus, ?string $reason = null, ?int $changedBy = null): LeadStatusHistory
    {
        return LeadStatusHistory::create([
            'lead_id' => $lead->id,
            'old_status' => $lead->getOriginal('status') ?? $lead->status,
            'new_status' => $newStatus,
            'reason' => $reason,
            'changed_by' => $changedBy,
            'changed_by_name' => $changedBy ? \App\Models\User::find($changedBy)?->name : 'System',
            'ip_address' => request()?->ip(),
            'changed_at' => now(),
        ]);
    }

    /**
     * Get dashboard statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => Lead::notDuplicate()->count(),
            'new' => Lead::new()->notDuplicate()->count(),
            'open' => Lead::open()->notDuplicate()->count(),
            'converted' => Lead::byStatus(Lead::STATUS_CONVERTED)->notDuplicate()->count(),
            'cancelled' => Lead::byStatus(Lead::STATUS_CANCELLED)->notDuplicate()->count(),
            'duplicate' => Lead::where('is_duplicate', true)->count(),
            'due_follow_up' => Lead::dueForFollowUp()->count(),
            'unassigned' => Lead::unassigned()->open()->count(),
            'today' => Lead::today()->notDuplicate()->count(),
            'this_week' => Lead::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->notDuplicate()->count(),
            'this_month' => Lead::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->notDuplicate()->count(),
            'by_type' => Lead::notDuplicate()
                ->selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'by_status' => Lead::notDuplicate()
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'by_source' => Lead::notDuplicate()
                ->selectRaw('source, count(*) as count')
                ->groupBy('source')
                ->pluck('count', 'source')
                ->toArray(),
            'conversion_rate' => $this->calculateConversionRate(),
        ];
    }

    /**
     * Calculate conversion rate
     */
    private function calculateConversionRate(): float
    {
        $total = Lead::notDuplicate()->count();
        $converted = Lead::byStatus(Lead::STATUS_CONVERTED)->notDuplicate()->count();

        if ($total === 0) return 0;

        return round(($converted / $total) * 100, 2);
    }

    /**
     * Get leads by tour
     */
    public function getByTour(int $tourId): Collection
    {
        return Lead::where('tour_id', $tourId)
            ->notDuplicate()
            ->with(['assignedTo:id,name'])
            ->latest()
            ->get();
    }

    /**
     * Get leads by hotel
     */
    public function getByHotel(int $hotelId): Collection
    {
        return Lead::where('hotel_id', $hotelId)
            ->notDuplicate()
            ->with(['assignedTo:id,name'])
            ->latest()
            ->get();
    }

    /**
     * Mark as duplicate
     */
    public function markAsDuplicate(Lead $lead, int $duplicateOfId): Lead
    {
        $lead->update([
            'is_duplicate' => true,
            'duplicate_of' => $duplicateOfId,
            'status' => Lead::STATUS_DUPLICATE,
        ]);

        $this->addStatusHistory($lead, Lead::STATUS_DUPLICATE, "تکراری از لید #{$duplicateOfId}");

        return $lead->fresh();
    }
}
