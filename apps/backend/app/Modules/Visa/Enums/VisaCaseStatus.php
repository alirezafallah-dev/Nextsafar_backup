<?php

namespace App\Modules\Visa\Enums;

/**
 * ۱۴ وضعیت پرونده ویزا طبق بخش ۱۶.۲ سند
 */
enum VisaCaseStatus: string
{
    case REGISTERED = 'registered';
    case DOCUMENTS_INCOMPLETE = 'documents_incomplete';
    case DOCUMENTS_COMPLETE = 'documents_complete';
    case UNDER_REVIEW = 'under_review';
    case NEEDS_APPOINTMENT = 'needs_appointment';
    case APPOINTMENT_BOOKED = 'appointment_booked';
    case SUBMITTED = 'submitted';
    case IN_EMBASSY_PROCESS = 'in_embassy_process';
    case NEEDS_BIOMETRICS = 'needs_biometrics';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case NEEDS_CORRECTION = 'needs_correction';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::REGISTERED => 'ثبت اولیه',
            self::DOCUMENTS_INCOMPLETE => 'مدارک ناقص',
            self::DOCUMENTS_COMPLETE => 'مدارک کامل',
            self::UNDER_REVIEW => 'در حال بررسی',
            self::NEEDS_APPOINTMENT => 'نیازمند وقت سفارت',
            self::APPOINTMENT_BOOKED => 'وقت گرفته شد',
            self::SUBMITTED => 'ارسال شده',
            self::IN_EMBASSY_PROCESS => 'در پروسه سفارت',
            self::NEEDS_BIOMETRICS => 'نیازمند انگشت‌نگاری',
            self::APPROVED => 'تأیید شد',
            self::REJECTED => 'رد شد',
            self::NEEDS_CORRECTION => 'نیازمند اصلاح',
            self::COMPLETED => 'تکمیل شد',
            self::CANCELLED => 'لغو شد',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::REGISTERED => 'blue',
            self::DOCUMENTS_INCOMPLETE => 'orange',
            self::DOCUMENTS_COMPLETE => 'indigo',
            self::UNDER_REVIEW => 'yellow',
            self::NEEDS_APPOINTMENT => 'purple',
            self::APPOINTMENT_BOOKED => 'cyan',
            self::SUBMITTED => 'indigo',
            self::IN_EMBASSY_PROCESS => 'violet',
            self::NEEDS_BIOMETRICS => 'amber',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::NEEDS_CORRECTION => 'orange',
            self::COMPLETED => 'emerald',
            self::CANCELLED => 'gray',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::REGISTERED => 'heroicon-o-document-plus',
            self::DOCUMENTS_INCOMPLETE => 'heroicon-o-exclamation-triangle',
            self::DOCUMENTS_COMPLETE => 'heroicon-o-document-check',
            self::UNDER_REVIEW => 'heroicon-o-magnifying-glass',
            self::NEEDS_APPOINTMENT => 'heroicon-o-calendar',
            self::APPOINTMENT_BOOKED => 'heroicon-o-calendar-days',
            self::SUBMITTED => 'heroicon-o-paper-airplane',
            self::IN_EMBASSY_PROCESS => 'heroicon-o-building-office-2',
            self::NEEDS_BIOMETRICS => 'heroicon-o-finger-print',
            self::APPROVED => 'heroicon-o-check-circle',
            self::REJECTED => 'heroicon-o-x-circle',
            self::NEEDS_CORRECTION => 'heroicon-o-pencil-square',
            self::COMPLETED => 'heroicon-o-check-badge',
            self::CANCELLED => 'heroicon-o-no-symbol',
        };
    }

    /**
     * وضعیت‌های فعال (در جریان)
     */
    public static function activeStatuses(): array
    {
        return [
            self::REGISTERED,
            self::DOCUMENTS_INCOMPLETE,
            self::DOCUMENTS_COMPLETE,
            self::UNDER_REVIEW,
            self::NEEDS_APPOINTMENT,
            self::APPOINTMENT_BOOKED,
            self::SUBMITTED,
            self::IN_EMBASSY_PROCESS,
            self::NEEDS_BIOMETRICS,
            self::NEEDS_CORRECTION,
        ];
    }

    /**
     * وضعیت‌های پایانی
     */
    public static function terminalStatuses(): array
    {
        return [
            self::APPROVED,
            self::REJECTED,
            self::COMPLETED,
            self::CANCELLED,
        ];
    }

    /**
     * وضعیت‌های موفق
     */
    public static function successfulStatuses(): array
    {
        return [
            self::APPROVED,
            self::COMPLETED,
        ];
    }

    /**
     * آیا این وضعیت فعال است؟
     */
    public function isActive(): bool
    {
        return in_array($this, self::activeStatuses());
    }

    /**
     * آیا این وضعیت پایانی است؟
     */
    public function isTerminal(): bool
    {
        return in_array($this, self::terminalStatuses());
    }

    /**
     * آیا این وضعیت موفقیت‌آمیز است؟
     */
    public function isSuccessful(): bool
    {
        return in_array($this, self::successfulStatuses());
    }

    /**
     * آیا این وضعیت نیاز به اقدام دارد؟
     */
    public function requiresAction(): bool
    {
        return in_array($this, [
            self::DOCUMENTS_INCOMPLETE,
            self::NEEDS_APPOINTMENT,
            self::NEEDS_BIOMETRICS,
            self::NEEDS_CORRECTION,
        ]);
    }

    /**
     * وضعیت‌های مجاز برای انتقال از این وضعیت
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::REGISTERED => [self::DOCUMENTS_INCOMPLETE, self::DOCUMENTS_COMPLETE, self::CANCELLED],
            self::DOCUMENTS_INCOMPLETE => [self::DOCUMENTS_COMPLETE, self::CANCELLED],
            self::DOCUMENTS_COMPLETE => [self::UNDER_REVIEW, self::NEEDS_APPOINTMENT, self::CANCELLED],
            self::UNDER_REVIEW => [self::NEEDS_APPOINTMENT, self::NEEDS_CORRECTION, self::SUBMITTED, self::CANCELLED],
            self::NEEDS_APPOINTMENT => [self::APPOINTMENT_BOOKED, self::CANCELLED],
            self::APPOINTMENT_BOOKED => [self::SUBMITTED, self::NEEDS_BIOMETRICS, self::CANCELLED],
            self::SUBMITTED => [self::IN_EMBASSY_PROCESS, self::NEEDS_BIOMETRICS, self::CANCELLED],
            self::IN_EMBASSY_PROCESS => [self::APPROVED, self::REJECTED, self::NEEDS_CORRECTION],
            self::NEEDS_BIOMETRICS => [self::IN_EMBASSY_PROCESS, self::CANCELLED],
            self::APPROVED => [self::COMPLETED],
            self::REJECTED => [],
            self::NEEDS_CORRECTION => [self::UNDER_REVIEW, self::CANCELLED],
            self::COMPLETED => [],
            self::CANCELLED => [],
        };
    }

    /**
     * آیا انتقال به وضعیت دیگر مجاز است؟
     */
    public function canTransitionTo(VisaCaseStatus $newStatus): bool
    {
        return in_array($newStatus, $this->allowedTransitions());
    }
}
