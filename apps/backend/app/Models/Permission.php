<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'group',
        'description',
        'is_sensitive',
    ];

    protected $casts = [
        'is_sensitive' => 'boolean',
    ];

    // === Relationships ===

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
            ->withTimestamps();
    }

    // === Scopes ===

    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function scopeSensitive($query)
    {
        return $query->where('is_sensitive', true);
    }

    // === Constants: Permission Groups ===
    public const GROUP_USERS = 'users';
    public const GROUP_ROLES = 'roles';
    public const GROUP_HOTELS = 'hotels';
    public const GROUP_TOURS = 'tours';
    public const GROUP_BOOKINGS = 'bookings';
    public const GROUP_PAYMENTS = 'payments';
    public const GROUP_REFUNDS = 'refunds';
    public const GROUP_VISA = 'visa';
    public const GROUP_LEADS = 'leads';
    public const GROUP_REPORTS = 'reports';
    public const GROUP_SETTINGS = 'settings';
    public const GROUP_SYNC = 'sync';
    public const GROUP_CONTENT = 'content';

    // === Methods ===

    public static function allGroups(): array
    {
        return [
            self::GROUP_USERS,
            self::GROUP_ROLES,
            self::GROUP_HOTELS,
            self::GROUP_TOURS,
            self::GROUP_BOOKINGS,
            self::GROUP_PAYMENTS,
            self::GROUP_REFUNDS,
            self::GROUP_VISA,
            self::GROUP_LEADS,
            self::GROUP_REPORTS,
            self::GROUP_SETTINGS,
            self::GROUP_SYNC,
            self::GROUP_CONTENT,
        ];
    }
}
