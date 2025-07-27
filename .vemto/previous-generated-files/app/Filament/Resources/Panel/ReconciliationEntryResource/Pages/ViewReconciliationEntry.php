<?php

namespace App\Filament\Resources\Panel\ReconciliationEntryResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\ReconciliationEntryResource;

class ViewReconciliationEntry extends ViewRecord
{
    protected static string $resource = ReconciliationEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
