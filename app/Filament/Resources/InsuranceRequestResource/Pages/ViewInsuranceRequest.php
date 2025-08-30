<?php

namespace App\Filament\Resources\InsuranceRequestResource\Pages;

use App\Filament\Resources\InsuranceRequestResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ViewInsuranceRequest extends ViewRecord{
    protected static string $resource = InsuranceRequestResource::class;

    public function infolist(Infolist $infolist): Infolist{
        return $infolist->schema([
            Section::make('الطلب')->schema([
                TextEntry::make('id')->label('#'),
                TextEntry::make('user.name')->label('المنتسب'),
                TextEntry::make('partnerOffering.organization.name')->label('الشريك'),
                TextEntry::make('partnerOffering.package.name')->label('الباقة'),
                TextEntry::make('partnerOffering.price')
                    ->label('السعر')
                    ->formatStateUsing(fn ($state) => number_format((float) ($state ?? 0)) . ' IQD'),
                TextEntry::make('status')->label('الحالة'),
                TextEntry::make('partner_filled_number')->label('رقم التأمين (شريك)')->placeholder('—'),
                TextEntry::make('platform_generated_number')->label('رقم التأمين (منصة)')->placeholder('—'),
                TextEntry::make('applied_at')->label('تاريخ التقديم')->dateTime(),
                TextEntry::make('activated_at')->label('تاريخ الاعتماد')->dateTime(),
            ])->columns(2),
        ]);
    }
}
