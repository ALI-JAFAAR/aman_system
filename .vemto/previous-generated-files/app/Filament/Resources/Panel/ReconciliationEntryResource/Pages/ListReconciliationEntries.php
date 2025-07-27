<?php

namespace App\Filament\Resources\Panel\ReconciliationEntryResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\ReconciliationEntryResource;

class ListReconciliationEntries extends ListRecords
{
    protected static string $resource = ReconciliationEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
