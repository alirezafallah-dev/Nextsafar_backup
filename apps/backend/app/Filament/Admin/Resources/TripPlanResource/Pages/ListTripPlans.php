<?php

namespace App\Filament\Admin\Resources\TripPlanResource\Pages;

use App\Filament\Admin\Resources\TripPlanResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListTripPlans extends ListRecords
{
    protected static string $resource = TripPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('settings')
                ->label('⚙️ تنظیمات AI')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('primary')
                ->url(route('filament.admin.pages.ai-trip-planner-settings')),
            Action::make('test_connection')
                ->label('🧪 تست اتصال Provider ها')
                ->icon('heroicon-o-signal')
                ->color('info')
                ->url(route('filament.admin.pages.ai-trip-planner-settings')),
        ];
    }
}
