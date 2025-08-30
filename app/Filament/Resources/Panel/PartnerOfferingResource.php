<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PartnerOffering;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\CheckboxColumn;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\PartnerOfferingResource\Pages;
use App\Filament\Resources\Panel\PartnerOfferingResource\RelationManagers;

class PartnerOfferingResource extends Resource{

    protected static ?string $model = PartnerOffering::class;

    protected static ?string $navigationGroup = 'التأمينات';
    protected static ?string $navigationLabel = 'عروض الشركاء';
    protected static ?int    $navigationSort  = 20;
    protected static ?string $navigationIcon  = 'heroicon-o-tag';


    public static function getModelLabel(): string
    {
        return __('crud.partnerOfferings.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.partnerOfferings.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.partnerOfferings.collectionTitle');
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

                    Select::make('package_id')
                        ->label('Package')
                        ->required()
                        ->relationship('package', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    TextInput::make('price')
                        ->label(__('crud.partnerOfferings.inputs.price.label'))
                        ->required()
                        ->numeric()
                        ->step(1),

                    DatePicker::make('contract_start')
                        ->label(
                            __(
                                'crud.partnerOfferings.inputs.contract_start.label'
                            )
                        )
                        ->rules(['date'])
                        ->required()
                        ->native(false),

                    DatePicker::make('contract_end')
                        ->label(
                            __(
                                'crud.partnerOfferings.inputs.contract_end.label'
                            )
                        )
                        ->rules(['date'])
                        ->required()
                        ->native(false),

                    Checkbox::make('auto_approve')
                        ->label(
                            __(
                                'crud.partnerOfferings.inputs.auto_approve.label'
                            )
                        )
                        ->rules(['boolean'])
                        ->required()
                        ->inline(),

                    Checkbox::make('partner_must_fill_number')
                        ->label(
                            __(
                                'crud.partnerOfferings.inputs.partner_must_fill_number.label'
                            )
                        )
                        ->rules(['boolean'])
                        ->required()
                        ->inline(),
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

                TextColumn::make('package.name')->label('Package'),

                TextColumn::make('price')->label(
                    __('crud.partnerOfferings.inputs.price.label')
                ),

                TextColumn::make('contract_start')
                    ->label(
                        __('crud.partnerOfferings.inputs.contract_start.label')
                    )
                    ->since(),

                TextColumn::make('contract_end')
                    ->label(
                        __('crud.partnerOfferings.inputs.contract_end.label')
                    )
                    ->since(),

                CheckboxColumn::make('auto_approve')->label(
                    __('crud.partnerOfferings.inputs.auto_approve.label')
                ),

                CheckboxColumn::make('partner_must_fill_number')->label(
                    __(
                        'crud.partnerOfferings.inputs.partner_must_fill_number.label'
                    )
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
            'index' => Pages\ListPartnerOfferings::route('/'),
            'create' => Pages\CreatePartnerOffering::route('/create'),
            'view' => Pages\ViewPartnerOffering::route('/{record}'),
            'edit' => Pages\EditPartnerOffering::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
