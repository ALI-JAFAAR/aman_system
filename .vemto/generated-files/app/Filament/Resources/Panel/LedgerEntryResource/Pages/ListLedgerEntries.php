<?php

namespace App\Filament\Resources\Panel\LedgerEntryResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\LedgerEntryResource;

class ListLedgerEntries extends ListRecords
{
    protected static string $resource = LedgerEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
