<?php

namespace App\Filament\Admin\Resources\NotificationTemplateResource\Pages;

use App\Filament\Admin\Resources\NotificationTemplateResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditNotificationTemplate extends EditRecord
{
    protected static string $resource = NotificationTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->label('مشاهده'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
