<?php

namespace App\Filament\Resources\AffiliationResource\Pages;

use App\Filament\Resources\AffiliationResource;
use Filament\Resources\Pages\ListRecords;

class ListAffiliations extends ListRecords
{
    protected static string $resource = AffiliationResource::class;

    protected function getHeaderActions(): array
    {
        return []; // read-only list
    }
}
