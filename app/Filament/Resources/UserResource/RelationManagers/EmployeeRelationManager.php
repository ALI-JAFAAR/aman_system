<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Tables;
use App\Models\Organization;

class EmployeeRelationManager extends RelationManager
{
    protected static string $relationship = 'employees'; // ensure: User::employee()

    protected static ?string $title = 'التعيين كموظّف';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('organization_id')
                ->label('الجهة')
                ->options(fn () => Organization::orderBy('name')->pluck('name','id')->toArray())
                ->searchable()->preload()->required(),
            Forms\Components\TextInput::make('position')->label('المسمى الوظيفي'),
            Forms\Components\DatePicker::make('hired_at')->label('تاريخ التعيين')->default(now()),
            Forms\Components\Toggle::make('is_active')->label('فعّال')->default(true),
        ])->columns(2);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('organization.name')->label('الجهة'),
            Tables\Columns\TextColumn::make('position')->label('المسمى'),
            Tables\Columns\IconColumn::make('is_active')->label('الحالة')->boolean(),
            Tables\Columns\TextColumn::make('hired_at')->label('التعيين')->date(),
        ])->headerActions([
            Tables\Actions\CreateAction::make()
                ->label('تعيين كموظّف')
                ->visible(fn($livewire) => blank($livewire->ownerRecord->employees)),
        ])->actions([
            Tables\Actions\EditAction::make()->label('تعديل'),
            Tables\Actions\DeleteAction::make()->label('إلغاء التعيين'),
        ])->defaultPaginationPageOption(5);
    }
}
