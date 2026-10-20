<?php

namespace App\Filament\Admin\Resources\VisaCaseResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TimelineRelationManager extends RelationManager
{
    protected static string $relationship = 'timeline';

    protected static ?string $title = 'تایم‌لاین پرونده';
    protected static ?string $modelLabel = 'رویداد';
    protected static ?string $pluralModelLabel = 'تایم‌لاین';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event_type')->label('نوع رویداد')->badge()->color('info'),
                TextColumn::make('description')->label('توضیحات')->limit(100),
                TextColumn::make('old_status')->label('وضعیت قبلی')->badge()->placeholder('—'),
                TextColumn::make('new_status')->label('وضعیت جدید')->badge()->color('success'),
                TextColumn::make('changedBy.name')->label('توسط')->placeholder('سیستم'),
                TextColumn::make('created_at')->label('تاریخ')->dateTime('Y/m/d H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
