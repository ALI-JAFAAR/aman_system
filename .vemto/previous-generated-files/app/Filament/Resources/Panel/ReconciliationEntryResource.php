<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\ReconciliationEntry;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\ReconciliationEntryResource\Pages;
use App\Filament\Resources\Panel\ReconciliationEntryResource\RelationManagers;

class ReconciliationEntryResource extends Resource
{
    protected static ?string $model = ReconciliationEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.reconciliationEntries.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.reconciliationEntries.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.reconciliationEntries.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    Select::make('reconciliation_id')
                        ->label('Reconciliation')
                        ->required()
                        ->relationship('reconciliation', 'period_start')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('ledger_entry_id')
                        ->label('Ledger Entry')
                        ->required()
                        ->relationship('ledgerEntry', 'reference_type')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    DateTimePicker::make('deleted_at')
                        ->label(
                            __(
                                'crud.reconciliationEntries.inputs.deleted_at.label'
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
                TextColumn::make('reconciliation.period_start')->label(
                    'Reconciliation'
                ),

                TextColumn::make('ledgerEntry.reference_type')->label(
                    'Ledger Entry'
                ),

                TextColumn::make('deleted_at')
                    ->label(
                        __('crud.reconciliationEntries.inputs.deleted_at.label')
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
            'index' => Pages\ListReconciliationEntries::route('/'),
            'create' => Pages\CreateReconciliationEntry::route('/create'),
            'view' => Pages\ViewReconciliationEntry::route('/{record}'),
            'edit' => Pages\EditReconciliationEntry::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
