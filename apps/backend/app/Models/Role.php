<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_system',
        'priority',
        'requires_2fa',
        'is_active',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'requires_2fa' => 'boolean',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    // === Relationships ===

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles')
            ->withPivot('assigned_at', 'assigned_by')
            ->withTimestamps();
    }

    // === Scopes ===

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    // === Methods ===

    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }

    public function hasAnyPermission(array $permissionSlugs): bool
    {
        return $this->permissions()->whereIn('slug', $permissionSlugs)->exists();
    }

    public function hasAllPermissions(array $permissionSlugs): bool
    {
        return $this->permissions()->whereIn('slug', $permissionSlugs)->count() === count($permissionSlugs);
    }

    public function grantPermission(int|string $permission): void
    {
        $permissionId = is_numeric($permission) 
            ? $permission 
            : Permission::where('slug', $permission)->value('id');
        
        if ($permissionId && !$this->hasPermission($permission)) {
            $this->permissions()->attach($permissionId);
        }
    }

    public function revokePermission(int|string $permission): void
    {
        $permissionId = is_numeric($permission) 
            ? $permission 
            : Permission::where('slug', $permission)->value('id');
        
        if ($permissionId) {
            $this->permissions()->detach($permissionId);
        }
    }

    public function syncPermissions(array $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);
    }

    public function isSuperAdmin(): bool
    {
        return $this->slug === 'super_admin';
    }

    public function isHigherThan(Role $other): bool
    {
        return $this->priority > $other->priority;
    }
}
