<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\FinancialReport;
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
use App\Filament\Resources\Panel\FinancialReportResource\Pages;
use App\Filament\Resources\Panel\FinancialReportResource\RelationManagers;

class FinancialReportResource extends Resource
{
    protected static ?string $model = FinancialReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.financialReports.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.financialReports.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.financialReports.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    TextInput::make('title')
                        ->label(__('crud.financialReports.inputs.title.label'))
                        ->required()
                        ->string()
                        ->autofocus(),

                    Select::make('report_type')
                        ->label(
                            __('crud.financialReports.inputs.report_type.label')
                        )
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'balance_sheet' => 'Balance sheet',
                            'profit_loss' => 'Profit loss',
                            'cash_flow' => 'Cash flow',
                            'custom' => 'Custom',
                        ]),

                    RichEditor::make('parameters')
                        ->label(
                            __('crud.financialReports.inputs.parameters.label')
                        )
                        ->required()
                        ->fileAttachmentsVisibility('public'),

                    TextInput::make('file_path')
                        ->label(
                            __('crud.financialReports.inputs.file_path.label')
                        )
                        ->required()
                        ->string(),

                    DateTimePicker::make('generated_at')
                        ->label(
                            __(
                                'crud.financialReports.inputs.generated_at.label'
                            )
                        )
                        ->rules(['date'])
                        ->required()
                        ->native(false),

                    Select::make('generated_by')
                        ->label('Employee')
                        ->required()
                        ->relationship('employee', 'job_title')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    RichEditor::make('notes')
                        ->label(__('crud.financialReports.inputs.notes.label'))
                        ->required()
                        ->string()
                        ->fileAttachmentsVisibility('public'),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->columns([
                TextColumn::make('title')->label(
                    __('crud.financialReports.inputs.title.label')
                ),

                TextColumn::make('report_type')->label(
                    __('crud.financialReports.inputs.report_type.label')
                ),

                TextColumn::make('parameters')
                    ->label(__('crud.financialReports.inputs.parameters.label'))
                    ->limit(255),

                TextColumn::make('file_path')->label(
                    __('crud.financialReports.inputs.file_path.label')
                ),

                TextColumn::make('generated_at')
                    ->label(
                        __('crud.financialReports.inputs.generated_at.label')
                    )
                    ->since(),

                TextColumn::make('employee.job_title')->label('Employee'),

                TextColumn::make('notes')
                    ->label(__('crud.financialReports.inputs.notes.label'))
                    ->limit(255),
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
            'index' => Pages\ListFinancialReports::route('/'),
            'create' => Pages\CreateFinancialReport::route('/create'),
            'view' => Pages\ViewFinancialReport::route('/{record}'),
            'edit' => Pages\EditFinancialReport::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
