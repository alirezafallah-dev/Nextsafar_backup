<?php

namespace App\Filament\Admin\Resources\TourResource\Pages;

use App\Filament\Admin\Resources\TourResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTour extends CreateRecord
{
    protected static string $resource = TourResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
