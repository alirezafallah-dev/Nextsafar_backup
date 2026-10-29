<?php

namespace App\Filament\Admin\Resources\DocumentResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TempLinksRelationManager extends RelationManager
{
    protected static string $relationship = 'tempLinks';

    protected static ?string $title = 'لینک‌های موقت';
    protected static ?string $modelLabel = 'لینک';
    protected static ?string $pluralModelLabel = 'لینک‌های موقت';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('token')->label('توکن')->limit(20)->copyable(),
                TextColumn::make('user.name')->label('ایجادکننده')->placeholder('—'),
                TextColumn::make('expires_at')->label('انقضا')
                    ->dateTime('Y/m/d H:i')
                    ->color(fn ($state) => $state->isPast() ? 'danger' : 'warning'),
                IconColumn::make('is_active')->label('فعال')->boolean()
                    ->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')->falseColor('danger'),
                TextColumn::make('access_count')->label('تعداد استفاده')->badge()->color('info'),
                TextColumn::make('last_accessed_at')->label('آخرین استفاده')->dateTime('Y/m/d H:i')->placeholder('—'),
                TextColumn::make('created_at')->label('تاریخ ایجاد')->dateTime('Y/m/d H:i')->sortable(),
            ])
            ->actions([
                Action::make('revoke')
                    ->label('لغو')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['is_active' => false]))
                    ->visible(fn ($record) => $record->is_active),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
