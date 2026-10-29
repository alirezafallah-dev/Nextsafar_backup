<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DocumentResource\Pages;
use App\Filament\Admin\Resources\DocumentResource\RelationManagers;
use App\Modules\Document\Models\Document;
use Illuminate\Support\Facades\Storage;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationLabel = 'مدارک';
    protected static \UnitEnum|string|null $navigationGroup = 'مدیریت ویزا';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'مدرک';
    protected static ?string $pluralModelLabel = 'مدارک';
    protected static ?string $slug = 'documents';
    protected static ?string $recordTitleAttribute = 'original_filename';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Document Tabs')->tabs([
                Tab::make('اطلاعات فایل')->icon('heroicon-o-document')->schema([
                    Section::make('شناسه فایل')->schema([
                        TextInput::make('code')->label('کد مدرک')->disabled()->columnSpan(1),
                        TextInput::make('uuid')->label('UUID')->disabled()->columnSpan(1),
                    ])->columns(2),

                    Section::make('فایل')->schema([
                        TextInput::make('original_filename')->label('نام اصلی فایل')->disabled(),
                        TextInput::make('stored_filename')->label('نام ذخیره شده')->disabled(),
                        TextInput::make('path')->label('مسیر')->disabled()->columnSpanFull(),
                    ])->columns(2),

                    Section::make('مشخصات فنی')->schema([
                        TextInput::make('disk')->label('Disk')->disabled(),
                        TextInput::make('mime_type')->label('MIME Type')->disabled(),
                        TextInput::make('extension')->label('پسوند')->disabled(),
                        TextInput::make('size')->label('حجم (بایت)')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => $state ? number_format($state) . ' bytes (' . round($state / 1024, 2) . ' KB)' : '—'),
                        TextInput::make('checksum')->label('Checksum')->disabled()->columnSpanFull(),
                    ])->columns(2),
                ]),

                Tab::make('دسته‌بندی و دسترسی')->icon('heroicon-o-tag')->schema([
                    Section::make('دسته‌بندی')->schema([
                        Select::make('category')->label('دسته‌بندی')->options([
                            'passport' => '📘 پاسپورت',
                            'photo' => '📷 عکس',
                            'bank_statement' => '💰 پرینت حساب',
                            'insurance' => '🏥 بیمه',
                            'invitation_letter' => '✉️ دعوتنامه',
                            'employment_letter' => '💼 گواهی اشتغال',
                            'other' => '📄 سایر',
                        ])->disabled(),
                        Select::make('visibility')->label('سطح دسترسی')->options([
                            'public' => '🌐 عمومی',
                            'private' => '🔒 خصوصی',
                        ])->disabled(),
                    ])->columns(2),

                    Section::make('مرتبط با')->schema([
                        TextInput::make('documentable_type')->label('نوع موجودیت')->disabled(),
                        TextInput::make('documentable_id')->label('شناسه موجودیت')->disabled(),
                        Select::make('user_id')->label('کاربر مالک')
                            ->relationship('user', 'name')
                            ->disabled(),
                        Select::make('uploaded_by')->label('آپلود کننده')
                            ->relationship('uploader', 'name')
                            ->disabled(),
                    ])->columns(2),
                ]),

                Tab::make('محتوا')->icon('heroicon-o-document-text')->schema([
                    Section::make('اطلاعات')->schema([
                        TextInput::make('title')->label('عنوان')->maxLength(255),
                        Textarea::make('description')->label('توضیحات')->rows(3)->columnSpanFull(),
                    ]),

                    Section::make('متادیتا')->schema([
                        KeyValue::make('metadata')->label('متادیتای اضافی')
                            ->keyLabel('کلید')
                            ->valueLabel('مقدار')
                            ->addActionLabel('افزودن')
                            ->reorderable()
                            ->columnSpanFull(),
                    ]),
                ]),

                Tab::make('وضعیت و امنیت')->icon('heroicon-o-shield-check')->schema([
                    Section::make('وضعیت اسکن و تأیید')->schema([
                        Select::make('scan_status')->label('وضعیت اسکن')->options([
                            'pending' => '⏳ در انتظار اسکن',
                            'clean' => '✓ پاک',
                            'infected' => '❌ آلوده',
                            'skipped' => '⚠️ رد شده',
                        ])->default('pending'),
                        Toggle::make('is_verified')->label('تأیید شده')->default(false),
                        Placeholder::make('verified_at')->label('تاریخ تأیید')
                            ->content(fn ($record) => $record?->verified_at?->format('Y/m/d H:i') ?? '—'),
                    ])->columns(3),
                ]),

                Tab::make('آمار')->icon('heroicon-o-chart-bar')->schema([
                    Section::make('آمار دانلود')->schema([
                        TextInput::make('download_count')->label('تعداد دانلود')->numeric()->disabled(),
                        Placeholder::make('last_downloaded_at')->label('آخرین دانلود')
                            ->content(fn ($record) => $record?->last_downloaded_at?->format('Y/m/d H:i') ?? '—'),
                    ])->columns(2),

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
                TextColumn::make('original_filename')->label('نام فایل')->searchable()->limit(40)->weight('bold'),
                TextColumn::make('category')->label('دسته‌بندی')->badge()
                    ->formatStateUsing(fn ($s) => match ($s) {
                        'passport' => '📘 پاسپورت',
                        'photo' => '📷 عکس',
                        'bank_statement' => '💰 پرینت',
                        'insurance' => '🏥 بیمه',
                        'invitation_letter' => '✉️ دعوتنامه',
                        'employment_letter' => '💼 اشتغال',
                        'other' => '📄 سایر',
                        default => $s,
                    })
                    ->color('info')
                    ->sortable(),
                TextColumn::make('visibility')->label('دسترسی')->badge()
                    ->formatStateUsing(fn ($s) => $s === 'public' ? '🌐 عمومی' : '🔒 خصوصی')
                    ->color(fn ($s) => $s === 'public' ? 'success' : 'warning')
                    ->sortable(),
                TextColumn::make('size')->label('حجم')
                    ->formatStateUsing(fn ($state) => $state ? round($state / 1024, 2) . ' KB' : '—')
                    ->sortable(),
                TextColumn::make('mime_type')->label('نوع')->limit(20)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.name')->label('مالک')->searchable()->sortable()->placeholder('—'),
                TextColumn::make('scan_status')->label('اسکن')->badge()
                    ->formatStateUsing(fn ($s) => match ($s) {
                        'pending' => '⏳ در انتظار',
                        'clean' => '✓ پاک',
                        'infected' => '❌ آلوده',
                        'skipped' => '⚠️ رد شده',
                        default => $s,
                    })
                    ->color(fn ($s) => match ($s) {
                        'clean' => 'success',
                        'infected' => 'danger',
                        'pending' => 'warning',
                        'skipped' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                IconColumn::make('is_verified')->label('تأیید')->boolean()
                    ->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')->falseColor('warning'),
                TextColumn::make('download_count')->label('دانلود')->badge()->color('info')->sortable(),
                TextColumn::make('created_at')->label('تاریخ ایجاد')->dateTime('Y/m/d')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')->label('دسته‌بندی')->options([
                    'passport' => '📘 پاسپورت',
                    'photo' => '📷 عکس',
                    'bank_statement' => '💰 پرینت',
                    'insurance' => '🏥 بیمه',
                    'invitation_letter' => '✉️ دعوتنامه',
                    'employment_letter' => '💼 اشتغال',
                    'other' => '📄 سایر',
                ]),
                SelectFilter::make('visibility')->label('دسترسی')->options([
                    'public' => 'عمومی',
                    'private' => 'خصوصی',
                ]),
                SelectFilter::make('scan_status')->label('وضعیت اسکن')->options([
                    'pending' => 'در انتظار',
                    'clean' => 'پاک',
                    'infected' => 'آلوده',
                    'skipped' => 'رد شده',
                ]),
                TernaryFilter::make('is_verified')->label('تأیید شده'),
                Filter::make('infected')->label('فقط آلوده')
                    ->query(fn (Builder $q) => $q->where('scan_status', 'infected'))
                    ->toggle(),
                Filter::make('not_verified')->label('تأیید نشده')
                    ->query(fn (Builder $q) => $q->where('is_verified', false))
                    ->toggle(),
                Filter::make('size_range')->label('بازه حجم')->form([
                    TextInput::make('min_size')->label('حداقل (KB)')->numeric(),
                    TextInput::make('max_size')->label('حداکثر (KB)')->numeric(),
                ])->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['min_size'] ?? null, fn (Builder $q, $v) => $q->where('size', '>=', $v * 1024))
                        ->when($data['max_size'] ?? null, fn (Builder $q, $v) => $q->where('size', '<=', $v * 1024));
                }),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('download')
                    ->label('دانلود')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->url(fn (Document $record) => \Illuminate\Support\Facades\Storage::disk($record->disk ?? 'public')->url($record->path), true)
                    ->openUrlInNewTab(),
                Action::make('mark_clean')
                    ->label('علامت پاک')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Document $record): void {
                        $record->update([
                            'scan_status' => 'clean',
                            'is_verified' => true,
                            'verified_at' => now(),
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('مدرک پاک علامت‌گذاری شد')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Document $record) => $record->scan_status !== 'clean'),
                Action::make('mark_infected')
                    ->label('علامت آلوده')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('علامت‌گذاری به عنوان آلوده')
                    ->modalDescription('آیا مطمئن هستید؟ این مدرک به عنوان آلوده علامت‌گذاری می‌شود.')
                    ->action(function (Document $record): void {
                        $record->update([
                            'scan_status' => 'infected',
                            'is_verified' => false,
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('مدرک آلوده علامت‌گذاری شد')
                            ->danger()
                            ->send();
                    })
                    ->visible(fn (Document $record) => $record->scan_status !== 'infected'),
                Action::make('generate_temp_link')
                    ->label('تولید لینک موقت')
                    ->icon('heroicon-o-link')
                    ->color('warning')
                    ->form([
                        TextInput::make('expires_in_minutes')->label('انقضا (دقیقه)')->numeric()->default(60)->required(),
                        Textarea::make('reason')->label('دلیل تولید')->rows(2),
                    ])
                    ->action(function (Document $record, array $data): void {
                        // TODO: Implement temp link generation
                        \Filament\Notifications\Notification::make()
                            ->title('لینک موقت تولید شد')
                            ->body('لینک به مدت ' . $data['expires_in_minutes'] . ' دقیقه معتبر است')
                            ->success()
                            ->send();
                    }),
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
            RelationManagers\AccessLogsRelationManager::class,
            RelationManagers\TempLinksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'view' => Pages\ViewDocument::route('/{record}'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $infected = static::getModel()::where('scan_status', 'infected')->count();
        return $infected > 0 ? (string) $infected : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }
}
