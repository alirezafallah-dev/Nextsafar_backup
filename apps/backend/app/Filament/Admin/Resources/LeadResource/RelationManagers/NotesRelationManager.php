<?php

namespace App\Filament\Admin\Resources\LeadResource\RelationManagers;

use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NotesRelationManager extends RelationManager
{
    protected static string $relationship = 'notes';

    protected static ?string $title = 'یادداشت‌ها';
    protected static ?string $modelLabel = 'یادداشت';
    protected static ?string $pluralModelLabel = 'یادداشت‌ها';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Textarea::make('content')->label('محتوای یادداشت')->required()->rows(3),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('content')->label('محتوا')->limit(100)->wrap(),
                TextColumn::make('author.name')->label('نویسنده')->placeholder('سیستم'),
                TextColumn::make('created_at')->label('تاریخ')->dateTime('Y/m/d H:i')->sortable(),
            ])
            ->headerActions([
                CreateAction::make()->label('افزودن یادداشت'),
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
