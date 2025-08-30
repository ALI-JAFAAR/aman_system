<?php

namespace App\Filament\Resources\PartnerAccountResource\Pages;

use App\Filament\Resources\PartnerAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartnerAccounts extends ListRecords
{
    protected static string $resource = PartnerAccountResource::class;

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
