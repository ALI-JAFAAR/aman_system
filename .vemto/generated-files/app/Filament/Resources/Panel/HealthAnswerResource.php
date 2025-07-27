<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\HealthAnswer;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\HealthAnswerResource\Pages;
use App\Filament\Resources\Panel\HealthAnswerResource\RelationManagers;

class HealthAnswerResource extends Resource
{
    protected static ?string $model = HealthAnswer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.healthAnswers.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.healthAnswers.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.healthAnswers.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    Select::make('user_service_id')
                        ->label('User Service')
                        ->required()
                        ->relationship('userService', 'submitted_at')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    TextInput::make('question_key')
                        ->label(
                            __('crud.healthAnswers.inputs.question_key.label')
                        )
                        ->required()
                        ->string(),

                    Select::make('answer')
                        ->label(__('crud.healthAnswers.inputs.answer.label'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'نعم' => '',
                            'كلا' => '',
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
                TextColumn::make('userService.submitted_at')->label(
                    'User Service'
                ),

                TextColumn::make('question_key')->label(
                    __('crud.healthAnswers.inputs.question_key.label')
                ),

                TextColumn::make('answer')->label(
                    __('crud.healthAnswers.inputs.answer.label')
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
            'index' => Pages\ListHealthAnswers::route('/'),
            'create' => Pages\CreateHealthAnswer::route('/create'),
            'view' => Pages\ViewHealthAnswer::route('/{record}'),
            'edit' => Pages\EditHealthAnswer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
