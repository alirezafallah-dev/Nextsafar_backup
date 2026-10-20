<?php

namespace App\Filament\Admin\Resources\VisaCaseResource\Pages;

use App\Filament\Admin\Resources\VisaCaseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVisaCase extends CreateRecord
{
    protected static string $resource = VisaCaseResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
