<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NotificationResource\Pages;
use App\Filament\Admin\Resources\NotificationResource\RelationManagers;
use App\Modules\Notification\Enums\NotificationChannel;
use App\Modules\Notification\Enums\NotificationEvent;
use App\Modules\Notification\Enums\NotificationStatus;
use App\Modules\Notification\Models\Notification;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationLabel = 'اعلان‌ها';
    protected static \UnitEnum|string|null $navigationGroup = 'مدیریت اطلاع‌رسانی';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'اعلان';
    protected static ?string $pluralModelLabel = 'اعلان‌ها';
    protected static ?string $slug = 'notifications';
    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Notification Tabs')->tabs([
                Tab::make('اطلاعات اصلی')->icon('heroicon-o-bell')->schema([
                    Section::make('شناسه و نوع')->schema([
                        TextInput::make('code')->label('کد اعلان')->disabled()->columnSpan(1),
                        TextInput::make('uuid')->label('UUID')->disabled()->columnSpan(1),
                        Select::make('event_type')->label('نوع رویداد')
                            ->options(array_combine(
                                array_column(NotificationEvent::cases(), 'value'),
                                array_map(fn($e) => $e->label(), NotificationEvent::cases())
                            ))
                            ->disabled(),
                        Select::make('channel')->label('کانال')
                            ->options(array_combine(
                                array_column(NotificationChannel::cases(), 'value'),
                                array_map(fn($c) => $c->label(), NotificationChannel::cases())
                            ))
                            ->disabled(),
                        Select::make('priority')->label('اولویت')->options([
                            'low' => 'کم',
                            'normal' => 'عادی',
                            'high' => 'بالا',
                            'urgent' => 'فوری',
                        ])->disabled(),
                    ])->columns(2),
                ]),

                Tab::make('گیرنده')->icon('heroicon-o-user')->schema([
                    Section::make('اطلاعات گیرنده')->schema([
                        Select::make('user_id')->label('کاربر')
                            ->relationship('user', 'name')
                            ->disabled(),
                        TextInput::make('recipient')->label('گیرنده (شماره/ایمیل)')->disabled(),
                        TextInput::make('recipient_name')->label('نام گیرنده')->disabled(),
                    ])->columns(3),

                    Section::make('Notifiable (Polymorphic)')->schema([
                        TextInput::make('notifiable_type')->label('نوع موجودیت')->disabled(),
                        TextInput::make('notifiable_id')->label('شناسه موجودیت')->disabled(),
                    ])->columns(2),
                ]),

                Tab::make('محتوا')->icon('heroicon-o-document-text')->schema([
                    Section::make('متن اعلان')->schema([
                        TextInput::make('subject')->label('موضوع')->disabled()->columnSpanFull(),
                        Textarea::make('content')->label('محتوا')->rows(5)->disabled()->columnSpanFull(),
                    ]),

                    Section::make('متغیرها')->schema([
                        KeyValue::make('variables')->label('متغیرهای استفاده شده')
                            ->keyLabel('کلید')
                            ->valueLabel('مقدار')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),

                    Section::make('قالب')->schema([
                        Select::make('template_id')->label('قالب استفاده شده')
                            ->relationship('template', 'name')
                            ->disabled(),
                    ]),
                ]),

                Tab::make('وضعیت و تلاش‌ها')->icon('heroicon-o-check-circle')->schema([
                    Section::make('وضعیت ارسال')->schema([
                        Select::make('status')->label('وضعیت')
                            ->options(array_combine(
                                array_column(NotificationStatus::cases(), 'value'),
                                array_map(fn($s) => $s->label(), NotificationStatus::cases())
                            ))
                            ->disabled(),
                        TextInput::make('attempts')->label('تعداد تلاش‌ها')->numeric()->disabled(),
                        TextInput::make('max_attempts')->label('حداکثر تلاش')->numeric()->disabled(),
                        DateTimePicker::make('next_retry_at')->label('تلاش بعدی')->native(false)->displayFormat('Y/m/d H:i')->disabled(),
                    ])->columns(3),

                    Section::make('خطا')->schema([
                        Textarea::make('error_message')->label('پیام خطا')->rows(3)->disabled()->columnSpanFull(),
                    ]),
                ]),

                Tab::make('Provider')->icon('heroicon-o-server')->schema([
                    Section::make('اطلاعات ارائه‌دهنده')->schema([
                        TextInput::make('provider_message_id')->label('شناسه پیام در Provider')->disabled(),
                        TextInput::make('provider_status')->label('وضعیت Provider')->disabled(),
                        Textarea::make('provider_response')->label('پاسخ Provider')->rows(4)->disabled()->columnSpanFull(),
                    ]),
                ]),

                Tab::make('زمان‌بندی')->icon('heroicon-o-clock')->schema([
                    Section::make('زمان‌های مهم')->schema([
                        DateTimePicker::make('scheduled_at')->label('زمان برنامه‌ریزی')->native(false)->displayFormat('Y/m/d H:i')->disabled(),
                        DateTimePicker::make('sent_at')->label('زمان ارسال')->native(false)->displayFormat('Y/m/d H:i')->disabled(),
                        DateTimePicker::make('delivered_at')->label('زمان تحویل')->native(false)->displayFormat('Y/m/d H:i')->disabled(),
                    ])->columns(3),

                    Section::make('متادیتا')->schema([
                        KeyValue::make('metadata')->label('متادیتای اضافی')
                            ->keyLabel('کلید')
                            ->valueLabel('مقدار')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),

                    Section::make('تاریخ‌های سیستمی')->schema([
                        Placeholder::make('created_at')->label('تاریخ ایجاد')
                            ->content(fn ($record) => $record?->created_at?->format('Y/m/d H:i') ?? '—'),
                        Placeholder::make('updated_at')->label('تاریخ بروزرسانی')
                            ->content(fn ($record) => $record?->updated_at?->format('Y/m/d H:i') ?? '—'),
                    ])->columns(2),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('کد')->searchable()->sortable()->badge()->color('primary'),
                TextColumn::make('event_type')->label('رویداد')->badge()
                    ->formatStateUsing(fn ($s) => NotificationEvent::tryFrom($s)?->label() ?? $s)
                    ->color('info')
                    ->sortable(),
                TextColumn::make('channel')->label('کانال')->badge()
                    ->formatStateUsing(fn ($s) => NotificationChannel::tryFrom($s)?->label() ?? $s)
                    ->color(fn ($s) => match ($s) {
                        'sms' => 'success',
                        'email' => 'info',
                        'internal' => 'primary',
                        'push' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('recipient')->label('گیرنده')->searchable()->limit(30),
                TextColumn::make('recipient_name')->label('نام گیرنده')->searchable()->limit(20)->toggleable(),
                TextColumn::make('subject')->label('موضوع')->limit(40)->toggleable(),
                TextColumn::make('status')->label('وضعیت')->badge()
                    ->formatStateUsing(fn ($s) => NotificationStatus::tryFrom($s)?->label() ?? $s)
                    ->color(fn ($s) => match ($s) {
                        'pending', 'queued' => 'warning',
                        'processing', 'retrying' => 'info',
                        'sent', 'delivered' => 'success',
                        'failed' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('attempts')->label('تلاش‌ها')
                    ->formatStateUsing(fn ($state, $record) => $state . '/' . $record->max_attempts)
                    ->badge()
                    ->color(fn ($state, $record) => $state >= $record->max_attempts ? 'danger' : 'info'),
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
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('sent_at')->label('زمان ارسال')->dateTime('Y/m/d H:i')->sortable()->placeholder('—'),
                TextColumn::make('delivered_at')->label('زمان تحویل')->dateTime('Y/m/d H:i')->sortable()->placeholder('—')->toggleable(),
                TextColumn::make('created_at')->label('تاریخ ایجاد')->dateTime('Y/m/d H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('channel')->label('کانال')
                    ->options(array_combine(
                        array_column(NotificationChannel::cases(), 'value'),
                        array_map(fn($c) => $c->label(), NotificationChannel::cases())
                    )),
                SelectFilter::make('status')->label('وضعیت')
                    ->options(array_combine(
                        array_column(NotificationStatus::cases(), 'value'),
                        array_map(fn($s) => $s->label(), NotificationStatus::cases())
                    )),
                SelectFilter::make('event_type')->label('نوع رویداد')
                    ->options(array_combine(
                        array_column(NotificationEvent::cases(), 'value'),
                        array_map(fn($e) => $e->label(), NotificationEvent::cases())
                    )),
                SelectFilter::make('priority')->label('اولویت')->options([
                    'low' => 'کم',
                    'normal' => 'عادی',
                    'high' => 'بالا',
                    'urgent' => 'فوری',
                ]),
                Filter::make('failed')->label('فقط ناموفق‌ها')
                    ->query(fn (Builder $q) => $q->where('status', 'failed'))
                    ->toggle(),
                Filter::make('pending')->label('در انتظار ارسال')
                    ->query(fn (Builder $q) => $q->whereIn('status', ['pending', 'queued']))
                    ->toggle(),
                Filter::make('date_range')->label('بازه تاریخ')->form([
                    \Filament\Forms\Components\DatePicker::make('from_date')->label('از تاریخ'),
                    \Filament\Forms\Components\DatePicker::make('to_date')->label('تا تاریخ'),
                ])->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['from_date'] ?? null, fn (Builder $q, $v) => $q->whereDate('created_at', '>=', $v))
                        ->when($data['to_date'] ?? null, fn (Builder $q, $v) => $q->whereDate('created_at', '<=', $v));
                }),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('retry')
                    ->label('تلاش مجدد')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('تلاش مجدد برای ارسال')
                    ->modalDescription('آیا می‌خواهید این اعلان را دوباره ارسال کنید؟')
                    ->action(function (Notification $record): void {
                        if ($record->status !== 'failed') {
                            \Filament\Notifications\Notification::make()
                                ->title('فقط اعلان‌های ناموفق قابل تلاش مجدد هستند')
                                ->warning()
                                ->send();
                            return;
                        }

                        $record->update([
                            'status' => 'queued',
                            'attempts' => 0,
                            'error_message' => null,
                            'next_retry_at' => null,
                        ]);

                        // TODO: Dispatch to queue
                        // dispatch(new SendNotificationJob($record));

                        \Filament\Notifications\Notification::make()
                            ->title('اعلان به صف ارسال اضافه شد')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Notification $record) => $record->status === 'failed'),
                Action::make('cancel')
                    ->label('لغو')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('لغو اعلان')
                    ->modalDescription('آیا مطمئن هستید؟')
                    ->action(function (Notification $record): void {
                        if (in_array($record->status, ['sent', 'delivered', 'cancelled'])) {
                            \Filament\Notifications\Notification::make()
                                ->title('این اعلان قابل لغو نیست')
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->update(['status' => 'cancelled']);

                        \Filament\Notifications\Notification::make()
                            ->title('اعلان لغو شد')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Notification $record) => !in_array($record->status, ['sent', 'delivered', 'cancelled'])),
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
            RelationManagers\LogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
            'view' => Pages\ViewNotification::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $failed = static::getModel()::where('status', 'failed')->count();
        return $failed > 0 ? (string) $failed : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }
}
