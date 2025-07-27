<?php

namespace App\Filament\Resources\Panel\LedgerEntryResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Panel\LedgerEntryResource;

class CreateLedgerEntry extends CreateRecord
{
    protected static string $resource = LedgerEntryResource::class;
}
