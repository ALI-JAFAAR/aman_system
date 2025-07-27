<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BankAccount;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\CheckboxColumn;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\BankAccountResource\Pages;
use App\Filament\Resources\Panel\BankAccountResource\RelationManagers;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.bankAccounts.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.bankAccounts.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.bankAccounts.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    TextInput::make('owner_id')
                        ->label(__('crud.bankAccounts.inputs.owner_id.label'))
                        ->required()
                        ->string()
                        ->autofocus(),

                    TextInput::make('owner_type')
                        ->label(__('crud.bankAccounts.inputs.owner_type.label'))
                        ->required()
                        ->string(),

                    TextInput::make('bank_name')
                        ->label(__('crud.bankAccounts.inputs.bank_name.label'))
                        ->required()
                        ->string(),

                    TextInput::make('branch_name')
                        ->label(
                            __('crud.bankAccounts.inputs.branch_name.label')
                        )
                        ->required()
                        ->string(),

                    TextInput::make('account_name')
                        ->label(
                            __('crud.bankAccounts.inputs.account_name.label')
                        )
                        ->required()
                        ->string(),

                    TextInput::make('account_number')
                        ->label(
                            __('crud.bankAccounts.inputs.account_number.label')
                        )
                        ->required()
                        ->string(),

                    TextInput::make('iban')
                        ->label(__('crud.bankAccounts.inputs.iban.label'))
                        ->required()
                        ->string(),

                    TextInput::make('currency')
                        ->label(__('crud.bankAccounts.inputs.currency.label'))
                        ->required()
                        ->string(),

                    Checkbox::make('is_primary')
                        ->label(__('crud.bankAccounts.inputs.is_primary.label'))
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
                TextColumn::make('owner_id')->label(
                    __('crud.bankAccounts.inputs.owner_id.label')
                ),

                TextColumn::make('owner_type')->label(
                    __('crud.bankAccounts.inputs.owner_type.label')
                ),

                TextColumn::make('bank_name')->label(
                    __('crud.bankAccounts.inputs.bank_name.label')
                ),

                TextColumn::make('branch_name')->label(
                    __('crud.bankAccounts.inputs.branch_name.label')
                ),

                TextColumn::make('account_name')->label(
                    __('crud.bankAccounts.inputs.account_name.label')
                ),

                TextColumn::make('account_number')->label(
                    __('crud.bankAccounts.inputs.account_number.label')
                ),

                TextColumn::make('iban')->label(
                    __('crud.bankAccounts.inputs.iban.label')
                ),

                TextColumn::make('currency')->label(
                    __('crud.bankAccounts.inputs.currency.label')
                ),

                CheckboxColumn::make('is_primary')->label(
                    __('crud.bankAccounts.inputs.is_primary.label')
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
            'index' => Pages\ListBankAccounts::route('/'),
            'create' => Pages\CreateBankAccount::route('/create'),
            'view' => Pages\ViewBankAccount::route('/{record}'),
            'edit' => Pages\EditBankAccount::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
