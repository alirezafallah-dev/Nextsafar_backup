<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Modules\Auth\Services\RoleService;
use Illuminate\Console\Command;

class TestRbac extends Command
{
    protected $signature = 'test:rbac';
    protected $description = 'Test RBAC functionality';

    public function handle(RoleService $roleService): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║     🔐 RBAC Test Suite                        ║');
        $this->info('╚══════════════════════════════════════════════╝');
        $this->info('');

        $tests = [
            'roles_exist' => $this->testRolesExist(),
            'permissions_exist' => $this->testPermissionsExist(),
            'assign_role' => $this->testAssignRole(),
            'check_permission' => $this->testCheckPermission(),
            'super_admin_bypass' => $this->testSuperAdminBypass(),
        ];

        $this->newLine();
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║            📊 RESULTS                         ║');
        $this->info('╚══════════════════════════════════════════════╝');

        $passed = 0;
        foreach ($tests as $test => $result) {
            if ($result) {
                $this->info("  ✅ {$test}");
                $passed++;
            } else {
                $this->error("  ❌ {$test}");
            }
        }

        $this->newLine();
        $this->info("Passed: {$passed}/" . count($tests));

        return $passed === count($tests) ? 0 : 1;
    }

    private function testRolesExist(): bool
    {
        $count = Role::count();
        $this->line("   Roles in database: {$count}");
        return $count >= 9; // 9 default roles
    }

    private function testPermissionsExist(): bool
    {
        $count = Permission::count();
        $this->line("   Permissions in database: {$count}");
        return $count >= 20; // حداقل ۲۰ دسترسی پایه
    }

    private function testAssignRole(): bool
    {
        try {
            $user = User::first();
            if (!$user) {
                $user = User::create([
                    'name' => 'Test User',
                    'phone' => '0912' . rand(1000000, 9999999),
                    'password' => bcrypt('password'),
                ]);
            }

            $role = Role::where('slug', 'support')->first();
            if (!$role) return false;

            $user->assignRole($role, 'Test assignment');
            
            $hasRole = $user->hasRole('support');
            $this->line("   User has 'support' role: " . ($hasRole ? 'Yes' : 'No'));
            
            // Clean up
            $user->removeRole($role, 'Test cleanup');
            
            return $hasRole;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testCheckPermission(): bool
    {
        try {
            $user = User::first();
            $role = Role::where('slug', 'tour_expert')->first();
            
            if (!$role) return false;
            
            $user->assignRole($role);
            
            $hasPermission = $user->hasPermission('tours.view');
            $this->line("   User has 'tours.view': " . ($hasPermission ? 'Yes' : 'No'));
            
            $user->removeRole($role);
            
            return $hasPermission;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testSuperAdminBypass(): bool
    {
        try {
            $user = User::first();
            $superAdminRole = Role::where('slug', 'super_admin')->first();
            
            if (!$superAdminRole) return false;
            
            $user->assignRole($superAdminRole);
            
            $canDoAnything = $user->hasPermission('any.permission.here');
            $this->line("   Super admin has any permission: " . ($canDoAnything ? 'Yes' : 'No'));
            
            $user->removeRole($superAdminRole);
            
            return $canDoAnything;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }
}
