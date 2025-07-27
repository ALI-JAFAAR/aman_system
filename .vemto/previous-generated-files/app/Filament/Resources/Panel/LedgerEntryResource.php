<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\LedgerEntry;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\LedgerEntryResource\Pages;
use App\Filament\Resources\Panel\LedgerEntryResource\RelationManagers;

class LedgerEntryResource extends Resource
{
    protected static ?string $model = LedgerEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.ledgerEntries.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.ledgerEntries.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.ledgerEntries.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    TextInput::make('reference_type')
                        ->label(
                            __('crud.ledgerEntries.inputs.reference_type.label')
                        )
                        ->required()
                        ->string()
                        ->autofocus(),

                    TextInput::make('reference_id')
                        ->label(
                            __('crud.ledgerEntries.inputs.reference_id.label')
                        )
                        ->required()
                        ->numeric()
                        ->step(1),

                    TextInput::make('account_code')
                        ->label(
                            __('crud.ledgerEntries.inputs.account_code.label')
                        )
                        ->required()
                        ->string(),

                    Select::make('entry_type')
                        ->label(
                            __('crud.ledgerEntries.inputs.entry_type.label')
                        )
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'debit' => 'Debit',
                            'credit' => 'Credit',
                        ]),

                    TextInput::make('amount')
                        ->label(__('crud.ledgerEntries.inputs.amount.label'))
                        ->required()
                        ->numeric()
                        ->step(1),

                    RichEditor::make('description')
                        ->label(
                            __('crud.ledgerEntries.inputs.description.label')
                        )
                        ->nullable()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    Select::make('created_by')
                        ->label('Employee')
                        ->required()
                        ->relationship('employee', 'job_title')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Checkbox::make('is_locked')
                        ->label(__('crud.ledgerEntries.inputs.is_locked.label'))
                        ->rules(['boolean'])
                        ->required()
                        ->inline(),

                    DateTimePicker::make('deleted_at')
                        ->label(
                            __('crud.ledgerEntries.inputs.deleted_at.label')
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
                TextColumn::make('reference_type')->label(
                    __('crud.ledgerEntries.inputs.reference_type.label')
                ),

                TextColumn::make('reference_id')->label(
                    __('crud.ledgerEntries.inputs.reference_id.label')
                ),

                TextColumn::make('account_code')->label(
                    __('crud.ledgerEntries.inputs.account_code.label')
                ),

                TextColumn::make('entry_type')->label(
                    __('crud.ledgerEntries.inputs.entry_type.label')
                ),

                TextColumn::make('amount')->label(
                    __('crud.ledgerEntries.inputs.amount.label')
                ),

                TextColumn::make('description')
                    ->label(__('crud.ledgerEntries.inputs.description.label'))
                    ->limit(255),

                TextColumn::make('employee.job_title')->label('Employee'),

                CheckboxColumn::make('is_locked')->label(
                    __('crud.ledgerEntries.inputs.is_locked.label')
                ),

                TextColumn::make('deleted_at')
                    ->label(__('crud.ledgerEntries.inputs.deleted_at.label'))
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
            'index' => Pages\ListLedgerEntries::route('/'),
            'create' => Pages\CreateLedgerEntry::route('/create'),
            'view' => Pages\ViewLedgerEntry::route('/{record}'),
            'edit' => Pages\EditLedgerEntry::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
