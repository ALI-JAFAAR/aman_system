<?php

namespace App\Filament\Resources\Panel\FinancialReportResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\FinancialReportResource;

class ListFinancialReports extends ListRecords
{
    protected static string $resource = FinancialReportResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
