<?php
// app/Filament/Resources/OrganizationResource/RelationManagers/ContractsRelationManager.php

namespace App\Filament\Resources\OrganizationResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;

use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContractsRelationManager extends RelationManager
{
    protected static string $relationship = 'contracts';
    protected static ?string $recordTitleAttribute = 'service_type';

    public  function table(Table $table): Table
    {
        return $table
            ->columns([
                BadgeColumn::make('service_type')
                    ->label('الخدمة')
                    ->enum([
                        'identity_issue' => 'هوية',
                        'route_card'     => 'بطاقة خط السير',
                        'claim'          => 'مطالبة',
                        'other'          => 'أخرى',
                    ]),
                BadgeColumn::make('initiator_type')
                    ->label('المنشئ')
                    ->enum([
                        'platform' => 'المنصة',
                        'partner'  => 'الشريك',
                    ]),
                TextColumn::make('platform_rate')
                    ->label('نسبة المنصة (%)'),
                TextColumn::make('organization_rate')
                    ->label('نسبة الجهة (%)'),
                TextColumn::make('partner_rate')
                    ->label('نسبة الشريك (%)'),
                TextColumn::make('contract_start')
                    ->label('بداية')
                    ->date(),
                TextColumn::make('contract_end')
                    ->label('انتهاء')
                    ->date()
                    ->placeholder('-'),
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
