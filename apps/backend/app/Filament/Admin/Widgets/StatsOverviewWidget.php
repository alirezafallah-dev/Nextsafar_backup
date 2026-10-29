<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use App\Modules\Lead\Models\Lead;
use App\Modules\Order\Models\Order;
use App\Modules\Payment\Models\Payment;
use App\Modules\Visa\Models\VisaCase;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public function getHeading(): string
    {
        return 'آمار کلی سیستم';
    }

    protected function getStats(): array
    {
        return [
            Stat::make('کل سفارش‌ها', Order::count())
                ->description('تعداد کل سفارش‌های ثبت شده')
                ->descriptionIcon('heroicon-o-shopping-cart')
                ->color('primary'),

            Stat::make('درآمد کل', number_format(Payment::where('status', 'successful')->sum('amount')) . ' IRR')
                ->description('مجموع پرداخت‌های موفق')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('کاربران فعال', User::where('is_active', true)->count())
                ->description('کاربرانی که حساب فعال دارند')
                ->descriptionIcon('heroicon-o-users')
                ->color('info'),

            Stat::make('درخواست‌های جدید', Lead::where('status', 'new')->count())
                ->description('درخواست‌های در انتظار پیگیری')
                ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                ->color('warning'),

            Stat::make('پرونده‌های ویزا', VisaCase::whereNotIn('status', ['completed', 'cancelled'])->count())
                ->description('پرونده‌های در حال پردازش')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('purple'),

            Stat::make('سفارش‌های در انتظار', Order::where('status', 'pending_payment')->count())
                ->description('سفارش‌های در انتظار پرداخت')
                ->descriptionIcon('heroicon-o-clock')
                ->color('danger'),
        ];
    }
}
