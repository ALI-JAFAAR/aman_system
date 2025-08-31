<?php

namespace App\Filament\Resources\ClaimResource\Pages;

use App\Filament\Resources\ClaimResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewClaim extends ViewRecord{

    protected static string $resource = ClaimResource::class;

    public function infolist(Infolist $infolist): Infolist{
        return $infolist->schema([
            Section::make('الملخص')->schema([
                TextEntry::make('user.name')->label('المنتسب'),
                TextEntry::make('type')->label('النوع'),
                TextEntry::make('status')->label('الحالة'),
                TextEntry::make('amount_requested')->label('المطلوب')
                    ->formatStateUsing(fn ($state) => number_format((float) ($state ?? 0)) . ' IQD'),
                TextEntry::make('amount_approved')->label('المعتمد')
                    ->formatStateUsing(fn ($state) => number_format((float) ($state ?? 0)) . ' IQD'),
                TextEntry::make('organization.name')->label('الجهة'),
                TextEntry::make('created_at')->label('أُنشئت')->dateTime(),
                TextEntry::make('approved_at')->label('تاريخ الاعتماد')->dateTime(),
                TextEntry::make('paid_at')->label('تاريخ الصرف')->dateTime(),
            ])->columns(2),
        ]);
    }
}
