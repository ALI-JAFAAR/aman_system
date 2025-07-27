<?php

namespace App\Filament\Resources\Panel\WithdrawRequestResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\WithdrawRequestResource;

class ViewWithdrawRequest extends ViewRecord
{
    protected static string $resource = WithdrawRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
