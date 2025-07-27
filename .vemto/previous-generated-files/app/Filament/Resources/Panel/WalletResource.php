<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use App\Models\Wallet;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\WalletResource\Pages;
use App\Filament\Resources\Panel\WalletResource\RelationManagers;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.wallets.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.wallets.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.wallets.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    Select::make('user_id')
                        ->label('User')
                        ->required()
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    TextInput::make('balance')
                        ->label(__('crud.wallets.inputs.balance.label'))
                        ->required()
                        ->numeric()
                        ->step(1),

                    TextInput::make('currency')
                        ->label(__('crud.wallets.inputs.currency.label'))
                        ->required()
                        ->string(),

                    TextInput::make('walletable_type')
                        ->label(__('crud.wallets.inputs.walletable_type.label'))
                        ->required()
                        ->string(),

                    TextInput::make('walletable_id')
                        ->label(__('crud.wallets.inputs.walletable_id.label'))
                        ->required()
                        ->numeric()
                        ->step(1),

                    DateTimePicker::make('deleted_at')
                        ->label(__('crud.wallets.inputs.deleted_at.label'))
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
                TextColumn::make('user.name')->label('User'),

                TextColumn::make('balance')->label(
                    __('crud.wallets.inputs.balance.label')
                ),

                TextColumn::make('currency')->label(
                    __('crud.wallets.inputs.currency.label')
                ),

                TextColumn::make('walletable_type')->label(
                    __('crud.wallets.inputs.walletable_type.label')
                ),

                TextColumn::make('walletable_id')->label(
                    __('crud.wallets.inputs.walletable_id.label')
                ),

                TextColumn::make('deleted_at')
                    ->label(__('crud.wallets.inputs.deleted_at.label'))
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
            'index' => Pages\ListWallets::route('/'),
            'create' => Pages\CreateWallet::route('/create'),
            'view' => Pages\ViewWallet::route('/{record}'),
            'edit' => Pages\EditWallet::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
