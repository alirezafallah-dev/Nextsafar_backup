<?php
namespace App\Filament\Admin\Resources\UserResource\RelationManagers;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
class BookingsRelationManager extends RelationManager {
    protected static string $relationship = 'bookings';
    protected static ?string $title = 'رزروهای این کاربر';
    public function table(Table $table): Table {
        return $table->recordTitleAttribute('booking_code')->columns([
            TextColumn::make('booking_code')->label('کد رزرو')->badge()->color('primary')->searchable(),
            TextColumn::make('booking_type')->label('نوع')->badge()
                ->formatStateUsing(fn($s)=>match($s){'flight'=>'✈️ پرواز','hotel'=>'🏨 هتل','tour'=>'🌍 تور','visa'=>'🛂 ویزا',default=>$s})
                ->color(fn($s)=>match($s){'flight'=>'info','hotel'=>'success','tour'=>'warning','visa'=>'primary',default=>'gray'}),
            TextColumn::make('item_title')->label('عنوان')->limit(35)->searchable(),
            TextColumn::make('status')->label('وضعیت')->badge()
                ->formatStateUsing(fn($s)=>match($s){'pending'=>'در انتظار','awaiting_payment'=>'در انتظار پرداخت','paid'=>'پرداخت شده','processing'=>'در حال پردازش','confirmed'=>'تایید شده','completed'=>'تکمیل شده','cancelled'=>'لغو شده','refunded'=>'مسترد شده','failed'=>'ناموفق',default=>$s})
                ->color(fn($s)=>match($s){'pending','processing'=>'warning','paid','confirmed','completed'=>'success','cancelled','failed'=>'danger','refunded'=>'info',default=>'gray'}),
            TextColumn::make('total_amount')->label('مبلغ')->money('IRR')->sortable(),
            TextColumn::make('created_at')->label('تاریخ')->dateTime('Y/m/d H:i')->sortable(),
        ])->defaultSort('created_at','desc')->actions([
            ViewAction::make()->label('مشاهده رزرو')->url(fn($r)=>route('filament.admin.resources.bookings.view',$r)),
        ]);
    }
}
