<?php

namespace App\Filament\Admin\Resources\NotificationTemplateResource\Pages;

use App\Filament\Admin\Resources\NotificationTemplateResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewNotificationTemplate extends ViewRecord
{
    protected static string $resource = NotificationTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('ویرایش')
                ->visible(fn () => !$this->record->is_system),
        ];
    }
}
