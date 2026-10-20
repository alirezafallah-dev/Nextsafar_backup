<?php
namespace App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
class CreateUser extends CreateRecord {
    protected static string $resource = UserResource::class;
    protected function afterCreate(): void {
        $this->record->forceFill([
            'email_verified_at' => $this->record->email_verified_at ?? now(),
            'phone_verified_at' => $this->record->phone_verified_at ?? now(),
        ])->save();
    }
}
