<?php

namespace App\Filament\Resources\Panel\AuditLogResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Panel\AuditLogResource;

class CreateAuditLog extends CreateRecord
{
    protected static string $resource = AuditLogResource::class;
}
