<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PaymentResource\Pages;
use App\Modules\Payment\Models\Payment;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'پرداخت‌ها';
    protected static \UnitEnum|string|null $navigationGroup = 'مدیریت مالی';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'پرداخت';
    protected static ?string $pluralModelLabel = 'پرداخت‌ها';
    protected static ?string $slug = 'payments';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('اطلاعات تراکنش')->schema([
                Placeholder::make('transaction_id')->label('شناسه تراکنش')->copyable(),
                Placeholder::make('reference_id')->label('شناسه مرجع')->copyable()->placeholder('—'),
                Placeholder::make('authority_code')->label('کد اختیار')->copyable()->placeholder('—'),
                Placeholder::make('gateway')->label('درگاه')->formatStateUsing(fn ($s) => match ($s) {
                    'zarinpal'=>'🟢 زرین‌پال','idpay'=>'🔵 آیدی‌پی','wallet'=>'🟡 کیف پول','manual'=>'⚪ دستی',default=>$s??'—',
                }),
                Placeholder::make('status')->label('وضعیت')->formatStateUsing(fn ($s) => match ($s) {
                    'pending'=>'🟡 در انتظار','redirecting'=>'🔵 در حال انتقال','success'=>'🟢 موفق','failed'=>'🔴 ناموفق','cancelled'=>'⚪ لغو شده','refunded'=>'🟣 مسترد شده',default=>$s??'—',
                }),
                Placeholder::make('amount_display')->label('مبلغ')->formatStateUsing(fn (?Payment $r) => $r
                    ? self::formatAmount($r->amount).' '.self::currencyLabel($r->currency) : '—'),
                Placeholder::make('paid_at')->label('تاریخ پرداخت')->formatStateUsing(fn ($s) => $s ? $s->format('Y/m/d H:i:s') : '❌ پرداخت نشده'),
                Placeholder::make('created_at')->label('تاریخ ایجاد')->formatStateUsing(fn ($s) => $s?->format('Y/m/d H:i:s') ?? '—'),
            ])->columns(2),
            Section::make('رزرو و کاربر')->schema([
                Placeholder::make('booking_info')->label('رزرو')->formatStateUsing(fn (?Payment $r) => $r?->booking
                    ? "📋 {$r->booking->booking_code} — {$r->booking->item_title}" : '—'),
                Placeholder::make('user_info')->label('کاربر')->formatStateUsing(fn (?Payment $r) => $r?->user
                    ? "👤 {$r->user->name} ({$r->user->phone})" : '—'),
                Placeholder::make('description')->label('توضیحات')->formatStateUsing(fn ($s) => $s ?? '—'),
            ])->columns(1),
            Section::make('اطلاعات کارت (Mask شده)')->schema([
                Placeholder::make('card_number_masked')->label('شماره کارت')->formatStateUsing(fn (?Payment $r) => self::maskCard($r?->card_number)),
                Placeholder::make('ip_address')->label('IP کاربر')->placeholder('—'),
                Placeholder::make('user_agent')->label('مرورگر')->formatStateUsing(fn ($s) => $s ? Str::limit($s, 80) : '—'),
            ])->columns(2),
            Section::make('لاگ درگاه (JSON)')->schema([
                Placeholder::make('gateway_request')->label('درخواست به درگاه')->formatStateUsing(fn ($s) => self::formatJson($s)),
                Placeholder::make('gateway_response')->label('پاسخ درگاه')->formatStateUsing(fn ($s) => self::formatJson($s)),
                Placeholder::make('callback_data')->label('داده Callback')->formatStateUsing(fn ($s) => self::formatJson($s)),
            ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_id')->label('شناسه تراکنش')->searchable()->copyable()->limit(16)
                    ->extraAttributes(['class' => 'font-mono text-sm']),
                TextColumn::make('booking.booking_code')->label('کد رزرو')->badge()->color('primary')->searchable()->placeholder('—'),
                TextColumn::make('user.name')->label('کاربر')->searchable()->sortable()->placeholder('—'),
                TextColumn::make('gateway')->label('درگاه')->badge()
                    ->formatStateUsing(fn ($s) => match ($s) { 'zarinpal'=>'زرین‌پال','idpay'=>'آیدی‌پی','wallet'=>'کیف پول','manual'=>'دستی',default=>$s })
                    ->color(fn ($s) => match ($s) { 'zarinpal'=>'success','idpay'=>'info','wallet'=>'warning','manual'=>'gray',default=>'gray' }),
                TextColumn::make('amount')->label('مبلغ')
                    ->formatStateUsing(fn ($s, Payment $r) => self::formatAmount($s).' '.self::currencyLabel($r->currency))
                    ->sortable()->alignEnd()->weight('medium'),
                TextColumn::make('status')->label('وضعیت')->badge()
                    ->formatStateUsing(fn ($s) => match ($s) { 'pending'=>'در انتظار','redirecting'=>'در حال انتقال','success'=>'موفق','failed'=>'ناموفق','cancelled'=>'لغو شده','refunded'=>'مسترد شده',default=>$s })
                    ->color(fn ($s) => match ($s) { 'pending'=>'warning','redirecting'=>'info','success'=>'success','failed'=>'danger','cancelled'=>'gray','refunded'=>'info',default=>'gray' }),
                TextColumn::make('card_number')->label('کارت')->formatStateUsing(fn ($s) => self::maskCard($s))->placeholder('—')
                    ->toggleable()->extraAttributes(['class' => 'font-mono text-sm']),
                TextColumn::make('paid_at')->label('تاریخ پرداخت')->dateTime('Y/m/d H:i')->sortable()->placeholder('—'),
                TextColumn::make('created_at')->label('ایجاد')->dateTime('Y/m/d H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->label('وضعیت')->options([
                    'pending'=>'در انتظار','redirecting'=>'در حال انتقال','success'=>'موفق','failed'=>'ناموفق','cancelled'=>'لغو شده','refunded'=>'مسترد شده',
                ])->multiple(),
                SelectFilter::make('gateway')->label('درگاه')->options([
                    'zarinpal'=>'زرین‌پال','idpay'=>'آیدی‌پی','wallet'=>'کیف پول','manual'=>'دستی',
                ]),
                SelectFilter::make('currency')->label('واحد')->options([
                    'IRR'=>'ریال','IRT'=>'تومان','USD'=>'دلار','EUR'=>'یورو','AED'=>'درهم',
                ]),
                Filter::make('created_at')->label('بازه زمانی')->form([
                    DatePicker::make('from')->label('از تاریخ'),
                    DatePicker::make('until')->label('تا تاریخ'),
                ])->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['from'] ?? null, fn (Builder $q, $v): Builder => $q->whereDate('created_at', '>=', $v))
                        ->when($data['until'] ?? null, fn (Builder $q, $v): Builder => $q->whereDate('created_at', '<=', $v));
                }),
                Filter::make('successful_only')->label('فقط موفق')->query(fn (Builder $q) => $q->where('status', 'success')),
            ])
            ->actions([
                ViewAction::make()->label('مشاهده'),
                Action::make('view_booking')->label('رزرو')->icon('heroicon-o-clipboard-document-list')->color('primary')
                    ->url(fn (Payment $r) => $r->booking_id ? route('filament.admin.resources.bookings.view', $r->booking_id) : null)
                    ->visible(fn (Payment $r) => (bool) $r->booking_id),
                Action::make('view_user')->label('کاربر')->icon('heroicon-o-user')->color('info')
                    ->url(fn (Payment $r) => $r->user_id ? route('filament.admin.resources.users.view', $r->user_id) : null)
                    ->visible(fn (Payment $r) => (bool) $r->user_id),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->persistFiltersInSession()
            ->poll('30s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'view' => Pages\ViewPayment::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $c = static::getModel()::whereIn('status', ['pending', 'redirecting'])->count();
        return $c > 0 ? (string) $c : null;
    }

    public static function getNavigationBadgeColor(): string|array|null { return 'warning'; }
    public static function getNavigationBadgeTooltip(): ?string { return 'پرداخت‌های در انتظار/در حال انتقال'; }

    public static function formatAmount($amount): string
    {
        if ($amount === null || $amount === '') return '—';
        return number_format((float) $amount, 0, '.', ',');
    }

    public static function currencyLabel(?string $c): string
    {
        return match ($c) { 'IRR'=>'ریال','IRT'=>'تومان','USD'=>'دلار','EUR'=>'یورو','AED'=>'درهم',default=>$c??'ریال' };
    }

    public static function maskCard(?string $card): string
    {
        if (blank($card)) return '—';
        $clean = preg_replace('/\s+/', '', $card);
        if (strlen($clean) < 4) return '****';
        return '**** **** **** '.substr($clean, -4);
    }

    public static function formatJson($value): string
    {
        if (blank($value)) return '—';
        if (is_string($value)) { $d = json_decode($value, true); $value = json_last_error() === JSON_ERROR_NONE ? $d : $value; }
        if (is_array($value)) return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return (string) $value;
    }
}
