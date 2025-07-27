<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\WithdrawRequest;
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
use App\Filament\Resources\Panel\WithdrawRequestResource\Pages;
use App\Filament\Resources\Panel\WithdrawRequestResource\RelationManagers;

class WithdrawRequestResource extends Resource
{
    protected static ?string $model = WithdrawRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.withdrawRequests.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.withdrawRequests.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.withdrawRequests.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    Select::make('wallet_id')
                        ->label('Wallet')
                        ->required()
                        ->relationship('wallet', 'currency')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    TextInput::make('amount')
                        ->label(__('crud.withdrawRequests.inputs.amount.label'))
                        ->required()
                        ->numeric()
                        ->step(1),

                    Select::make('status')
                        ->label(__('crud.withdrawRequests.inputs.status.label'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ]),

                    DateTimePicker::make('requested_at')
                        ->label(
                            __(
                                'crud.withdrawRequests.inputs.requested_at.label'
                            )
                        )
                        ->rules(['date'])
                        ->required()
                        ->native(false),

                    TextInput::make('approved_at')
                        ->label(
                            __('crud.withdrawRequests.inputs.approved_at.label')
                        )
                        ->required()
                        ->string(),

                    RichEditor::make('notes')
                        ->label(__('crud.withdrawRequests.inputs.notes.label'))
                        ->required()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    TextInput::make('executed_at')
                        ->label(
                            __('crud.withdrawRequests.inputs.executed_at.label')
                        )
                        ->required()
                        ->string(),

                    Select::make('approved_by')
                        ->label('Employee')
                        ->required()
                        ->relationship('employee', 'job_title')
                        ->searchable()
                        ->preload()
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
                TextColumn::make('wallet.currency')->label('Wallet'),

                TextColumn::make('amount')->label(
                    __('crud.withdrawRequests.inputs.amount.label')
                ),

                TextColumn::make('status')->label(
                    __('crud.withdrawRequests.inputs.status.label')
                ),

                TextColumn::make('requested_at')
                    ->label(
                        __('crud.withdrawRequests.inputs.requested_at.label')
                    )
                    ->since(),

                TextColumn::make('approved_at')->label(
                    __('crud.withdrawRequests.inputs.approved_at.label')
                ),

                TextColumn::make('notes')
                    ->label(__('crud.withdrawRequests.inputs.notes.label'))
                    ->limit(255),

                TextColumn::make('executed_at')->label(
                    __('crud.withdrawRequests.inputs.executed_at.label')
                ),

                TextColumn::make('employee.job_title')->label('Employee'),
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
            'index' => Pages\ListWithdrawRequests::route('/'),
            'create' => Pages\CreateWithdrawRequest::route('/create'),
            'view' => Pages\ViewWithdrawRequest::route('/{record}'),
            'edit' => Pages\EditWithdrawRequest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
