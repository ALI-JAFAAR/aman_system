<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\AdministrativeRecord;
use Filament\Resources\RelationManagers\RelationManager;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;

class AdministrativeRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'administrativeRecords';
    protected static ?string $recordTitleAttribute = 'title';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('record_type')
                    ->badge()
                    ->label('النوع')
//                    ->enum([
//                        'identity'    => 'هوية',
//                        'certificate' => 'شهادة',
//                        'license'     => 'ترخيص',
//                        'warning'     => 'إنذار',
//                        'other'       => 'أخرى',
//                    ])
                ,
                TextColumn::make('title')->label('العنوان'),
                TextColumn::make('record_date')->label('تاريخ الإصدار'),
                TextColumn::make('expiry_date')->label('تاريخ الانتهاء'),
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
