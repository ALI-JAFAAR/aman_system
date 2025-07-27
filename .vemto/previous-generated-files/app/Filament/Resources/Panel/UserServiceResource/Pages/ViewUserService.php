<?php

namespace App\Filament\Resources\Panel\UserServiceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\UserServiceResource;

class ViewUserService extends ViewRecord
{
    protected static string $resource = UserServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
