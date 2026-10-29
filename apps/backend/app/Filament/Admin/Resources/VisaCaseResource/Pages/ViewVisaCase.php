<?php

namespace App\Filament\Admin\Resources\VisaCaseResource\Pages;

use App\Filament\Admin\Resources\VisaCaseResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVisaCase extends ViewRecord
{
    protected static string $resource = VisaCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('ویرایش'),
        ];
    }
}
