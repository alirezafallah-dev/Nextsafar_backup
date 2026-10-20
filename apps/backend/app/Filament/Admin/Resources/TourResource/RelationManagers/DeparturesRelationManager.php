<?php

namespace App\Filament\Admin\Resources\TourResource\RelationManagers;

use App\Modules\Tour\Models\TourDeparture;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DeparturesRelationManager extends RelationManager
{
    protected static string $relationship = 'departures';

    protected static ?string $title = 'تاریخ‌های حرکت';
    protected static ?string $modelLabel = 'تاریخ حرکت';
    protected static ?string $pluralModelLabel = 'تاریخ‌های حرکت';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            DatePicker::make('departure_date')->label('تاریخ حرکت')->required()->native(false)->displayFormat('Y/m/d'),
            DatePicker::make('return_date')->label('تاریخ برگشت')->native(false)->displayFormat('Y/m/d'),
            TextInput::make('total_capacity')->label('ظرفیت کل')->numeric()->required()->minValue(1)->default(20),
            TextInput::make('available_seats')->label('ظرفیت باقی‌مانده')->numeric()->required()->minValue(0),
            TextInput::make('price_adult')->label('قیمت بزرگسال')->numeric()->required()->prefix('IRR'),
            TextInput::make('price_child')->label('قیمت کودک')->numeric()->default(0)->prefix('IRR'),
            TextInput::make('price_infant')->label('قیمت نوزاد')->numeric()->default(0)->prefix('IRR'),
            TextInput::make('price_single')->label('قیمت تک‌نفره')->numeric()->default(0)->prefix('IRR'),
            Select::make('status')->label('وضعیت')->options([
                'open' => '🟢 باز',
                'guaranteed' => '✓ تضمین شده',
                'full' => '🔴 تکمیل',
                'cancelled' => '❌ لغو شده',
            ])->default('open'),
            Textarea::make('notes')->label('توضیحات')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('departure_date')->label('حرکت')->date('Y/m/d')->sortable()->weight('bold'),
                TextColumn::make('return_date')->label('برگشت')->date('Y/m/d')->sortable()->toggleable(),
                TextColumn::make('total_capacity')->label('ظرفیت کل')->badge()->color('info'),
                TextColumn::make('available_seats')->label('باقی‌مانده')
                    ->badge()
                    ->color(fn ($state) => $state > 5 ? 'success' : ($state > 0 ? 'warning' : 'danger')),
                TextColumn::make('price_adult')->label('بزرگسال')->money('IRR', true)->weight('bold'),
                TextColumn::make('price_child')->label('کودک')->money('IRR', true)->toggleable(),
                TextColumn::make('price_infant')->label('نوزاد')->money('IRR', true)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')->label('وضعیت')->badge()
                    ->formatStateUsing(fn ($s) => match ($s) {
                        'open' => '🟢 باز',
                        'guaranteed' => '✓ تضمین شده',
                        'full' => '🔴 تکمیل',
                        'cancelled' => '❌ لغو',
                        default => $s,
                    })
                    ->color(fn ($s) => match ($s) {
                        'open', 'guaranteed' => 'success',
                        'full' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->headerActions([
                CreateAction::make()->label('افزودن تاریخ حرکت'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('departure_date', 'asc');
    }
}
