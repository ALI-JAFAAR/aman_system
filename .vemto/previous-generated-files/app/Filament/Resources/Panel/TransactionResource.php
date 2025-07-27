<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\TransactionResource\Pages;
use App\Filament\Resources\Panel\TransactionResource\RelationManagers;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.transactions.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.transactions.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.transactions.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    TextInput::make('wallet_id')
                        ->label(__('crud.transactions.inputs.wallet_id.label'))
                        ->required()
                        ->numeric()
                        ->step(1)
                        ->autofocus(),

                    Select::make('transaction_type')
                        ->label(
                            __(
                                'crud.transactions.inputs.transaction_type.label'
                            )
                        )
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'credit' => 'Credit',
                            'debit' => 'Debit',
                            'transfer' => 'Transfer',
                            'withdraw' => 'Withdraw',
                            'deposit' => 'Deposit',
                        ]),

                    TextInput::make('amount')
                        ->label(__('crud.transactions.inputs.amount.label'))
                        ->required()
                        ->numeric()
                        ->step(1),

                    Select::make('target_wallet_id')
                        ->label('Wallet')
                        ->nullable()
                        ->relationship('wallet', 'currency')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('status')
                        ->label(__('crud.transactions.inputs.status.label'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'completed' => 'Completed',
                            'pending' => 'Pending',
                            'failed' => 'Failed',
                        ]),

                    TextInput::make('reference_type')
                        ->label(
                            __('crud.transactions.inputs.reference_type.label')
                        )
                        ->nullable()
                        ->string(),

                    TextInput::make('reference_id')
                        ->label(
                            __('crud.transactions.inputs.reference_id.label')
                        )
                        ->required()
                        ->numeric()
                        ->step(1),

                    RichEditor::make('description')
                        ->label(
                            __('crud.transactions.inputs.description.label')
                        )
                        ->nullable()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    DateTimePicker::make('deleted_at')
                        ->label(__('crud.transactions.inputs.deleted_at.label'))
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
                TextColumn::make('wallet_id')->label(
                    __('crud.transactions.inputs.wallet_id.label')
                ),

                TextColumn::make('transaction_type')->label(
                    __('crud.transactions.inputs.transaction_type.label')
                ),

                TextColumn::make('amount')->label(
                    __('crud.transactions.inputs.amount.label')
                ),

                TextColumn::make('wallet.currency')->label('Wallet'),

                TextColumn::make('status')->label(
                    __('crud.transactions.inputs.status.label')
                ),

                TextColumn::make('reference_type')->label(
                    __('crud.transactions.inputs.reference_type.label')
                ),

                TextColumn::make('reference_id')->label(
                    __('crud.transactions.inputs.reference_id.label')
                ),

                TextColumn::make('description')
                    ->label(__('crud.transactions.inputs.description.label'))
                    ->limit(255),

                TextColumn::make('deleted_at')
                    ->label(__('crud.transactions.inputs.deleted_at.label'))
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'view' => Pages\ViewTransaction::route('/{record}'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
