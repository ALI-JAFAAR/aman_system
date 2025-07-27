<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\ProfessionalRecord;
use Filament\Resources\RelationManagers\RelationManager;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProfessionalRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'professionalRecords';
    protected static ?string $recordTitleAttribute = 'title';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('المسمى الوظيفي/الشهادة'),
                TextColumn::make('organization')->label('الجهة'),
                TextColumn::make('start_date')->label('من'),
                TextColumn::make('end_date')->label('إلى')->placeholder('-'),
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
