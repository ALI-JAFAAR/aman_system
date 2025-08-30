<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\UserOffering;
use Filament\Resources\RelationManagers\RelationManager;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;

class UserOfferingsRelationManager extends RelationManager
{
    protected static string $relationship = 'userOfferings';
    protected static ?string $recordTitleAttribute = 'package.name';

    public function table(Table $table): Table{
        return $table
            ->columns([
                TextColumn::make('package.name')->label('الباقة'),
                TextColumn::make('organization.name')->label('الجهة'),
                TextColumn::make('price')->label('السعر'),
                TextColumn::make('status')
                    ->badge()
                    ->label('الحالة')
//                    ->enum([
//                        'pending'  => 'في الانتظار',
//                        'active'   => 'نشط',
//                        'expired'  => 'منتهية',
//                    ])
                ,
                TextColumn::make('created_at')->label('تاريخ الاشتراك')->dateTime('Y-m-d'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
