<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use App\Models\Claim;
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
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\ClaimResource\Pages;
use App\Filament\Resources\Panel\ClaimResource\RelationManagers;

class ClaimResource extends Resource
{
    protected static ?string $model = Claim::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.claims.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.claims.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.claims.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    Select::make('user_offering_id')
                        ->label('User Offering')
                        ->required()
                        ->relationship(
                            'userOffering',
                            'platform_generated_number'
                        )
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('type')
                        ->label(__('crud.claims.inputs.type.label'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'health' => 'Health',
                            'legal' => 'Legal',
                            'financial' => 'Financial',
                        ]),

                    RichEditor::make('details')
                        ->label(__('crud.claims.inputs.details.label'))
                        ->required()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    DatePicker::make('accident_date')
                        ->label(__('crud.claims.inputs.accident_date.label'))
                        ->rules(['date'])
                        ->nullable()
                        ->native(false),

                    TextInput::make('amount_requested')
                        ->label(__('crud.claims.inputs.amount_requested.label'))
                        ->required()
                        ->numeric()
                        ->step(1),

                    Select::make('status')
                        ->label(__('crud.claims.inputs.status.label'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'pending' => 'Pending',
                            'needs_info' => 'Needs info',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ]),

                    TextInput::make('resolution_amount')
                        ->label(
                            __('crud.claims.inputs.resolution_amount.label')
                        )
                        ->required()
                        ->numeric()
                        ->step(1),

                    RichEditor::make('resolution_note')
                        ->label(__('crud.claims.inputs.resolution_note.label'))
                        ->nullable()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    TextInput::make('submitted_at')
                        ->label(__('crud.claims.inputs.submitted_at.label'))
                        ->required()
                        ->string(),

                    DateTimePicker::make('deleted_at')
                        ->label(__('crud.claims.inputs.deleted_at.label'))
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
                TextColumn::make(
                    'userOffering.platform_generated_number'
                )->label('User Offering'),

                TextColumn::make('type')->label(
                    __('crud.claims.inputs.type.label')
                ),

                TextColumn::make('details')
                    ->label(__('crud.claims.inputs.details.label'))
                    ->limit(255),

                TextColumn::make('accident_date')
                    ->label(__('crud.claims.inputs.accident_date.label'))
                    ->since(),

                TextColumn::make('amount_requested')->label(
                    __('crud.claims.inputs.amount_requested.label')
                ),

                TextColumn::make('status')->label(
                    __('crud.claims.inputs.status.label')
                ),

                TextColumn::make('resolution_amount')->label(
                    __('crud.claims.inputs.resolution_amount.label')
                ),

                TextColumn::make('resolution_note')
                    ->label(__('crud.claims.inputs.resolution_note.label'))
                    ->limit(255),

                TextColumn::make('submitted_at')->label(
                    __('crud.claims.inputs.submitted_at.label')
                ),

                TextColumn::make('deleted_at')
                    ->label(__('crud.claims.inputs.deleted_at.label'))
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
            'index' => Pages\ListClaims::route('/'),
            'create' => Pages\CreateClaim::route('/create'),
            'view' => Pages\ViewClaim::route('/{record}'),
            'edit' => Pages\EditClaim::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
