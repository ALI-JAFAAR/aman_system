<?php

namespace App\Filament\Resources\Panel\ReconciliationEntryResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\ReconciliationEntryResource;

class EditReconciliationEntry extends EditRecord
{
    protected static string $resource = ReconciliationEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
