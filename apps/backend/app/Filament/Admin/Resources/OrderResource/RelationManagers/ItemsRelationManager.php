<?php

namespace App\Filament\Admin\Resources\OrderResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'آیتم‌های سفارش';
    protected static ?string $modelLabel = 'آیتم';
    protected static ?string $pluralModelLabel = 'آیتم‌ها';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('item_type')->label('نوع آیتم')->options([
                'tour' => '🌍 تور',
                'hotel' => '🏨 هتل',
                'flight' => '✈️ پرواز',
                'visa' => '🛂 ویزا',
                'service' => '⚙️ خدمات',
                'other' => '📦 سایر',
            ])->required(),
            TextInput::make('item_title')->label('عنوان آیتم')->required()->maxLength(255),
            TextInput::make('quantity')->label('تعداد')->numeric()->required()->default(1)->minValue(1),
            TextInput::make('unit_price')->label('قیمت واحد')->numeric()->required()->prefix('IRR'),
            TextInput::make('discount')->label('تخفیف')->numeric()->default(0)->prefix('IRR'),
            Textarea::make('description')->label('توضیحات')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item_type')->label('نوع')->badge()
                    ->formatStateUsing(fn ($s) => match ($s) {
                        'tour' => '🌍 تور',
                        'hotel' => '🏨 هتل',
                        'flight' => '✈️ پرواز',
                        'visa' => '🛂 ویزا',
                        'service' => '⚙️ خدمات',
                        'other' => '📦 سایر',
                        default => $s,
                    })
                    ->color(fn ($s) => match ($s) {
                        'tour' => 'warning',
                        'hotel' => 'success',
                        'flight' => 'info',
                        'visa' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('item_title')->label('عنوان')->searchable()->limit(30),
                TextColumn::make('quantity')->label('تعداد')->badge()->color('info'),
                TextColumn::make('unit_price')->label('قیمت واحد')->money('IRR', true),
                TextColumn::make('discount')->label('تخفیف')->money('IRR', true)->color('danger'),
                TextColumn::make('total_price')->label('قیمت کل')->money('IRR', true)->weight('bold')->color('success'),
            ])
            ->headerActions([
                CreateAction::make()->label('افزودن آیتم'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
