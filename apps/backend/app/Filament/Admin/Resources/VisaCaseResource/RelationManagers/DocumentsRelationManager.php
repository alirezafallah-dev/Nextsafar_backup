<?php

namespace App\Filament\Admin\Resources\VisaCaseResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'مدارک پرونده';
    protected static ?string $modelLabel = 'مدرک';
    protected static ?string $pluralModelLabel = 'مدارک';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document_type')->label('نوع مدرک')->badge()->color('info'),
                TextColumn::make('file_name')->label('نام فایل')->limit(30),
                TextColumn::make('file_size')->label('حجم')
                    ->formatStateUsing(fn ($state) => $state ? round($state / 1024, 2) . ' KB' : '—'),
                IconColumn::make('is_verified')->label('تایید شده')->boolean()
                    ->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')->falseColor('warning'),
                TextColumn::make('verified_at')->label('تاریخ تایید')->dateTime('Y/m/d H:i')->placeholder('—'),
                TextColumn::make('created_at')->label('تاریخ آپلود')->dateTime('Y/m/d H:i'),
            ])
            ->actions([
                ViewAction::make()->url(fn ($record) => $record->file_path ? asset('storage/' . $record->file_path) : '#'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
