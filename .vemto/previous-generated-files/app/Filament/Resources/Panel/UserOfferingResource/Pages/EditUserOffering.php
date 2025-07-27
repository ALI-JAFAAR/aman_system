<?php

namespace App\Filament\Resources\Panel\UserOfferingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\UserOfferingResource;

class EditUserOffering extends EditRecord
{
    protected static string $resource = UserOfferingResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
