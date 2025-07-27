<?php
// app/Filament/Resources/OrganizationResource/RelationManagers/PartnerOfferingsRelationManager.php

namespace App\Filament\Resources\OrganizationResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;


class PartnerOfferingsRelationManager extends RelationManager
{
    protected static string $relationship = 'partnerOfferings';
    protected static ?string $recordTitleAttribute = 'package.name';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('package.name')
                    ->label('الباقة')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('price')
                    ->label('السعر'),
                TextColumn::make('contract_start')
                    ->label('بداية العقد')
                    ->date(),
                TextColumn::make('contract_end')
                    ->label('انتهاء العقد')
                    ->date()
                    ->placeholder('-'),
                IconColumn::make('auto_approve')
                    ->label('تفعيل تلقائي')
                    ->boolean(),
                IconColumn::make('partner_must_fill_number')
                    ->label('الشريك يملأ رقم')
                    ->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
