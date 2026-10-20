<?php

namespace App\Filament\Admin\Resources\NotificationResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    protected static ?string $title = 'لاگ تلاش‌ها';
    protected static ?string $modelLabel = 'لاگ';
    protected static ?string $pluralModelLabel = 'لاگ تلاش‌ها';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('attempt_number')->label('شماره تلاش')->badge()->color('info'),
                TextColumn::make('status')->label('وضعیت')->badge()
                    ->color(fn ($s) => match ($s) {
                        'success' => 'success',
                        'failed' => 'danger',
                        'retry' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('error_message')->label('پیام خطا')->limit(50)->placeholder('—'),
                TextColumn::make('provider_response')->label('پاسخ Provider')->limit(50)->placeholder('—'),
                TextColumn::make('created_at')->label('تاریخ')->dateTime('Y/m/d H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
