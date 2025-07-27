<?php

namespace App\Filament\Resources\Panel\WalletResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\WalletResource;

class ListWallets extends ListRecords
{
    protected static string $resource = WalletResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
