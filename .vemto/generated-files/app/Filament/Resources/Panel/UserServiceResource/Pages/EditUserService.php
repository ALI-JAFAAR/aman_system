<?php

namespace App\Filament\Resources\Panel\UserServiceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\UserServiceResource;

class EditUserService extends EditRecord
{
    protected static string $resource = UserServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
