<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'کاربران';
    protected static \UnitEnum|string|null $navigationGroup = 'مدیریت کاربران';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'کاربر';
    protected static ?string $pluralModelLabel = 'کاربران';
    protected static ?string $slug = 'users';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('اطلاعات شخصی')->schema([
                TextInput::make('name')->label('نام و نام خانوادگی')->required()->maxLength(255),
                TextInput::make('email')->label('ایمیل')->email()->nullable()->unique(ignoreRecord: true)->maxLength(255),
                TextInput::make('phone')->label('موبایل')->tel()->nullable()->unique(ignoreRecord: true)->maxLength(20),
                TextInput::make('avatar')->label('آدرس آواتار (URL)')->url()->nullable()->placeholder('https://...'),
            ])->columns(2),
            Section::make('امنیت حساب')->schema([
                TextInput::make('password')->label('رمز عبور جدید')->password()->revealable()->minLength(8)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->helperText('خالی = بدون تغییر رمز.'),
                Toggle::make('is_active')->label('حساب فعال است')->helperText('غیرفعال = کاربر نمی‌تواند وارد شود.')->default(true),
            ])->columns(2),
            Section::make('اطلاعات ثبت‌نام')->schema([
                Placeholder::make('signup_method')->label('روش ثبت‌نام')->formatStateUsing(fn ($s) => match ($s) {
                    'phone' => '📱 موبایل (OTP)', 'google' => '🔵 گوگل', 'admin' => '🛡️ ادمین', default => '—',
                }),
                Placeholder::make('phone_verified_at')->label('تایید موبایل')->formatStateUsing(fn ($s) => $s ? '✅ '.$s->format('Y/m/d H:i') : '❌ تایید نشده'),
                Placeholder::make('email_verified_at')->label('تایید ایمیل')->formatStateUsing(fn ($s) => $s ? '✅ '.$s->format('Y/m/d H:i') : '❌ تایید نشده'),
                Placeholder::make('created_at')->label('تاریخ عضویت')->formatStateUsing(fn ($s) => $s ? $s->format('Y/m/d H:i') : '—'),
            ])->columns(2)->visibleOn('edit'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('avatar')->label('آواتار')->circular()->size(36)
                    ->defaultImageUrl(fn (User $r) => 'https://ui-avatars.com/api/?name='.urlencode($r->name ?: 'U').'&background=fba834&color=fff&bold=true'),
                TextColumn::make('name')->label('نام')->searchable()->sortable()->weight('medium'),
                TextColumn::make('phone')->label('موبایل')->searchable()->copyable()->placeholder('—'),
                TextColumn::make('email')->label('ایمیل')->searchable()->copyable()->placeholder('—')->toggleable(),
                TextColumn::make('signup_method')->label('ثبت‌نام')->badge()
                    ->formatStateUsing(fn ($s) => match ($s) { 'phone'=>'📱 موبایل','google'=>'🔵 گوگل','admin'=>'🛡️ ادمین',default=>'—' })
                    ->color(fn ($s) => match ($s) { 'phone'=>'info','google'=>'success','admin'=>'warning',default=>'gray' }),
                IconColumn::make('is_active')->label('وضعیت')->boolean()
                    ->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')->falseColor('danger')->sortable(),
                TextColumn::make('bookings_count')->label('رزروها')->counts('bookings')->badge()->color('primary')->sortable(),
                TextColumn::make('payments_count')->label('پرداخت‌ها')->counts('payments')->badge()->color('success')->sortable()->toggleable(),
                TextColumn::make('created_at')->label('عضویت')->dateTime('Y/m/d')->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('signup_method')->label('روش ثبت‌نام')->options([
                    'phone'=>'موبایل (OTP)','google'=>'گوگل','admin'=>'ادمین',
                ]),
                TernaryFilter::make('is_active')->label('وضعیت حساب')->trueLabel('فعال')->falseLabel('غیرفعال')
                    ->queries(
                        true: fn (Builder $q) => $q->where('is_active', true),
                        false: fn (Builder $q) => $q->where('is_active', false),
                        blank: fn (Builder $q) => $q,
                    ),
                Filter::make('created_at')->label('بازه عضویت')->form([
                    DatePicker::make('created_from')->label('از تاریخ'),
                    DatePicker::make('created_until')->label('تا تاریخ'),
                ])->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['created_from'] ?? null, fn (Builder $q, $v): Builder => $q->whereDate('created_at', '>=', $v))
                        ->when($data['created_until'] ?? null, fn (Builder $q, $v): Builder => $q->whereDate('created_at', '<=', $v));
                }),
                Filter::make('has_bookings')->label('فقط با رزرو')->query(fn (Builder $q) => $q->has('bookings')),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('toggle_active')
                    ->label(fn (User $r) => $r->is_active ? 'غیرفعال' : 'فعال')
                    ->icon(fn (User $r) => $r->is_active ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                    ->color(fn (User $r) => $r->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (User $r) => $r->is_active ? 'غیرفعال کردن حساب' : 'فعال کردن حساب')
                    ->modalDescription(fn (User $r) => $r->is_active ? "«{$r->name}» دیگر نمی‌تواند وارد شود." : "«{$r->name}» مجدداً می‌تواند وارد شود.")
                    ->action(fn (User $r) => $r->update(['is_active' => ! $r->is_active])),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->persistFiltersInSession();
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\BookingsRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null { return 'success'; }
    public static function getNavigationBadgeTooltip(): ?string { return 'تعداد کاربران فعال'; }
}
