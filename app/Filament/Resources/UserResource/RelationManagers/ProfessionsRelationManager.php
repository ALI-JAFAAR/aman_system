<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Tables;
use App\Models\Profession;
use App\Models\Specialization;
use Filament\Forms\Get;

class ProfessionsRelationManager extends RelationManager{

    protected static string $relationship = 'professionalRecords'; // ensure on User model
    protected static ?string $title = 'السجل المهني';

    public function form(Forms\Form $form): Forms\Form{
        return $form->schema([
            Forms\Components\Select::make('profession_id')->label('المهنة')
                ->options(fn () => Profession::orderBy('name')->pluck('name', 'id')->toArray())
                ->searchable()->preload()->live()->required(),

            Forms\Components\Select::make('specialization_id')->label('الاختصاص')
                ->options(fn (Get $get) => $get('profession_id')
                    ? Specialization::where('profession_id', $get('profession_id'))
                        ->orderBy('name')->pluck('name', 'id')->toArray()
                    : []
                )
                ->searchable()->preload()->nullable(),

            Forms\Components\DatePicker::make('started_at')->label('تاريخ البدء')->required(),
            Forms\Components\DatePicker::make('ended_at')->label('تاريخ الانتهاء')->native(false),
            Forms\Components\Toggle::make('is_primary')->label('رئيسية')->default(false),
            Forms\Components\TextInput::make('license_number')->label('رقم الإجازة/الهوية')->maxLength(100),
            Forms\Components\Textarea::make('notes')->label('ملاحظات')->rows(3),
        ])->columns(2);
    }

    public function table(Tables\Table $table): Tables\Table{
        return $table->columns([
            Tables\Columns\TextColumn::make('profession.name')->label('المهنة'),
            Tables\Columns\TextColumn::make('specialization.name')->label('الاختصاص'),
            Tables\Columns\IconColumn::make('is_primary')->label('رئيسية')->boolean(),
            Tables\Columns\TextColumn::make('started_at')->label('البدء')->date(),
            Tables\Columns\TextColumn::make('ended_at')->label('الانتهاء')->date(),
        ])->headerActions([
            Tables\Actions\CreateAction::make()->label('إضافة'),
        ])->actions([
            Tables\Actions\EditAction::make()->label('تعديل'),
            Tables\Actions\DeleteAction::make()->label('حذف'),
        ])->defaultPaginationPageOption(5);
    }
}
