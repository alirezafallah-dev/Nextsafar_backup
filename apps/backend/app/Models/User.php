<?php

namespace App\Models;

use Filament\Panel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasRoles;
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'signup_method',
        'google_id',
        'google_avatar',
        'google_linked_at',
        'phone_verified_at',
        'email_verified_at',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'google_linked_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if user has verified phone
     */
    public function hasVerifiedPhone(): bool
    {
        return $this->phone_verified_at !== null;
    }

    /**
     * Mark phone as verified
     */
    public function markPhoneAsVerified(): void
    {
        $this->phone_verified_at = now();
        $this->save();
    }

    /**
     * Check if user is linked to Google
     */
    public function isLinkedToGoogle(): bool
    {
        return $this->google_id !== null;
    }

    /**
     * رزروهای کاربر
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(\App\Modules\Booking\Models\Booking::class);
    }

    /**
     * پرداخت‌های کاربر
     */
    public function payments(): HasMany
    {
        return $this->hasMany(\App\Modules\Payment\Models\Payment::class);
    }

    /**
     * بررسی دسترسی به پنل Filament
     * فقط کاربران با نقش‌های خاص می‌توانند وارد پنل شوند
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // فقط کاربران فعال
        if (!$this->is_active) {
            return false;
        }

        // بررسی نقش‌های مجاز
        return $this->hasAnyRole(['super_admin', 'admin', 'manager', 'support']);
    }
}
