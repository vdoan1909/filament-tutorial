<?php

namespace App\Filament\Resources\CountryResource\Pages;

use App\Filament\Resources\CountryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateCountry extends CreateRecord
{
    protected static string $resource = CountryResource::class;

    // protected function getCreatedNotificationTitle(): string|null
    // {
    //     return 'Create country successfully';
    // }

    protected function getCreatedNotification(): Notification|null
    {
        return Notification::make()
            ->success()
            ->title('Create country')
            ->body('Create country successfully');
    }
}