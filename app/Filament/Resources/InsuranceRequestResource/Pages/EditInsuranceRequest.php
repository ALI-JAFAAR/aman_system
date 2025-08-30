<?php

namespace App\Filament\Resources\InsuranceRequestResource\Pages;

use App\Filament\Resources\InsuranceRequestResource;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Form;
class EditInsuranceRequest extends EditRecord
{
    protected static string $resource = InsuranceRequestResource::class;

    public function form(Form $form): Form{
        return $form->schema([
            Section::make('بيانات التأمين من الشريك')->schema([
                TextInput::make('partner_filled_number')
                    ->label('رقم التأمين (من الشريك)')
                    ->required()
                    ->maxLength(100),
            ])->columns(1),
        ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array{
        if(!empty('partner_filled_number')){
            $data['activated_at'] = now();
            $data['status'] = 'active';
        }
        return $data;
    }

    protected function getSavedNotificationTitle(): ?string{
        return 'تم تحديث طلب التأمين بنجاح';
    }
}
