<?php

namespace App\Filament\Admin\Resources\TourResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HotelsRelationManager extends RelationManager
{
    protected static string $relationship = 'hotels';

    protected static ?string $title = 'هتل‌های تور';
    protected static ?string $modelLabel = 'هتل';
    protected static ?string $pluralModelLabel = 'هتل‌های مرتبط';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('hotel_id')->label('هتل')
                ->relationship('hotel', 'name')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('relation_type')->label('نوع ارتباط')->options([
                'main' => '🏨 اصلی',
                'alternative' => '🔄 جایگزین',
                'optional' => '⚙️ اختیاری',
            ])->default('main'),
            TextInput::make('priority')->label('اولویت')->numeric()->default(1)->minValue(1),
            Textarea::make('notes')->label('توضیحات')->rows(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام هتل')->searchable()->sortable()->weight('bold'),
                TextColumn::make('city')->label('شهر')->searchable()->placeholder('—'),
                TextColumn::make('pivot.relation_type')->label('نوع ارتباط')->badge()
                    ->formatStateUsing(fn ($s) => match ($s) {
                        'main' => '🏨 اصلی',
                        'alternative' => '🔄 جایگزین',
                        'optional' => '⚙️ اختیاری',
                        default => $s,
                    })
                    ->color(fn ($s) => match ($s) {
                        'main' => 'success',
                        'alternative' => 'warning',
                        'optional' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('pivot.priority')->label('اولویت')->badge()->color('info')->sortable(),
                TextColumn::make('pivot.notes')->label('توضیحات')->limit(30)->placeholder('—'),
            ])
            ->headerActions([
                AttachAction::make()->label('افزودن هتل')
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action) => [
                        Select::make('relation_type')->label('نوع ارتباط')->options([
                            'main' => '🏨 اصلی',
                            'alternative' => '🔄 جایگزین',
                            'optional' => '⚙️ اختیاری',
                        ])->default('main')->required(),
                        TextInput::make('priority')->label('اولویت')->numeric()->default(1)->required(),
                        Textarea::make('notes')->label('توضیحات')->rows(2),
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DetachAction::make()->label('حذف ارتباط'),
            ])
            ->defaultSort('pivot.priority', 'asc');
    }
}
