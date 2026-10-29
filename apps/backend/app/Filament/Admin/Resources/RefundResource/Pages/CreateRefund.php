<?php

namespace App\Filament\Admin\Resources\RefundResource\Pages;

use App\Filament\Admin\Resources\RefundResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRefund extends CreateRecord
{
    protected static string $resource = RefundResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
