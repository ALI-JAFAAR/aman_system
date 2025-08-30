<?php

namespace App\Filament\Resources\HostAccountResource\Pages;

use App\Filament\Resources\HostAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHostAccounts extends ListRecords
{
    protected static string $resource = HostAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
