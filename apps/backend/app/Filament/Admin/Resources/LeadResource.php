<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LeadResource\Pages;
use App\Filament\Admin\Resources\LeadResource\RelationManagers;
use App\Modules\Lead\Models\Lead;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'درخواست‌ها';
    protected static \UnitEnum|string|null $navigationGroup = 'مدیریت فروش';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'درخواست';
    protected static ?string $pluralModelLabel = 'درخواست‌ها';
    protected static ?string $slug = 'leads';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('اطلاعات اصلی')->schema([
                TextInput::make('code')->label('کد درخواست')->disabled()->columnSpan(1),
                Select::make('type')->label('نوع درخواست')->options([
                    'tour_request' => '🌍 درخواست تور',
                    'hotel_request' => '🏨 درخواست هتل',
                    'visa_request' => '🛂 درخواست ویزا',
                    'consultation' => '💬 مشاوره',
                    'callback' => '📞 درخواست تماس',
                    'price_inquiry' => '💰 استعلام قیمت',
                ])->required()->disabled(fn ($record) => $record !== null),
                Select::make('status')->label('وضعیت')->options(self::getStatusOptions())->required(),
                Select::make('priority')->label('اولویت')->options([
                    'low' => 'کم',
                    'normal' => 'عادی',
                    'high' => 'بالا',
                    'urgent' => 'فوری',
                ])->default('normal'),
            ])->columns(4),

            Section::make('اطلاعات تماس')->schema([
                TextInput::make('full_name')->label('نام و نام خانوادگی')->required()->maxLength(255),
                TextInput::make('phone')->label('شماره تماس')->tel()->required()->maxLength(20),
                TextInput::make('email')->label('ایمیل')->email()->nullable()->maxLength(255),
                TextInput::make('source')->label('منبع درخواست')->default('website')->disabled(),
            ])->columns(2),

            Section::make('جزئیات درخواست')->schema([
                TextInput::make('destination')->label('مقصد')->nullable()->maxLength(255),
                DatePicker::make('travel_date')->label('تاریخ سفر')->native(false)->displayFormat('Y/m/d'),
                TextInput::make('adults')->label('بزرگسال')->numeric()->default(1)->minValue(0),
                TextInput::make('children')->label('کودک')->numeric()->default(0)->minValue(0),
                TextInput::make('infants')->label('نوزاد')->numeric()->default(0)->minValue(0),
                TextInput::make('budget')->label('بودجه تقریبی')->numeric()->prefix('IRR')->nullable(),
            ])->columns(3),

            Section::make('یادداشت‌ها')->schema([
                Textarea::make('message')->label('پیام کاربر')->rows(4)->columnSpan(1),
                Textarea::make('admin_notes')->label('یادداشت ادمین')->rows(4)->columnSpan(1),
            ])->columns(2),

            Section::make('پیگیری')->schema([
                Select::make('assigned_to')->label('کارشناس مسئول')
                    ->relationship('assignedAgent', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                DatePicker::make('next_follow_up_at')->label('تاریخ پیگیری بعدی')->native(false)->displayFormat('Y/m/d H:i'),
                Placeholder::make('created_at')->label('تاریخ ایجاد')->content(fn ($record) => $record?->created_at?->format('Y/m/d H:i') ?? '—'),
                Placeholder::make('converted_at')->label('تاریخ تبدیل')->content(fn ($record) => $record?->converted_at?->format('Y/m/d H:i') ?? '—'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('کد')->searchable()->sortable()->badge()->color('primary'),
                TextColumn::make('type')->label('نوع')->badge()
                    ->formatStateUsing(fn ($s) => self::getTypeLabel($s))
                    ->color(fn ($s) => self::getTypeColor($s))
                    ->sortable(),
                TextColumn::make('full_name')->label('نام')->searchable()->sortable()->limit(20),
                TextColumn::make('phone')->label('موبایل')->searchable()->copyable(),
                TextColumn::make('destination')->label('مقصد')->searchable()->limit(20)->placeholder('—'),
                TextColumn::make('status')->label('وضعیت')->badge()
                    ->formatStateUsing(fn ($s) => self::getStatusOptions()[$s] ?? $s)
                    ->color(fn ($s) => self::getStatusColor($s))
                    ->sortable(),
                TextColumn::make('priority')->label('اولویت')->badge()
                    ->formatStateUsing(fn ($s) => match ($s) {
                        'low' => 'کم',
                        'normal' => 'عادی',
                        'high' => 'بالا',
                        'urgent' => 'فوری',
                        default => $s,
                    })
                    ->color(fn ($s) => match ($s) {
                        'urgent' => 'danger',
                        'high' => 'warning',
                        'normal' => 'info',
                        'low' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('assignedAgent.name')->label('کارشناس')->placeholder('—')->toggleable(),
                TextColumn::make('travel_date')->label('تاریخ سفر')->date('Y/m/d')->sortable()->toggleable()->placeholder('—'),
                TextColumn::make('created_at')->label('تاریخ ایجاد')->dateTime('Y/m/d H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')->label('نوع')->options([
                    'tour_request' => '🌍 تور',
                    'hotel_request' => '🏨 هتل',
                    'visa_request' => '🛂 ویزا',
                    'consultation' => '💬 مشاوره',
                    'callback' => '📞 تماس',
                    'price_inquiry' => '💰 استعلام',
                ]),
                SelectFilter::make('status')->label('وضعیت')->options(self::getStatusOptions()),
                SelectFilter::make('priority')->label('اولویت')->options([
                    'low' => 'کم',
                    'normal' => 'عادی',
                    'high' => 'بالا',
                    'urgent' => 'فوری',
                ]),
                Filter::make('assigned')->label('فقط اختصاص داده شده')
                    ->query(fn (Builder $q) => $q->whereNotNull('assigned_to')),
                Filter::make('unassigned')->label('فقط اختصاص نشده')
                    ->query(fn (Builder $q) => $q->whereNull('assigned_to')),
                Filter::make('date_range')->label('بازه تاریخ')->form([
                    DatePicker::make('from_date')->label('از تاریخ'),
                    DatePicker::make('to_date')->label('تا تاریخ'),
                ])->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['from_date'] ?? null, fn (Builder $q, $v) => $q->whereDate('created_at', '>=', $v))
                        ->when($data['to_date'] ?? null, fn (Builder $q, $v) => $q->whereDate('created_at', '<=', $v));
                }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('assign')
                    ->label('اختصاص')
                    ->icon('heroicon-o-user')
                    ->color('info')
                    ->form([
                        Select::make('assigned_to')
                            ->label('کارشناس')
                            ->relationship('assignedAgent', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->action(function (Lead $record, array $data): void {
                        $record->update(['assigned_to' => $data['assigned_to']]);
                        \Filament\Notifications\Notification::make()
                            ->title('درخواست اختصاص یافت')
                            ->success()
                            ->send();
                    }),
                Action::make('convert_to_order')
                    ->label('تبدیل به سفارش')
                    ->icon('heroicon-o-shopping-cart')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('تبدیل درخواست به سفارش')
                    ->modalDescription('آیا مطمئن هستید؟')
                    ->action(function (Lead $record): void {
                        // TODO: Implement conversion logic
                        \Filament\Notifications\Notification::make()
                            ->title('در حال تبدیل...')
                            ->info()
                            ->send();
                    })
                    ->visible(fn (Lead $record) => $record->status !== 'converted_to_order'),
                Action::make('close')
                    ->label('بستن')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Lead $record): void {
                        $record->update(['status' => 'closed']);
                        \Filament\Notifications\Notification::make()
                            ->title('درخواست بسته شد')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Lead $record) => !in_array($record->status, ['closed', 'converted_to_order'])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->persistFiltersInSession();
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StatusHistoryRelationManager::class,
            RelationManagers\NotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'view' => Pages\ViewLead::route('/{record}'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $new = static::getModel()::where('status', 'new')->count();
        return $new > 0 ? (string) $new : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    protected static function getStatusOptions(): array
    {
        return [
            'new' => '🆕 جدید',
            'reviewing' => '🔍 در حال بررسی',
            'contacted' => '📞 تماس گرفته شد',
            'waiting_response' => '⏳ در انتظار پاسخ',
            'phone_booked' => '✓ رزرو تلفنی شد',
            'converted_to_order' => '🛒 تبدیل به سفارش شد',
            'closed' => '❌ بسته شد',
            'no_response' => '⚠️ بی‌پاسخ',
        ];
    }

    protected static function getStatusColor(string $status): string
    {
        return match ($status) {
            'new' => 'danger',
            'reviewing', 'contacted', 'waiting_response' => 'warning',
            'phone_booked', 'converted_to_order' => 'success',
            'closed', 'no_response' => 'gray',
            default => 'gray',
        };
    }

    protected static function getTypeLabel(string $type): string
    {
        return match ($type) {
            'tour_request' => '🌍 تور',
            'hotel_request' => '🏨 هتل',
            'visa_request' => '🛂 ویزا',
            'consultation' => '💬 مشاوره',
            'callback' => '📞 تماس',
            'price_inquiry' => '💰 استعلام',
            default => $type,
        };
    }

    protected static function getTypeColor(string $type): string
    {
        return match ($type) {
            'tour_request' => 'warning',
            'hotel_request' => 'success',
            'visa_request' => 'primary',
            'consultation' => 'info',
            'callback' => 'purple',
            'price_inquiry' => 'gray',
            default => 'gray',
        };
    }
}
