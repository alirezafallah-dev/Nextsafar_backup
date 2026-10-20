<?php

namespace App\Filament\Admin\Resources\OrderResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PassengersRelationManager extends RelationManager
{
    protected static string $relationship = 'passengers';

    protected static ?string $title = 'مسافران';
    protected static ?string $modelLabel = 'مسافر';
    protected static ?string $pluralModelLabel = 'مسافران';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('type')->label('نوع مسافر')->options([
                'adult' => '👤 بزرگسال',
                'child' => '👦 کودک',
                'infant' => '👶 نوزاد',
            ])->required(),
            TextInput::make('first_name')->label('نام')->required()->maxLength(100),
            TextInput::make('last_name')->label('نام خانوادگی')->required()->maxLength(100),
            TextInput::make('national_id')->label('کد ملی')->maxLength(20),
            TextInput::make('passport_number')->label('شماره پاسپورت')->maxLength(50),
            DatePicker::make('birth_date')->label('تاریخ تولد')->native(false)->displayFormat('Y/m/d'),
            Select::make('gender')->label('جنسیت')->options([
                'male' => 'مرد',
                'female' => 'زن',
            ]),
            TextInput::make('phone')->label('موبایل')->tel()->maxLength(20),
            TextInput::make('email')->label('ایمیل')->email()->maxLength(255),
            TextInput::make('price')->label('قیمت')->numeric()->prefix('IRR')->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')->label('نوع')->badge()
                    ->formatStateUsing(fn ($s) => match ($s) {
                        'adult' => '👤 بزرگسال',
                        'child' => '👦 کودک',
                        'infant' => '👶 نوزاد',
                        default => $s,
                    })
                    ->color(fn ($s) => match ($s) {
                        'adult' => 'primary',
                        'child' => 'warning',
                        'infant' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('first_name')->label('نام')->searchable(),
                TextColumn::make('last_name')->label('نام خانوادگی')->searchable(),
                TextColumn::make('national_id')->label('کد ملی')->searchable()->copyable()->placeholder('—'),
                TextColumn::make('passport_number')->label('پاسپورت')->searchable()->copyable()->placeholder('—')->toggleable(),
                TextColumn::make('birth_date')->label('تاریخ تولد')->date('Y/m/d')->toggleable(),
                TextColumn::make('gender')->label('جنسیت')->badge()
                    ->formatStateUsing(fn ($s) => $s === 'male' ? 'مرد' : 'زن')
                    ->color(fn ($s) => $s === 'male' ? 'info' : 'pink')
                    ->toggleable(),
                TextColumn::make('phone')->label('موبایل')->searchable()->copyable()->placeholder('—'),
                TextColumn::make('price')->label('قیمت')->money('IRR', true)->color('success'),
            ])
            ->headerActions([
                CreateAction::make()->label('افزودن مسافر'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
