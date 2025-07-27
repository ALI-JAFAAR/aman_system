<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Reconciliation;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\ReconciliationResource\Pages;
use App\Filament\Resources\Panel\ReconciliationResource\RelationManagers;

class ReconciliationResource extends Resource
{
    protected static ?string $model = Reconciliation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.reconciliations.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.reconciliations.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.reconciliations.collectionTitle');
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

                    Select::make('contract_id')
                        ->label('Contract')
                        ->required()
                        ->relationship('contract', 'contract_start')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    DatePicker::make('period_start')
                        ->label(
                            __('crud.reconciliations.inputs.period_start.label')
                        )
                        ->rules(['date'])
                        ->required()
                        ->native(false),

                    DatePicker::make('period_end')
                        ->label(
                            __('crud.reconciliations.inputs.period_end.label')
                        )
                        ->rules(['date'])
                        ->required()
                        ->native(false),

                    TextInput::make('total_gross_amount')
                        ->label(
                            __(
                                'crud.reconciliations.inputs.total_gross_amount.label'
                            )
                        )
                        ->required()
                        ->numeric()
                        ->step(1),

                    TextInput::make('total_platform_share')
                        ->label(
                            __(
                                'crud.reconciliations.inputs.total_platform_share.label'
                            )
                        )
                        ->required()
                        ->numeric()
                        ->step(1),

                    TextInput::make('total_organization_share')
                        ->label(
                            __(
                                'crud.reconciliations.inputs.total_organization_share.label'
                            )
                        )
                        ->required()
                        ->numeric()
                        ->step(1),

                    TextInput::make('total_partner_share')
                        ->label(
                            __(
                                'crud.reconciliations.inputs.total_partner_share.label'
                            )
                        )
                        ->required()
                        ->numeric()
                        ->step(1),

                    Select::make('status')
                        ->label(__('crud.reconciliations.inputs.status.label'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'draft' => 'Draft',
                            'pending_partner' => 'Pending partner',
                            'confirmed' => 'Confirmed',
                        ]),

                    DateTimePicker::make('platform_reconciled_at')
                        ->label(
                            __(
                                'crud.reconciliations.inputs.platform_reconciled_at.label'
                            )
                        )
                        ->rules(['date'])
                        ->required()
                        ->native(false),

                    Select::make('platform_reconciled_by')
                        ->label('Employee')
                        ->required()
                        ->relationship('employee', 'job_title')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('partner_reconciled_by')
                        ->label('Employee2')
                        ->required()
                        ->relationship('employee2', 'job_title')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    DateTimePicker::make('deleted_at')
                        ->label(
                            __('crud.reconciliations.inputs.deleted_at.label')
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

                TextColumn::make('contract.contract_start')->label('Contract'),

                TextColumn::make('period_start')
                    ->label(
                        __('crud.reconciliations.inputs.period_start.label')
                    )
                    ->since(),

                TextColumn::make('period_end')
                    ->label(__('crud.reconciliations.inputs.period_end.label'))
                    ->since(),

                TextColumn::make('total_gross_amount')->label(
                    __('crud.reconciliations.inputs.total_gross_amount.label')
                ),

                TextColumn::make('total_platform_share')->label(
                    __('crud.reconciliations.inputs.total_platform_share.label')
                ),

                TextColumn::make('total_organization_share')->label(
                    __(
                        'crud.reconciliations.inputs.total_organization_share.label'
                    )
                ),

                TextColumn::make('total_partner_share')->label(
                    __('crud.reconciliations.inputs.total_partner_share.label')
                ),

                TextColumn::make('status')->label(
                    __('crud.reconciliations.inputs.status.label')
                ),

                TextColumn::make('platform_reconciled_at')
                    ->label(
                        __(
                            'crud.reconciliations.inputs.platform_reconciled_at.label'
                        )
                    )
                    ->since(),

                TextColumn::make('employee.job_title')->label('Employee'),

                TextColumn::make('employee2.job_title')->label('Employee2'),

                TextColumn::make('deleted_at')
                    ->label(__('crud.reconciliations.inputs.deleted_at.label'))
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
            'index' => Pages\ListReconciliations::route('/'),
            'create' => Pages\CreateReconciliation::route('/create'),
            'view' => Pages\ViewReconciliation::route('/{record}'),
            'edit' => Pages\EditReconciliation::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
