<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\RoleHistory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRoles
{
    /**
     * Get all roles for the user
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot('assigned_at', 'assigned_by')
            ->withTimestamps();
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            return $this->roles()->where('slug', $roles)->exists();
        }

        return $this->roles()->whereIn('slug', $roles)->exists();
    }

    /**
     * Check if user has all specified roles
     */
    public function hasAllRoles(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->count() === count($roles);
    }

    /**
     * Check if user has any permission
     */
    public function hasPermission(string $permission): bool
    {
        // Super admin has all permissions
        if ($this->hasRole('super_admin')) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('slug', $permission);
            })
            ->exists();
    }

    /**
     * Check if user has any of the specified permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->hasRole('super_admin')) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissions) {
                $query->whereIn('slug', $permissions);
            })
            ->exists();
    }

    /**
     * Check if user has all specified permissions
     */
    public function hasAllPermissions(array $permissions): bool
    {
        if ($this->hasRole('super_admin')) {
            return true;
        }

        $userPermissions = $this->getAllPermissions()->pluck('slug')->toArray();
        return empty(array_diff($permissions, $userPermissions));
    }

    /**
     * Get all permissions through roles
     */
    public function getAllPermissions()
    {
        return \App\Models\Permission::whereHas('roles', function ($query) {
            $query->whereIn('roles.id', $this->roles()->pluck('roles.id'));
        })->get();
    }

    /**
     * Assign role to user with history logging
     */
    public function assignRole(string|int|Role $role, ?string $reason = null): void
    {
        $roleId = $role instanceof Role ? $role->id : (is_numeric($role) ? $role : Role::where('slug', $role)->value('id'));
        
        if (!$roleId) {
            return;
        }

        if (!$this->roles()->where('roles.id', $roleId)->exists()) {
            $this->roles()->attach($roleId, [
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
            ]);

            RoleHistory::create([
                'user_id' => $this->id,
                'action' => RoleHistory::ACTION_ASSIGNED,
                'new_role_id' => $roleId,
                'changed_by' => auth()->id(),
                'reason' => $reason,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'changed_at' => now(),
            ]);
        }
    }

    /**
     * Remove role from user with history logging
     */
    public function removeRole(string|int|Role $role, ?string $reason = null): void
    {
        $roleId = $role instanceof Role ? $role->id : (is_numeric($role) ? $role : Role::where('slug', $role)->value('id'));
        
        if (!$roleId) {
            return;
        }

        if ($this->roles()->where('roles.id', $roleId)->exists()) {
            $this->roles()->detach($roleId);

            RoleHistory::create([
                'user_id' => $this->id,
                'action' => RoleHistory::ACTION_REMOVED,
                'old_role_id' => $roleId,
                'changed_by' => auth()->id(),
                'reason' => $reason,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'changed_at' => now(),
            ]);
        }
    }

    /**
     * Sync roles (replace all roles)
     */
    public function syncRoles(array $roles, ?string $reason = null): void
    {
        $oldRoles = $this->roles()->pluck('roles.id')->toArray();
        
        $roleIds = [];
        foreach ($roles as $role) {
            if ($role instanceof Role) {
                $roleIds[] = $role->id;
            } elseif (is_numeric($role)) {
                $roleIds[] = $role;
            } else {
                $id = Role::where('slug', $role)->value('id');
                if ($id) $roleIds[] = $id;
            }
        }

        $this->roles()->sync($roleIds);

        // Log changes
        $added = array_diff($roleIds, $oldRoles);
        $removed = array_diff($oldRoles, $roleIds);

        foreach ($added as $roleId) {
            RoleHistory::create([
                'user_id' => $this->id,
                'action' => RoleHistory::ACTION_ASSIGNED,
                'new_role_id' => $roleId,
                'changed_by' => auth()->id(),
                'reason' => $reason,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'changed_at' => now(),
            ]);
        }

        foreach ($removed as $roleId) {
            RoleHistory::create([
                'user_id' => $this->id,
                'action' => RoleHistory::ACTION_REMOVED,
                'old_role_id' => $roleId,
                'changed_by' => auth()->id(),
                'reason' => $reason,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'changed_at' => now(),
            ]);
        }
    }

    /**
     * Get primary role (highest priority)
     */
    public function getPrimaryRole(): ?Role
    {
        return $this->roles()->orderByDesc('priority')->first();
    }

    /**
     * Check if user is admin (has any admin role)
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(['admin', 'super_admin']);
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Check if user requires 2FA based on their roles
     */
    public function requiresTwoFactor(): bool
    {
        return $this->roles()->where('requires_2fa', true)->exists();
    }
}
