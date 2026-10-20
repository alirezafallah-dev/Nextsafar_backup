<?php
namespace App\Filament\Admin\Resources\UserResource\RelationManagers;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
class PaymentsRelationManager extends RelationManager {
    protected static string $relationship = 'payments';
    protected static ?string $title = 'پرداخت‌های این کاربر';
    public function table(Table $table): Table {
        return $table->recordTitleAttribute('transaction_id')->columns([
            TextColumn::make('transaction_id')->label('شناسه تراکنش')->limit(18)->searchable()->copyable(),
            TextColumn::make('booking.booking_code')->label('رزرو')->badge()->color('primary')->placeholder('—'),
            TextColumn::make('gateway')->label('درگاه')->badge()->formatStateUsing(fn($s)=>match($s){'zarinpal'=>'زرین‌پال','idpay'=>'آیدی‌پی','wallet'=>'کیف پول','manual'=>'دستی',default=>$s}),
            TextColumn::make('amount')->label('مبلغ')->money('IRR')->sortable(),
            TextColumn::make('status')->label('وضعیت')->badge()
                ->formatStateUsing(fn($s)=>match($s){'pending'=>'در انتظار','redirecting'=>'در حال انتقال','success'=>'موفق','failed'=>'ناموفق','cancelled'=>'لغو شده','refunded'=>'مسترد شده',default=>$s})
                ->color(fn($s)=>match($s){'pending','redirecting'=>'warning','success'=>'success','failed'=>'danger','cancelled','refunded'=>'info',default=>'gray'}),
            TextColumn::make('paid_at')->label('تاریخ پرداخت')->dateTime('Y/m/d H:i')->placeholder('—')->sortable(),
        ])->defaultSort('created_at','desc')->actions([
            ViewAction::make()->label('رزرو مرتبط')->url(fn($r)=>$r->booking_id?route('filament.admin.resources.bookings.view',$r->booking_id):null),
        ]);
    }
}
