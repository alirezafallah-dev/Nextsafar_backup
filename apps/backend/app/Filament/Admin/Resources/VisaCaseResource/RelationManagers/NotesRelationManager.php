<?php

namespace App\Filament\Admin\Resources\VisaCaseResource\RelationManagers;

use Filament\Forms\Components\Select;
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
            Select::make('type')->label('نوع یادداشت')->options([
                'internal' => '🔒 داخلی',
                'public' => '🌐 عمومی',
            ])->default('internal')->required(),
            Textarea::make('content')->label('محتوا')->required()->rows(3),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')->label('نوع')->badge()
                    ->formatStateUsing(fn ($s) => $s === 'internal' ? '🔒 داخلی' : '🌐 عمومی')
                    ->color(fn ($s) => $s === 'internal' ? 'warning' : 'success'),
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
