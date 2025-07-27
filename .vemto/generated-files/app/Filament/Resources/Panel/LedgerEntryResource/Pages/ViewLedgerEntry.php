<?php

namespace App\Filament\Resources\Panel\LedgerEntryResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\LedgerEntryResource;

class ViewLedgerEntry extends ViewRecord
{
    protected static string $resource = LedgerEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
