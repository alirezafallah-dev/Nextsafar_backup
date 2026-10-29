<?php

namespace App\Filament\Admin\Resources\DocumentResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AccessLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'accessLogs';

    protected static ?string $title = 'لاگ دسترسی‌ها';
    protected static ?string $modelLabel = 'دسترسی';
    protected static ?string $pluralModelLabel = 'لاگ دسترسی';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('کاربر')->placeholder('مهمان'),
                TextColumn::make('ip_address')->label('IP')->badge()->color('info'),
                TextColumn::make('user_agent')->label('User Agent')->limit(50),
                TextColumn::make('access_type')->label('نوع دسترسی')->badge()
                    ->formatStateUsing(fn ($s) => match ($s) {
                        'view' => '👁️ مشاهده',
                        'download' => '⬇️ دانلود',
                        'temp_link' => '🔗 لینک موقت',
                        default => $s,
                    })
                    ->color('primary'),
                TextColumn::make('created_at')->label('تاریخ')->dateTime('Y/m/d H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
