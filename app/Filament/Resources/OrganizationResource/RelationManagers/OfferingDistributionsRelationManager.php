<?php
// app/Filament/Resources/OrganizationResource/RelationManagers/OfferingDistributionsRelationManager.php

namespace App\Filament\Resources\OrganizationResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;

use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OfferingDistributionsRelationManager extends RelationManager
{
    protected static string $relationship = 'offeringDistributions';
    protected static ?string $recordTitleAttribute = 'organization.name';

    public function table(Table $table): Table{
        return $table
            ->columns([
                TextColumn::make('organization.name')
                    ->label('الجهة المستفيدة')
                    ->sortable()
                    ->searchable(),
                BadgeColumn::make('mode')
                    ->label('النمط')
                    ->enum([
                        'percentage' => 'نسبة',
                        'fixed'      => 'مبلغ ثابت',
                    ])
                    ->colors([
                        'primary' => 'percentage',
                        'success' => 'fixed',
                    ]),
                TextColumn::make('value')
                    ->label('القيمة'),
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
