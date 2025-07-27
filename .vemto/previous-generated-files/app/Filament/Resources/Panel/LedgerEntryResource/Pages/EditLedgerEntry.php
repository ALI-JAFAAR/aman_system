<?php

namespace App\Filament\Resources\Panel\LedgerEntryResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\LedgerEntryResource;

class EditLedgerEntry extends EditRecord
{
    protected static string $resource = LedgerEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
