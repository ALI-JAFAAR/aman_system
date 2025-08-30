<?php

namespace App\Filament\Resources\HostAccountResource\Pages;

use App\Filament\Resources\HostAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHostAccount extends EditRecord
{
    protected static string $resource = HostAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
