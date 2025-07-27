<?php
// app/Filament/Resources/OrganizationResource/RelationManagers/EmployeesRelationManager.php

namespace App\Filament\Resources\OrganizationResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';
    protected static ?string $recordTitleAttribute = 'job_title';

    public  function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('الموظف')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('job_title')
                    ->label('المسمى الوظيفي')
                    ->sortable(),
                TextColumn::make('salary')
                    ->label('الراتب'),
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
