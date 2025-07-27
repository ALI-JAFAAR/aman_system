<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use App\Models\OfferingDistribution;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\OfferingDistributionResource\Pages;
use App\Filament\Resources\Panel\OfferingDistributionResource\RelationManagers;

class OfferingDistributionResource extends Resource
{
    protected static ?string $model = OfferingDistribution::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.offeringDistributions.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.offeringDistributions.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.offeringDistributions.collectionTitle');
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

                    Select::make('partner_offering_id')
                        ->label('Partner Offering')
                        ->required()
                        ->relationship('partnerOffering', 'contract_start')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('mode')
                        ->label(
                            __('crud.offeringDistributions.inputs.mode.label')
                        )
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'percentage' => 'Percentage',
                            'fixed' => 'Fixed',
                        ]),

                    TextInput::make('value')
                        ->label(
                            __('crud.offeringDistributions.inputs.value.label')
                        )
                        ->required()
                        ->numeric()
                        ->step(1),

                    DateTimePicker::make('deleted_at')
                        ->label(
                            __(
                                'crud.offeringDistributions.inputs.deleted_at.label'
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

                TextColumn::make('partnerOffering.contract_start')->label(
                    'Partner Offering'
                ),

                TextColumn::make('mode')->label(
                    __('crud.offeringDistributions.inputs.mode.label')
                ),

                TextColumn::make('value')->label(
                    __('crud.offeringDistributions.inputs.value.label')
                ),

                TextColumn::make('deleted_at')
                    ->label(
                        __('crud.offeringDistributions.inputs.deleted_at.label')
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
            'index' => Pages\ListOfferingDistributions::route('/'),
            'create' => Pages\CreateOfferingDistribution::route('/create'),
            'view' => Pages\ViewOfferingDistribution::route('/{record}'),
            'edit' => Pages\EditOfferingDistribution::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
