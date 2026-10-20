<?php

namespace App\Modules\Auth\Services;

use App\Core\Logging\AppLogger;
use App\Exceptions\ApiException;
use App\Core\ApiErrorCode;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RoleService
{
    /**
     * Create a new role
     */
    public function createRole(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'is_system' => $data['is_system'] ?? false,
                'priority' => $data['priority'] ?? 0,
                'requires_2fa' => $data['requires_2fa'] ?? false,
            ]);

            if (!empty($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            AppLogger::admin()->info('role.created', [
                'role_id' => $role->id,
                'slug' => $role->slug,
            ]);

            return $role;
        });
    }

    /**
     * Update an existing role
     */
    public function updateRole(Role $role, array $data): Role
    {
        if ($role->is_system && isset($data['slug'])) {
            throw new ApiException(
                errorCode: ApiErrorCode::FORBIDDEN,
                userMessage: 'نقش‌های سیستمی قابل تغییر شناسه نیستند.',
                httpStatus: 403,
            );
        }

        return DB::transaction(function () use ($role, $data) {
            $role->update(array_filter($data, fn($v) => $v !== null));

            if (isset($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            AppLogger::admin()->info('role.updated', [
                'role_id' => $role->id,
                'changes' => $data,
            ]);

            return $role->fresh();
        });
    }

    /**
     * Delete a role (soft check for system roles)
     */
    public function deleteRole(Role $role): bool
    {
        if ($role->is_system) {
            throw new ApiException(
                errorCode: ApiErrorCode::FORBIDDEN,
                userMessage: 'نقش‌های سیستمی قابل حذف نیستند.',
                httpStatus: 403,
            );
        }

        if ($role->users()->exists()) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'این نقش به کاربران اختصاص داده شده و قابل حذف نیست.',
                httpStatus: 422,
            );
        }

        return $role->delete();
    }

    /**
     * Assign role to user with validation
     */
    public function assignRoleToUser(User $user, Role $role, ?User $assignedBy = null, ?string $reason = null): void
    {
        // Check role hierarchy (lower priority cannot assign higher priority)
        if ($assignedBy && !$assignedBy->isSuperAdmin()) {
            $assignerRole = $assignedBy->getPrimaryRole();
            if ($assignerRole && !$assignerRole->isHigherThan($role)) {
                throw new ApiException(
                    errorCode: ApiErrorCode::FORBIDDEN,
                    userMessage: 'شما نمی‌توانید نقشی با اولویت بالاتر یا مساوی خود اختصاص دهید.',
                    httpStatus: 403,
                );
            }
        }

        $user->assignRole($role, $reason);

        AppLogger::admin()->info('role.assigned', [
            'user_id' => $user->id,
            'role_id' => $role->id,
            'assigned_by' => $assignedBy?->id,
        ]);
    }

    /**
     * Get all users with specific role
     */
    public function getUsersByRole(string $roleSlug)
    {
        $role = Role::where('slug', $roleSlug)->first();
        if (!$role) {
            throw ApiException::notFound('نقش', $roleSlug);
        }

        return $role->users()->get();
    }

    /**
     * Get role statistics
     */
    public function getRoleStatistics(): array
    {
        return [
            'total_roles' => Role::count(),
            'system_roles' => Role::system()->count(),
            'active_roles' => Role::active()->count(),
            'roles_with_2fa' => Role::where('requires_2fa', true)->count(),
            'users_per_role' => Role::withCount('users')->get()->map(fn($r) => [
                'slug' => $r->slug,
                'name' => $r->name,
                'users_count' => $r->users_count,
            ]),
        ];
    }
}
