<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    protected $signature = 'admin:reset-password {email?} {password?}';
    protected $description = 'Reset admin user password';

    public function handle(): int
    {
        $email = $this->argument('email') ?? 'admin@nextsafar.com';
        $password = $this->argument('password') ?? 'Admin@12345';

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            
            // Create super admin if not exists
            if ($this->confirm("Do you want to create this user as super_admin?", true)) {
                $user = User::create([
                    'name' => 'مدیر سیستم',
                    'email' => $email,
                    'phone' => '09120000000',
                    'password' => Hash::make($password),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);

                // Assign super_admin role
                $role = \App\Models\Role::where('slug', 'super_admin')->first();
                if ($role && method_exists($user, 'assignRole')) {
                    $user->assignRole($role);
                }

                $this->info("✅ User created successfully!");
            } else {
                return 1;
            }
        } else {
            // Update password
            $user->update([
                'password' => Hash::make($password),
                'is_active' => true,
            ]);
            $this->info("✅ Password updated for {$email}");
        }

        $this->newLine();
        $this->info('📋 Login Credentials:');
        $this->table(
            ['Field', 'Value'],
            [
                ['URL', config('app.url') . '/admin'],
                ['Email', $email],
                ['Password', $password],
            ]
        );

        $this->newLine();
        $this->warn('⚠️  Please change this password after first login!');

        return 0;
    }
}
