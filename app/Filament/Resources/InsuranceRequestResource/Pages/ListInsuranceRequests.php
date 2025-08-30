<?php

namespace App\Filament\Resources\InsuranceRequestResource\Pages;

use App\Filament\Resources\InsuranceRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInsuranceRequests extends ListRecords
{
    protected static string $resource = InsuranceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
