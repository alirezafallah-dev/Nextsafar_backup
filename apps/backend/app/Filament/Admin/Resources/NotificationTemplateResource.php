<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NotificationTemplateResource\Pages;
use App\Modules\Notification\Enums\NotificationChannel;
use App\Modules\Notification\Enums\NotificationEvent;
use App\Modules\Notification\Models\NotificationTemplate;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class NotificationTemplateResource extends Resource
{
    protected static ?string $model = NotificationTemplate::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'قالب‌های اعلان';
    protected static \UnitEnum|string|null $navigationGroup = 'مدیریت اطلاع‌رسانی';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'قالب';
    protected static ?string $pluralModelLabel = 'قالب‌های اعلان';
    protected static ?string $slug = 'notification-templates';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Template Tabs')->tabs([
                Tab::make('شناسه')->icon('heroicon-o-tag')->schema([
                    Section::make('اطلاعات قالب')->schema([
                        TextInput::make('code')->label('کد قالب')->disabled()->columnSpan(1),
                        TextInput::make('name')->label('نام قالب')->required()->maxLength(255)->columnSpan(1),
                        Select::make('event_type')->label('نوع رویداد')
                            ->options(array_combine(
                                array_column(NotificationEvent::cases(), 'value'),
                                array_map(fn($e) => $e->label(), NotificationEvent::cases())
                            ))
                            ->required()
                            ->disabled(fn ($record) => $record && $record->is_system),
                        Select::make('channel')->label('کانال')
                            ->options(array_combine(
                                array_column(NotificationChannel::cases(), 'value'),
                                array_map(fn($c) => $c->label(), NotificationChannel::cases())
                            ))
                            ->required()
                            ->disabled(fn ($record) => $record && $record->is_system),
                        TextInput::make('locale')->label('زبان')->default('fa')->maxLength(10),
                    ])->columns(2),

                    Section::make('توضیحات')->schema([
                        Textarea::make('description')->label('توضیحات قالب')->rows(3)->columnSpanFull(),
                    ]),
                ]),

                Tab::make('محتوا')->icon('heroicon-o-document-text')->schema([
                    Section::make('متن قالب')->schema([
                        TextInput::make('subject')->label('موضوع (برای ایمیل)')->maxLength(255)->columnSpanFull()
                            ->helperText('از متغیرها استفاده کنید: {{user.name}}, {{order.code}}'),
                        RichEditor::make('content')->label('محتوای قالب')->columnSpanFull()
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike', 'link',
                                'h2', 'h3', 'blockquote',
                                'orderedList', 'bulletList',
                                'undo', 'redo',
                            ])
                            ->helperText('متغیرهای مجاز: {{user.name}}, {{order.code}}, {{payment.amount}}, و غیره'),
                    ]),

                    Section::make('متغیرها')->schema([
                        KeyValue::make('variables')->label('متغیرهای پیش‌فرض')
                            ->keyLabel('کلید')
                            ->valueLabel('توضیحات')
                            ->addActionLabel('افزودن متغیر')
                            ->reorderable()
                            ->columnSpanFull(),
                    ]),
                ]),

                Tab::make('تنظیمات')->icon('heroicon-o-cog-6-tooth')->schema([
                    Section::make('وضعیت')->schema([
                        Toggle::make('is_active')->label('فعال است')->default(true)
                            ->helperText('قالب‌های غیرفعال در ارسال اعلان استفاده نمی‌شوند'),
                        Toggle::make('is_system')->label('قالب سیستمی')->disabled()
                            ->helperText('قالب‌های سیستمی قابل حذف نیستند'),
                    ])->columns(2),

                    Section::make('متادیتا')->schema([
                        KeyValue::make('metadata')->label('متادیتای اضافی')
                            ->keyLabel('کلید')
                            ->valueLabel('مقدار')
                            ->reorderable()
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
                TextColumn::make('name')->label('نام قالب')->searchable()->sortable()->limit(30)->weight('bold'),
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
                TextColumn::make('locale')->label('زبان')->badge()->color('gray')->sortable(),
                IconColumn::make('is_active')->label('فعال')->boolean()
                    ->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')->falseColor('danger'),
                IconColumn::make('is_system')->label('سیستمی')->boolean()
                    ->trueIcon('heroicon-o-lock-closed')->falseIcon('heroicon-o-lock-open')
                    ->trueColor('warning')->falseColor('gray'),
                TextColumn::make('description')->label('توضیحات')->limit(50)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('تاریخ ایجاد')->dateTime('Y/m/d')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('event_type')->label('نوع رویداد')
                    ->options(array_combine(
                        array_column(NotificationEvent::cases(), 'value'),
                        array_map(fn($e) => $e->label(), NotificationEvent::cases())
                    )),
                SelectFilter::make('channel')->label('کانال')
                    ->options(array_combine(
                        array_column(NotificationChannel::cases(), 'value'),
                        array_map(fn($c) => $c->label(), NotificationChannel::cases())
                    )),
                TernaryFilter::make('is_active')->label('وضعیت فعال'),
                TernaryFilter::make('is_system')->label('قالب سیستمی'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (NotificationTemplate $record) => !$record->is_system),
                Action::make('toggle_active')
                    ->label(fn (NotificationTemplate $record) => $record->is_active ? 'غیرفعال کردن' : 'فعال کردن')
                    ->icon(fn (NotificationTemplate $record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (NotificationTemplate $record) => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function (NotificationTemplate $record): void {
                        $record->update(['is_active' => !$record->is_active]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('وضعیت قالب تغییر کرد')
                            ->body($record->is_active ? 'قالب فعال شد' : 'قالب غیرفعال شد')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (NotificationTemplate $record) => !$record->is_system),
                Action::make('preview')
                    ->label('پیش‌نمایش')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading('پیش‌نمایش قالب')
                    ->modalContent(fn (NotificationTemplate $record) => view('filament.admin.notifications.template-preview', [
                        'template' => $record,
                    ]))
                    ->modalSubmitAction(false),
            ])
            ->bulkActions([
                BulkActionGroup::make([]),
            ])
            ->defaultSort('event_type', 'asc')
            ->striped()
            ->persistFiltersInSession();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotificationTemplates::route('/'),
            'view' => Pages\ViewNotificationTemplate::route('/{record}'),
            'edit' => Pages\EditNotificationTemplate::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $inactive = static::getModel()::where('is_active', false)->count();
        return $inactive > 0 ? (string) $inactive : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}
