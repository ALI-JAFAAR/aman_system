<?php
namespace App\Filament\Resources\ReconciliationResource\Pages;

use App\Filament\Resources\ReconciliationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReconciliation extends CreateRecord{
    protected static string $resource = ReconciliationResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model{
        // We don’t save the form model directly; we build it via the service:
        return app(\App\Services\ReconciliationBuilder::class)
            ->build($data['kind'], (int)$data['organization_id'], $data['period_start'], $data['period_end'], $data['contract_id'] ?? null);
    }
}
