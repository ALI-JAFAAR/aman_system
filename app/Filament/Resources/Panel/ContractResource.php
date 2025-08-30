<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use App\Models\Contract;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\ContractResource\Pages;
use App\Filament\Resources\Panel\ContractResource\RelationManagers;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationGroup = 'التأمينات';
    protected static ?string $navigationLabel = 'العقود';
    protected static ?int    $navigationSort  = 30;
    protected static ?string $navigationIcon  = 'heroicon-o-briefcase';


    public static function getModelLabel(): string
    {
        return __('crud.contracts.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.contracts.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.contracts.collectionTitle');
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

                    Select::make('service_type')
                        ->label(__('crud.contracts.inputs.service_type.label'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'identity_issue' => 'Identity issue',
                            'route_card' => 'Route card',
                            'claim' => 'Claim',
                            'other' => 'Other',
                            'certifcate' => 'Certifcate',
                        ]),

                    Select::make('initiator_type')
                        ->label(
                            __('crud.contracts.inputs.initiator_type.label')
                        )
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'platform' => 'Platform',
                            'partner' => 'Partner',
                        ]),

                    TextInput::make('platform_rate')
                        ->label(__('crud.contracts.inputs.platform_rate.label'))
                        ->nullable()
                        ->numeric()
                        ->step(1),

                    TextInput::make('organization_rate')
                        ->label(
                            __('crud.contracts.inputs.organization_rate.label')
                        )
                        ->nullable()
                        ->numeric()
                        ->step(1),

                    TextInput::make('partner_rate')
                        ->label(__('crud.contracts.inputs.partner_rate.label'))
                        ->required()
                        ->numeric()
                        ->step(1),

                    DatePicker::make('contract_start')
                        ->label(
                            __('crud.contracts.inputs.contract_start.label')
                        )
                        ->rules(['date'])
                        ->required()
                        ->native(false),

                    DatePicker::make('contract_end')
                        ->label(__('crud.contracts.inputs.contract_end.label'))
                        ->rules(['date'])
                        ->nullable()
                        ->native(false),

                    RichEditor::make('notes')
                        ->label(__('crud.contracts.inputs.notes.label'))
                        ->required()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    Select::make('partner_offering_id')
                        ->label('Partner Offering')
                        ->nullable()
                        ->relationship('partnerOffering', 'contract_start')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    TextInput::make('platform_share')
                        ->label(
                            __('crud.contracts.inputs.platform_share.label')
                        )
                        ->required()
                        ->numeric()
                        ->step(1),

                    TextInput::make('organization_share')
                        ->label(
                            __('crud.contracts.inputs.organization_share.label')
                        )
                        ->required()
                        ->numeric()
                        ->step(1),

                    TextInput::make('partner_share')
                        ->label(__('crud.contracts.inputs.partner_share.label'))
                        ->required()
                        ->numeric()
                        ->step(1),

                    TextInput::make('contract_version')
                        ->label(
                            __('crud.contracts.inputs.contract_version.label')
                        )
                        ->required()
                        ->numeric()
                        ->step(1),
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

                TextColumn::make('service_type')->label(
                    __('crud.contracts.inputs.service_type.label')
                ),

                TextColumn::make('initiator_type')->label(
                    __('crud.contracts.inputs.initiator_type.label')
                ),

                TextColumn::make('platform_rate')->label(
                    __('crud.contracts.inputs.platform_rate.label')
                ),

                TextColumn::make('organization_rate')->label(
                    __('crud.contracts.inputs.organization_rate.label')
                ),

                TextColumn::make('partner_rate')->label(
                    __('crud.contracts.inputs.partner_rate.label')
                ),

                TextColumn::make('contract_start')
                    ->label(__('crud.contracts.inputs.contract_start.label'))
                    ->since(),

                TextColumn::make('contract_end')
                    ->label(__('crud.contracts.inputs.contract_end.label'))
                    ->since(),

                TextColumn::make('notes')
                    ->label(__('crud.contracts.inputs.notes.label'))
                    ->limit(255),

                TextColumn::make('partnerOffering.contract_start')->label(
                    'Partner Offering'
                ),

                TextColumn::make('platform_share')->label(
                    __('crud.contracts.inputs.platform_share.label')
                ),

                TextColumn::make('organization_share')->label(
                    __('crud.contracts.inputs.organization_share.label')
                ),

                TextColumn::make('partner_share')->label(
                    __('crud.contracts.inputs.partner_share.label')
                ),

                TextColumn::make('contract_version')->label(
                    __('crud.contracts.inputs.contract_version.label')
                ),
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
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'view' => Pages\ViewContract::route('/{record}'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
