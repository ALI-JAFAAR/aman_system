<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationSpecializationResource\Pages;
use App\Models\OrganizationSpecialization;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Form;

class OrganizationSpecializationResource extends Resource{

    protected static ?string $model = OrganizationSpecialization::class;
    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationLabel = 'تقييد الاختصاصات للجهات';

    public static function form(Form $form): Form{
        return $form->schema([
            Select::make('organization_id')->label('الجهة')->relationship('organization','name')->searchable()->preload()->required(),
            Select::make('specialization_id')->label('الاختصاص')->relationship('specialization','name')->searchable()->preload()->required(),
        ]);
    }
    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('organization.name')->label('الجهة')->searchable(),
            TextColumn::make('specialization.name')->label('الاختصاص')->searchable(),
        ])->actions([DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrganizationSpecializations::route('/'),
            'create' => Pages\CreateOrganizationSpecialization::route('/create'),
            'edit'   => Pages\EditOrganizationSpecialization::route('/{record}/edit'),
        ];
    }
}
