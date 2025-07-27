<?php
// app/Filament/Resources/OrganizationResource/RelationManagers/AffiliationsRelationManager.php

namespace App\Filament\Resources\OrganizationResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;


class AffiliationsRelationManager extends RelationManager
{
    protected static string $relationship = 'affiliations';
    protected static ?string $recordTitleAttribute = 'user.name';

    public  function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->sortable()
                    ->searchable(),
                BadgeColumn::make('status')
                    ->label('الحالة')
                    ->enum([
                        'pending'  => 'في الانتظار',
                        'approved' => 'مقبول',
                        'rejected' => 'مرفوض',
                    ])
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ]),
                TextColumn::make('joined_at')
                    ->label('تاريخ الطلب')
                    ->dateTime('Y-m-d H:i'),
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
