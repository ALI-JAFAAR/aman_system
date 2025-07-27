<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Models\OrganizationSpecialization;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\OrganizationSpecializationResource\Pages;
use App\Filament\Resources\Panel\OrganizationSpecializationResource\RelationManagers;

class OrganizationSpecializationResource extends Resource
{
    protected static ?string $model = OrganizationSpecialization::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.organizationSpecializations.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.organizationSpecializations.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.organizationSpecializations.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    Select::make('organization_id')
                        ->label('Organization')
                        ->required()
                        ->relationship('organization', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('specialization_id')
                        ->label('Specialization')
                        ->required()
                        ->relationship('specialization', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    DateTimePicker::make('deleted_at')
                        ->label(
                            __(
                                'crud.organizationSpecializations.inputs.deleted_at.label'
                            )
                        )
                        ->rules(['date'])
                        ->nullable()
                        ->native(false),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->columns([
                TextColumn::make('organization.name')->label('Organization'),

                TextColumn::make('specialization.name')->label(
                    'Specialization'
                ),

                TextColumn::make('deleted_at')
                    ->label(
                        __(
                            'crud.organizationSpecializations.inputs.deleted_at.label'
                        )
                    )
                    ->since(),
            ])
            ->filters([Tables\Filters\TrashedFilter::make()])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrganizationSpecializations::route('/'),
            'create' => Pages\CreateOrganizationSpecialization::route(
                '/create'
            ),
            'view' => Pages\ViewOrganizationSpecialization::route('/{record}'),
            'edit' => Pages\EditOrganizationSpecialization::route(
                '/{record}/edit'
            ),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
