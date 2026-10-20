<?php

namespace App\Filament\Admin\Widgets;

use App\Modules\Order\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string
    {
        return 'آخرین سفارش‌ها';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest()->limit(10))
            ->columns([
                TextColumn::make('code')->label('کد')->badge()->color('primary'),
                TextColumn::make('user.name')->label('مشتری')->limit(20),
                TextColumn::make('status')->label('وضعیت')->badge()
                    ->formatStateUsing(function ($s) {
                        if (empty($s)) return 'نامشخص';
                        $statusValue = $s instanceof \BackedEnum ? $s->value : (string) $s;
                        return match ($statusValue) {
                            'draft' => '📝 پیش‌نویس',
                            'pending_payment' => '⏳ در انتظار پرداخت',
                            'paid' => '✅ پرداخت شده',
                            'confirmed' => '✓ تأیید شده',
                            'processing' => '⚙️ در حال پردازش',
                            'completed' => '🎉 تکمیل شده',
                            'cancelled' => '❌ لغو شده',
                            default => $statusValue,
                        };
                    })
                    ->color(function ($s) {
                        if (empty($s)) return 'gray';
                        $statusValue = $s instanceof \BackedEnum ? $s->value : (string) $s;
                        return match ($statusValue) {
                            'draft' => 'gray',
                            'pending_payment' => 'warning',
                            'paid', 'confirmed', 'completed' => 'success',
                            'processing' => 'info',
                            'cancelled' => 'danger',
                            default => 'gray',
                        };
                    }),
                TextColumn::make('total_amount')->label('مبلغ')->money('IRR', true)->weight('bold'),
                TextColumn::make('created_at')->label('تاریخ')->dateTime('Y/m/d H:i'),
            ])
            ->paginated(false);
    }
}
