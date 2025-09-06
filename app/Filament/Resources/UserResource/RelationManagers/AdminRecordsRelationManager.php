<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class AdminRecordsRelationManager extends RelationManager{

    protected static string $relationship = 'administrativeRecords'; // ensure on User model
    protected static ?string $title = 'السجل الإداري';

    public function form(Form $form): Form{
        return $form->schema([
            Select::make('type')->label('النوع')
                ->options([
                    'hire'       => 'تعيين',
                    'promotion'  => 'ترقية',
                    'warning'    => 'إنذار',
                    'suspension' => 'إيقاف',
                    'termination'=> 'إنهاء خدمة',
                    'other'      => 'أخرى',
                ])->required(),
            TextInput::make('title')->label('العنوان')->maxLength(255),
            Textarea::make('notes')->label('ملاحظات')->rows(3),
            DatePicker::make('issued_at')->label('تاريخ الإصدار')->required(),
            DatePicker::make('expires_at')->label('تاريخ الانتهاء')->native(false),
        ])->columns(2);
    }

    public function table(Table $table): Table{
        return $table->columns([
            Tables\Columns\TextColumn::make('type')->label('النوع')->badge(),
            Tables\Columns\TextColumn::make('title')->label('العنوان')->wrap(),
            Tables\Columns\TextColumn::make('issued_at')->label('الإصدار')->date(),
            Tables\Columns\TextColumn::make('expires_at')->label('الانتهاء')->date(),
        ])->headerActions([
            Tables\Actions\CreateAction::make()->label('إضافة قيد'),
        ])->actions([
            Tables\Actions\EditAction::make()->label('تعديل'),
            Tables\Actions\DeleteAction::make()->label('حذف'),
        ])->defaultPaginationPageOption(5);
    }
}
