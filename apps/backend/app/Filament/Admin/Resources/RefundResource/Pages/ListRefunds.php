<?php

namespace App\Filament\Admin\Resources\RefundResource\Pages;

use App\Filament\Admin\Resources\RefundResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRefunds extends ListRecords
{
    protected static string $resource = RefundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('استرداد جدید'),
        ];
    }
}
