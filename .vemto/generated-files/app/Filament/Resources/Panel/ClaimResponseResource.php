<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ClaimResponse;
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
use App\Filament\Resources\Panel\ClaimResponseResource\Pages;
use App\Filament\Resources\Panel\ClaimResponseResource\RelationManagers;

class ClaimResponseResource extends Resource
{
    protected static ?string $model = ClaimResponse::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.claimResponses.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.claimResponses.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.claimResponses.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    Select::make('claim_id')
                        ->label('Claim')
                        ->required()
                        ->relationship('claim', 'details')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('action')
                        ->label(__('crud.claimResponses.inputs.action.label'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'request_info' => 'Request info',
                            'provide_info' => 'Provide info',
                            'approve' => 'Approve',
                            'reject' => 'Reject',
                            'legal_contract' => 'Legal contract',
                            'user_accept_contract' => 'User accept contract',
                        ]),

                    Select::make('actor_type')
                        ->label(
                            __('crud.claimResponses.inputs.actor_type.label')
                        )
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'employee' => 'Employee',
                            'user' => 'User',
                        ]),

                    TextInput::make('actor_id')
                        ->label(__('crud.claimResponses.inputs.actor_id.label'))
                        ->required()
                        ->numeric()
                        ->step(1),

                    RichEditor::make('message')
                        ->label(__('crud.claimResponses.inputs.message.label'))
                        ->required()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    DateTimePicker::make('deleted_at')
                        ->label(
                            __('crud.claimResponses.inputs.deleted_at.label')
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
                TextColumn::make('claim.details')->label('Claim'),

                TextColumn::make('action')->label(
                    __('crud.claimResponses.inputs.action.label')
                ),

                TextColumn::make('actor_type')->label(
                    __('crud.claimResponses.inputs.actor_type.label')
                ),

                TextColumn::make('actor_id')->label(
                    __('crud.claimResponses.inputs.actor_id.label')
                ),

                TextColumn::make('message')
                    ->label(__('crud.claimResponses.inputs.message.label'))
                    ->limit(255),

                TextColumn::make('deleted_at')
                    ->label(__('crud.claimResponses.inputs.deleted_at.label'))
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
            'index' => Pages\ListClaimResponses::route('/'),
            'create' => Pages\CreateClaimResponse::route('/create'),
            'view' => Pages\ViewClaimResponse::route('/{record}'),
            'edit' => Pages\EditClaimResponse::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
