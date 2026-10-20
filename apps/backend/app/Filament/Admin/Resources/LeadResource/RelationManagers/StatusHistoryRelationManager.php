<?php

namespace App\Filament\Admin\Resources\LeadResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StatusHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'statusHistory';

    protected static ?string $title = 'تاریخچه تغییر وضعیت';
    protected static ?string $modelLabel = 'تغییر وضعیت';
    protected static ?string $pluralModelLabel = 'تاریخچه';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('old_status')->label('وضعیت قبلی')->badge(),
                TextColumn::make('new_status')->label('وضعیت جدید')->badge()->color('success'),
                TextColumn::make('reason')->label('دلیل')->limit(50)->placeholder('—'),
                TextColumn::make('changedBy.name')->label('توسط')->placeholder('سیستم'),
                TextColumn::make('created_at')->label('تاریخ')->dateTime('Y/m/d H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
