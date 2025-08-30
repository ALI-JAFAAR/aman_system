<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use App\Models\Package;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\PackageResource\Pages;
use App\Filament\Resources\Panel\PackageResource\RelationManagers;

class PackageResource extends Resource{

    protected static ?string $model = Package::class;

    protected static ?string $navigationGroup = 'التأمينات';
    protected static ?string $navigationLabel = 'الباقات';
    protected static ?int    $navigationSort  = 10;
    protected static ?string $navigationIcon  = 'heroicon-o-cube';


    public static function getModelLabel(): string
    {
        return __('crud.packages.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.packages.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.packages.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    TextInput::make('name')
                        ->label(__('crud.packages.inputs.name.label'))
                        ->required()
                        ->string()
                        ->autofocus(),

                    RichEditor::make('description')
                        ->label(__('crud.packages.inputs.description.label'))
                        ->required()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    Select::make('default_behavior')
                        ->label(
                            __('crud.packages.inputs.default_behavior.label')
                        )
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'auto_generate_number' => 'Auto generate number',
                            'partner_approval_required' =>
                                'Partner approval required',
                        ]),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->columns([
                TextColumn::make('name')->label(
                    __('crud.packages.inputs.name.label')
                ),

                TextColumn::make('description')
                    ->label(__('crud.packages.inputs.description.label'))
                    ->limit(255),

                TextColumn::make('default_behavior')->label(
                    __('crud.packages.inputs.default_behavior.label')
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
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'view' => Pages\ViewPackage::route('/{record}'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
