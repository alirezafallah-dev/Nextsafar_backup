<?php

namespace App\Filament\Admin\Resources\VisaCaseResource\Pages;

use App\Filament\Admin\Resources\VisaCaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVisaCases extends ListRecords
{
    protected static string $resource = VisaCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('پرونده جدید'),
        ];
    }
}
