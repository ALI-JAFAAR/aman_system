<?php

namespace App\Filament\Resources\Panel\ReconciliationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\ReconciliationResource;

class ViewReconciliation extends ViewRecord
{
    protected static string $resource = ReconciliationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
