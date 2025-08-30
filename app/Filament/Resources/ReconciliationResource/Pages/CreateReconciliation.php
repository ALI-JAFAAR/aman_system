<?php
// App/Filament/Resources/ReconciliationResource/Pages/CreateReconciliation.php

namespace App\Filament\Resources\ReconciliationResource\Pages;

use App\Filament\Resources\ReconciliationResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\ReconciliationBuilder;
use Illuminate\Database\Eloquent\Model;
class CreateReconciliation extends CreateRecord{

    protected static string $resource = ReconciliationResource::class;

    protected function handleRecordCreation(array $data): Model{
        return app(ReconciliationBuilder::class)->build(
            kind: $data['kind'],
            organizationId: (int) $data['organization_id'],
            from: $data['period_start'],
            to:   $data['period_end'],
        );
    }
}
