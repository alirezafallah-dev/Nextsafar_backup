<?php

namespace App\Filament\Admin\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'پرداخت‌ها';
    protected static ?string $modelLabel = 'پرداخت';
    protected static ?string $pluralModelLabel = 'پرداخت‌ها';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('کد پرداخت')->searchable()->badge()->color('primary'),
                TextColumn::make('gateway')->label('درگاه')->badge()
                    ->formatStateUsing(fn ($s) => strtoupper($s))
                    ->color('info'),
                TextColumn::make('amount')->label('مبلغ')->money('IRR', true)->weight('bold'),
                TextColumn::make('status')->label('وضعیت')->badge()
                    ->formatStateUsing(fn ($s) => match ($s) {
                        'initiated' => '⏳ شروع شده',
                        'pending' => '⏸️ در انتظار',
                        'successful' => '✅ موفق',
                        'failed' => '❌ ناموفق',
                        'refunded' => '💸 مسترد شده',
                        default => $s,
                    })
                    ->color(fn ($s) => match ($s) {
                        'successful' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'purple',
                        default => 'warning',
                    }),
                TextColumn::make('reference_id')->label('کد پیگیری')->searchable()->copyable()->placeholder('—'),
                TextColumn::make('paid_at')->label('تاریخ پرداخت')->dateTime('Y/m/d H:i')->placeholder('—'),
            ])
            ->actions([
                ViewAction::make()->url(fn ($record) => route('filament.admin.resources.payments.view', $record)),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
