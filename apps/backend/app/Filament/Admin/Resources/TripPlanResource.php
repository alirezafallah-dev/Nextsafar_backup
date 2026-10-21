<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TripPlanResource\Pages;
use App\Modules\TripPlanner\Models\TripPlan;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
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
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TripPlanResource extends Resource
{
    protected static ?string $model = TripPlan::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'برنامه‌های سفر AI';
    protected static \UnitEnum|string|null $navigationGroup = 'هوش مصنوعی';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'برنامه سفر';
    protected static ?string $pluralModelLabel = 'برنامه‌های سفر AI';
    protected static ?string $slug = 'trip-plans';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Trip Plan')->tabs([
                Tab::make('اطلاعات ورودی')->icon('heroicon-o-document-text')->schema([
                    Section::make('مقصد و زمان')->schema([
                        TextInput::make('destination')->label('مقصد')->disabled(),
                        TextInput::make('country')->label('کشور')->disabled(),
                        TextInput::make('days')->label('تعداد روز')->numeric()->disabled(),
                        TextInput::make('travelers')->label('تعداد مسافران')->numeric()->disabled(),
                        DatePicker::make('start_date')->label('تاریخ شروع')->disabled(),
                        Select::make('budget_level')->label('سطح بودجه')
                            ->options([
                                'economy' => '💰 اقتصادی',
                                'medium' => '💵 متوسط',
                                'luxury' => '💎 لوکس',
                            ])->disabled(),
                    ])->columns(2),

                    Section::make('علاقه‌مندی‌ها')->schema([
                        Textarea::make('interests')->label('علاقه‌مندی‌ها')
                            ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
                ]),

                Tab::make('خروجی AI')->icon('heroicon-o-sparkles')->schema([
                    Section::make('اطلاعات کلی')->schema([
                        TextInput::make('trip_title')->label('عنوان سفر')->disabled()->columnSpanFull(),
                        Textarea::make('trip_summary')->label('خلاصه سفر')->rows(3)->disabled()->columnSpanFull(),
                    ]),

                    Section::make('برنامه روزانه (JSON)')->schema([
                        Textarea::make('days_plan')->label('برنامه روزانه')
                            ->rows(15)
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state)
                            ->disabled()
                            ->columnSpanFull(),
                    ]),

                    Section::make('پیشنهادهای AI')->schema([
                        Textarea::make('suggested_hotels')->label('هتل‌های پیشنهادی')
                            ->rows(5)
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state)
                            ->disabled()
                            ->columnSpanFull(),
                        Textarea::make('suggested_restaurants')->label('رستوران‌های پیشنهادی')
                            ->rows(5)
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state)
                            ->disabled()
                            ->columnSpanFull(),
                        Textarea::make('suggested_tours')->label('تورهای پیشنهادی')
                            ->rows(5)
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state)
                            ->disabled()
                            ->columnSpanFull(),
                    ]),

                    Section::make('بودجه')->schema([
                        TextInput::make('total_budget_min')->label('حداقل بودجه (USD)')->numeric()->disabled(),
                        TextInput::make('total_budget_max')->label('حداکثر بودجه (USD)')->numeric()->disabled(),
                        TextInput::make('currency')->label('واحد پول')->disabled(),
                    ])->columns(3),
                ]),

                Tab::make('متا')->icon('heroicon-o-cog-6-tooth')->schema([
                    Section::make('اطلاعات تولید')->schema([
                        Select::make('status')->label('وضعیت')
                            ->options([
                                'pending' => '⏳ در انتظار',
                                'completed' => '✅ تکمیل',
                                'failed' => '❌ ناموفق',
                                'expired' => '⚠️ منقضی',
                            ])->disabled(),
                        TextInput::make('model_used')->label('مدل AI استفاده شده')->disabled(),
                        TextInput::make('tokens_used')->label('توکن مصرفی')->numeric()->disabled(),
                        TextInput::make('generation_time_ms')->label('زمان تولید (ms)')->numeric()->disabled(),
                        TextInput::make('attempts')->label('تعداد تلاش‌ها')->numeric()->disabled(),
                    ])->columns(2),

                    Section::make('کاربر و بازدید')->schema([
                        Select::make('user_id')->label('کاربر')
                            ->relationship('user', 'name')
                            ->disabled(),
                        TextInput::make('session_id')->label('Session ID')->disabled(),
                        TextInput::make('ip_address')->label('IP')->disabled(),
                        TextInput::make('view_count')->label('تعداد بازدید')->numeric()->disabled(),
                    ])->columns(2),

                    Section::make('خطا')->schema([
                        Textarea::make('error_message')->label('پیام خطا')->rows(3)->disabled()->columnSpanFull(),
                    ]),

                    Section::make('تاریخ‌ها')->schema([
                        Placeholder::make('created_at')->label('تاریخ ایجاد')
                            ->content(fn ($record) => $record?->created_at?->format('Y/m/d H:i') ?? '—'),
                        Placeholder::make('expires_at')->label('تاریخ انقضا')
                            ->content(fn ($record) => $record?->expires_at?->format('Y/m/d H:i') ?? '—'),
                        Placeholder::make('last_viewed_at')->label('آخرین بازدید')
                            ->content(fn ($record) => $record?->last_viewed_at?->format('Y/m/d H:i') ?? '—'),
                    ])->columns(3),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('destination')
                    ->label('مقصد')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('country')
                    ->label('کشور')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('days')
                    ->label('روز')
                    ->sortable()
                    ->suffix(' روز')
                    ->badge()
                    ->color('info'),

                BadgeColumn::make('budget_level')
                    ->label('بودجه')
                    ->colors([
                        'success' => 'economy',
                        'warning' => 'medium',
                        'danger' => 'luxury',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'economy' => '💰 اقتصادی',
                        'medium' => '💵 متوسط',
                        'luxury' => '💎 لوکس',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('trip_title')
                    ->label('عنوان')
                    ->limit(40)
                    ->searchable()
                    ->toggleable(),

                BadgeColumn::make('status')
                    ->label('وضعیت')
                    ->colors([
                        'secondary' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'warning' => 'expired',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check-circle' => 'completed',
                        'heroicon-o-x-circle' => 'failed',
                        'heroicon-o-exclamation' => 'expired',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending' => '⏳ در انتظار',
                        'completed' => '✅ تکمیل',
                        'failed' => '❌ ناموفق',
                        'expired' => '⚠️ منقضی',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('model_used')
                    ->label('مدل AI')
                    ->badge()
                    ->color('purple')
                    ->toggleable(),

                TextColumn::make('tokens_used')
                    ->label('توکن')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state))
                    ->toggleable(),

                TextColumn::make('generation_time_ms')
                    ->label('زمان')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state / 1000, 2) . 's')
                    ->toggleable(),

                TextColumn::make('view_count')
                    ->label('بازدید')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('user.name')
                    ->label('کاربر')
                    ->placeholder('مهمان')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('تاریخ')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار',
                        'completed' => 'تکمیل شده',
                        'failed' => 'ناموفق',
                        'expired' => 'منقضی',
                    ]),
                SelectFilter::make('budget_level')
                    ->label('سطح بودجه')
                    ->options([
                        'economy' => 'اقتصادی',
                        'medium' => 'متوسط',
                        'luxury' => 'لوکس',
                    ]),
                Filter::make('created_today')
                    ->label('ایجاد شده امروز')
                    ->query(fn (Builder $query) => $query->whereDate('created_at', today()))
                    ->toggle(),
                Filter::make('high_token')
                    ->label('مصرف بالای ۲۰۰۰ توکن')
                    ->query(fn (Builder $query) => $query->where('tokens_used', '>', 2000))
                    ->toggle(),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('delete')
                    ->label('حذف')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->delete()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTripPlans::route('/'),
            'view' => Pages\ViewTripPlan::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
}
