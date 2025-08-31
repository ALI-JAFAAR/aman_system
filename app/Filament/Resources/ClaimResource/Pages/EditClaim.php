<?php

namespace App\Filament\Resources\ClaimResource\Pages;

use App\Filament\Resources\ClaimResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClaim extends EditRecord
{
    protected static string $resource = ClaimResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // لا نسمح بتغيير الحالة يدويًا من النموذج؛ تُدار عبر الإجراءات
        unset($data['status']);
        return $data;
    }
    protected function getHeaderActions(): array{
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
