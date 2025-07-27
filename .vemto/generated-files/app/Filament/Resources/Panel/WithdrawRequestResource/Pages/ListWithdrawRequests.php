<?php

namespace App\Filament\Resources\Panel\WithdrawRequestResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\WithdrawRequestResource;

class ListWithdrawRequests extends ListRecords
{
    protected static string $resource = WithdrawRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
