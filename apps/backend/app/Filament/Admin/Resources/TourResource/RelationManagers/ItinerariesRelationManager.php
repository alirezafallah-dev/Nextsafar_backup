<?php

namespace App\Filament\Admin\Resources\TourResource\RelationManagers;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItinerariesRelationManager extends RelationManager
{
    protected static string $relationship = 'itineraries';

    protected static ?string $title = 'برنامه سفر روزانه';
    protected static ?string $modelLabel = 'روز';
    protected static ?string $pluralModelLabel = 'برنامه سفر';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('day_number')->label('روز')->numeric()->required()->minValue(1),
            TextInput::make('title')->label('عنوان روز')->required()->maxLength(255),
            RichEditor::make('description')->label('توضیحات روز')->columnSpanFull(),
            TextInput::make('meals')->label('وعده‌های غذایی')->placeholder('صبحانه, ناهار, شام')->columnSpanFull(),
            TextInput::make('accommodation')->label('محل اقامت')->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('day_number')->label('روز')->badge()->color('primary')->sortable()->weight('bold'),
                TextColumn::make('title')->label('عنوان')->searchable()->limit(50),
                TextColumn::make('meals')->label('وعده‌ها')->limit(30)->placeholder('—')->toggleable(),
                TextColumn::make('accommodation')->label('اقامت')->limit(30)->placeholder('—')->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()->label('افزودن روز'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('day_number', 'asc')
            ->reorderable('day_number');
    }
}
