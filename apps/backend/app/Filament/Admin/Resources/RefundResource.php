<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RefundResource\Pages;
use App\Modules\Payment\Models\Refund;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
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

class RefundResource extends Resource
{
    protected static ?string $model = Refund::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-arrow-uturn-left';
    protected static ?string $navigationLabel = 'استردادها';
    protected static \UnitEnum|string|null $navigationGroup = 'مدیریت مالی';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'استرداد';
    protected static ?string $pluralModelLabel = 'استردادها';
    protected static ?string $slug = 'refunds';
    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Refund Tabs')->tabs([
                Tab::make('اطلاعات اصلی')->icon('heroicon-o-document-text')->schema([
                    Section::make('شناسه و وضعیت')->schema([
                        TextInput::make('code')->label('کد استرداد')->disabled()->columnSpan(1),
                        Select::make('type')->label('نوع استرداد')->options(self::getTypeOptions())->disabled(),
                        Select::make('status')->label('وضعیت')->options(self::getStatusOptions())->disabled(),
                        Select::make('currency')->label('واحد پول')->default('IRR')->disabled(),
                    ])->columns(2),

                    Section::make('دلایل')->schema([
                        Textarea::make('reason')->label('دلیل درخواست')->rows(3)->columnSpanFull()->disabled(),
                        Textarea::make('penalty_reason')->label('دلیل جریمه')->rows(2)->columnSpanFull()->disabled(),
                        Textarea::make('rejection_reason')->label('دلیل رد')->rows(2)->columnSpanFull()->disabled(),
                    ]),
                ]),

                Tab::make('مبالغ')->icon('heroicon-o-currency-dollar')->schema([
                    Section::make('محاسبه استرداد')->schema([
                        TextInput::make('original_amount')->label('مبلغ اصلی')->numeric()->prefix('IRR')->disabled(),
                        TextInput::make('penalty_amount')->label('مبلغ جریمه')->numeric()->prefix('IRR')->disabled()->color('danger'),
                        TextInput::make('refund_amount')->label('مبلغ استرداد')->numeric()->prefix('IRR')->disabled()->color('success')->weight('bold'),
                        TextInput::make('amount')->label('مبلغ درخواستی')->numeric()->prefix('IRR')->disabled(),
                    ])->columns(2),
                ]),

                Tab::make('ارتباطات')->icon('heroicon-o-link')->schema([
                    Section::make('مربوط به')->schema([
                        Select::make('user_id')->label('کاربر')
                            ->relationship('user', 'name')
                            ->disabled(),
                        Select::make('order_id')->label('سفارش')
                            ->relationship('order', 'code')
                            ->disabled(),
                        Select::make('payment_id')->label('پرداخت')
                            ->relationship('payment', 'code')
                            ->disabled(),
                        Select::make('booking_id')->label('رزرو')
                            ->relationship('booking', 'code')
                            ->disabled()
                            ->nullable(),
                    ])->columns(2),
                ]),

                Tab::make('Approvals')->icon('heroicon-o-check-circle')->schema([
                    Section::make('تأیید')->schema([
                        Select::make('approved_by')->label('تأییدکننده')
                            ->relationship('approver', 'name')
                            ->disabled(),
                        DateTimePicker::make('approved_at')->label('تاریخ تأیید')->native(false)->displayFormat('Y/m/d H:i')->disabled(),
                    ])->columns(2),

                    Section::make('رد')->schema([
                        Select::make('rejected_by')->label('ردکننده')
                            ->relationship('rejector', 'name')
                            ->disabled(),
                        DateTimePicker::make('rejected_at')->label('تاریخ رد')->native(false)->displayFormat('Y/m/d H:i')->disabled(),
                    ])->columns(2),
                ]),

                Tab::make('پردازش')->icon('heroicon-o-cog-6-tooth')->schema([
                    Section::make('پردازش استرداد')->schema([
                        Select::make('processed_by')->label('پردازش‌کننده')
                            ->relationship('processor', 'name')
                            ->disabled(),
                        DateTimePicker::make('processed_at')->label('تاریخ پردازش')->native(false)->displayFormat('Y/m/d H:i')->disabled(),
                        TextInput::make('gateway_ref_id')->label('مرجع درگاه')->disabled()->maxLength(100),
                    ])->columns(2),

                    Section::make('زمان‌ها')->schema([
                        DateTimePicker::make('requested_at')->label('تاریخ درخواست')->native(false)->displayFormat('Y/m/d H:i')->disabled(),
                        Placeholder::make('created_at')->label('تاریخ ایجاد')
                            ->content(fn ($record) => $record?->created_at?->format('Y/m/d H:i') ?? '—'),
                        Placeholder::make('updated_at')->label('تاریخ بروزرسانی')
                            ->content(fn ($record) => $record?->updated_at?->format('Y/m/d H:i') ?? '—'),
                    ])->columns(2),
                ]),

                Tab::make('یادداشت‌ها')->icon('heroicon-o-chat-bubble-bottom-center-text')->schema([
                    Section::make('یادداشت‌های ادمین')->schema([
                        Textarea::make('admin_notes')->label('یادداشت‌های ادمین')->rows(5)->columnSpanFull(),
                    ]),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('کد استرداد')->searchable()->sortable()->badge()->color('primary')->weight('bold'),
                TextColumn::make('user.name')->label('کاربر')->searchable()->sortable()->limit(20),
                TextColumn::make('order.code')->label('سفارش')->searchable()->sortable()->badge()->color('info'),
                TextColumn::make('payment.code')->label('پرداخت')->searchable()->sortable()->badge()->color('gray')->toggleable(),
                TextColumn::make('type')->label('نوع')->badge()
                    ->formatStateUsing(fn ($s) => self::getTypeOptions()[$s] ?? $s)
                    ->color(fn ($s) => self::getTypeColor($s))
                    ->sortable(),
                TextColumn::make('original_amount')->label('مبلغ اصلی')->money('IRR', true)->sortable(),
                TextColumn::make('penalty_amount')->label('جریمه')->money('IRR', true)->sortable()->color('danger'),
                TextColumn::make('refund_amount')->label('مبلغ استرداد')->money('IRR', true)->sortable()->color('success')->weight('bold'),
                TextColumn::make('status')->label('وضعیت')->badge()
                    ->formatStateUsing(fn ($s) => self::getStatusOptions()[$s] ?? $s)
                    ->color(fn ($s) => self::getStatusColor($s))
                    ->sortable(),
                TextColumn::make('approver.name')->label('تأییدکننده')->placeholder('—')->toggleable(),
                TextColumn::make('processed_at')->label('تاریخ پردازش')->dateTime('Y/m/d H:i')->sortable()->toggleable()->placeholder('—'),
                TextColumn::make('created_at')->label('تاریخ ایجاد')->dateTime('Y/m/d')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->label('وضعیت')->options(self::getStatusOptions()),
                SelectFilter::make('type')->label('نوع')->options(self::getTypeOptions()),
                Filter::make('amount_range')->label('بازه مبلغ')->form([
                    TextInput::make('min_amount')->label('از مبلغ')->numeric()->prefix('IRR'),
                    TextInput::make('max_amount')->label('تا مبلغ')->numeric()->prefix('IRR'),
                ])->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['min_amount'] ?? null, fn (Builder $q, $v) => $q->where('refund_amount', '>=', $v))
                        ->when($data['max_amount'] ?? null, fn (Builder $q, $v) => $q->where('refund_amount', '<=', $v));
                }),
                Filter::make('pending_approval')->label('در انتظار تأیید')
                    ->query(fn (Builder $q) => $q->where('status', 'pending'))
                    ->toggle(),
                Filter::make('approved')->label('تأیید شده')
                    ->query(fn (Builder $q) => $q->where('status', 'approved'))
                    ->toggle(),
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
                Action::make('approve')
                    ->label('تأیید')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('تأیید استرداد')
                    ->modalDescription('آیا از تأیید این استرداد اطمینان دارید؟')
                    ->form([
                        Textarea::make('approval_notes')->label('یادداشت تأیید')->rows(2),
                    ])
                    ->action(function (Refund $record, array $data): void {
                        // TODO: Call RefundService::approve()
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'admin_notes' => ($record->admin_notes ? $record->admin_notes . "\n" : '') . 
                                '[' . now()->format('Y-m-d H:i') . '] تأیید شد: ' . ($data['approval_notes'] ?? ''),
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('استرداد تأیید شد')
                            ->body('استرداد ' . $record->code . ' با موفقیت تأیید شد')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Refund $record) => $record->status === 'pending'),
                Action::make('reject')
                    ->label('رد')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('رد استرداد')
                    ->modalDescription('لطفاً دلیل رد را وارد کنید')
                    ->form([
                        Textarea::make('rejection_reason')->label('دلیل رد')->required()->rows(3),
                    ])
                    ->action(function (Refund $record, array $data): void {
                        // TODO: Call RefundService::reject()
                        $record->update([
                            'status' => 'rejected',
                            'rejected_by' => auth()->id(),
                            'rejected_at' => now(),
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('استرداد رد شد')
                            ->body('استرداد ' . $record->code . ' رد شد')
                            ->danger()
                            ->send();
                    })
                    ->visible(fn (Refund $record) => $record->status === 'pending'),
                Action::make('process')
                    ->label('پردازش')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('شروع پردازش استرداد')
                    ->modalDescription('آیا می‌خواهید پردازش استرداد را شروع کنید؟')
                    ->action(function (Refund $record): void {
                        // TODO: Call RefundService::process()
                        $record->update([
                            'status' => 'processing',
                            'processed_by' => auth()->id(),
                            'processed_at' => now(),
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('پردازش شروع شد')
                            ->body('استرداد ' . $record->code . ' در حال پردازش است')
                            ->info()
                            ->send();
                    })
                    ->visible(fn (Refund $record) => $record->status === 'approved'),
                Action::make('complete')
                    ->label('تکمیل')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('تکمیل استرداد')
                    ->modalDescription('آیا استرداد با موفقیت تکمیل شد؟')
                    ->form([
                        TextInput::make('gateway_ref_id')->label('مرجع درگاه')->maxLength(100),
                    ])
                    ->action(function (Refund $record, array $data): void {
                        // TODO: Call RefundService::complete()
                        $record->update([
                            'status' => 'completed',
                            'gateway_ref_id' => $data['gateway_ref_id'] ?? null,
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('استرداد تکمیل شد')
                            ->body('استرداد ' . $record->code . ' با موفقیت تکمیل شد')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Refund $record) => $record->status === 'processing'),
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
            // در آینده می‌توانیم ApprovalsRelationManager و TransactionsRelationManager اضافه کنیم
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRefunds::route('/'),
            'create' => Pages\CreateRefund::route('/create'),
            'view' => Pages\ViewRefund::route('/{record}'),
            'edit' => Pages\EditRefund::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::where('status', 'pending')->count();
        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    protected static function getStatusOptions(): array
    {
        return [
            'pending' => '⏳ در انتظار تأیید',
            'approved' => '✓ تأیید شده',
            'rejected' => '❌ رد شده',
            'processing' => '⚙️ در حال پردازش',
            'completed' => '✅ تکمیل شده',
            'cancelled' => '🚫 لغو شده',
            'failed' => '⚠️ ناموفق',
        ];
    }

    protected static function getStatusColor($status): string
    {
        return match ($status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'processing' => 'info',
            'completed' => 'success',
            'cancelled' => 'gray',
            'failed' => 'danger',
            default => 'gray',
        };
    }

    protected static function getTypeOptions(): array
    {
        return [
            'full' => '💯 استرداد کامل',
            'partial' => '📊 استرداد جزئی',
            'penalty' => '⚖️ با جریمه',
        ];
    }

    protected static function getTypeColor($type): string
    {
        return match ($type) {
            'full' => 'success',
            'partial' => 'info',
            'penalty' => 'warning',
            default => 'gray',
        };
    }
}
