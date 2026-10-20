<?php

namespace App\Filament\Admin\Widgets;

use App\Modules\Lead\Models\Lead;
use App\Modules\Notification\Models\Notification;
use App\Modules\Order\Models\Order;
use App\Modules\Payment\Models\Refund;
use App\Modules\Visa\Models\VisaCase;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PendingActionsWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    public function getHeading(): string
    {
        return 'کارهای در انتظار';
    }

    protected function getStats(): array
    {
        return [
            Stat::make('درخواست‌های جدید', Lead::where('status', 'new')->count())
                ->description('نیاز به پیگیری')
                ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                ->color('warning')
                ->url(route('filament.admin.resources.leads.index', ['tableFilters[status][value]' => 'new'])),

            Stat::make('سفارش‌های در انتظار پرداخت', Order::where('status', 'pending_payment')->count())
                ->description('منتظر پرداخت مشتری')
                ->descriptionIcon('heroicon-o-clock')
                ->color('info')
                ->url(route('filament.admin.resources.orders.index', ['tableFilters[status][value]' => 'pending_payment'])),

            Stat::make('استردادهای در انتظار تأیید', Refund::where('status', 'pending')->count())
                ->description('نیاز به بررسی و تأیید')
                ->descriptionIcon('heroicon-o-arrow-uturn-left')
                ->color('danger')
                ->url(route('filament.admin.resources.refunds.index', ['tableFilters[status][value]' => 'pending'])),

            Stat::make('اعلان‌های ناموفق', Notification::where('status', 'failed')->count())
                ->description('نیاز به تلاش مجدد')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->url(route('filament.admin.resources.notifications.index', ['tableFilters[status][value]' => 'failed'])),

            Stat::make('پرونده‌های ویزای تأخیردار', VisaCase::where('is_overdue', true)->count())
                ->description('از SLA گذشته‌اند')
                ->descriptionIcon('heroicon-o-exclamation-circle')
                ->color('danger')
                ->url(route('filament.admin.resources.visa-cases.index', ['tableFilters[is_overdue][value]' => '1'])),
        ];
    }
}
