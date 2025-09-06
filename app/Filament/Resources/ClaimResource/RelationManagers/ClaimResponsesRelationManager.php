<?php

namespace App\Filament\Resources\ClaimResource\RelationManagers;

use App\Models\Employee;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;

class ClaimResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'claimResponses';
    protected static ?string $title = 'ردود المطالبة';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('action')
                ->label('الإجراء')
                ->options([
                    'request_info'        => 'طلب معلومات',
                    'provide_info'        => 'تزويد معلومات',
                    'approve'             => 'إقرار/موافقة',
                    'reject'              => 'رفض',
                    'legal_contract'      => 'عقد قانوني',
                    'user_accept_contract'=> 'موافقة المنتسب على العقد',
                ])
                ->required()
                ->native(false),

            Forms\Components\Select::make('actor_type')
                ->label('نوع المنفّذ')
                ->options([
                    'employee' => 'موظف',
                    'user'     => 'منتسب',
                ])
                ->default('employee')
                ->required()
                ->live()
                ->native(false),

            Forms\Components\Select::make('actor_id')
                ->label('المنفّذ')
                ->options(function (Get $get) {
                    return $get('actor_type') === 'user'
                        ? User::orderBy('name')->pluck('name', 'id')->toArray()
                        : Employee::with('user:id,name')
                            ->get()
                            ->mapWithKeys(fn ($e) => [$e->id => ($e->user?->name ? "{$e->user->name} (EMP#{$e->id})" : "EMP#{$e->id}")])
                            ->toArray();
                })
                ->searchable()
                ->required()
                ->native(false),

            Forms\Components\Textarea::make('message')
                ->label('الرسالة')
                ->rows(4)
                ->maxLength(65535),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->heading('ردود المطالبة')
            ->columns([
                Tables\Columns\TextColumn::make('action')->label('الإجراء')->badge(),
                Tables\Columns\TextColumn::make('actor_type')->label('النوع')->badge(),
                Tables\Columns\TextColumn::make('actor_id')
                    ->label('المنفّذ')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->actor_type === 'user'
                            ? optional($record->actorUser)->name
                            : optional($record->actorEmployee?->user)->name;
                    })
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('message')->label('الرسالة')->limit(60),
                Tables\Columns\TextColumn::make('created_at')->label('التاريخ')->since(),
            ])
            ->filters([])
            // ALWAYS show the Add button (no conditions)
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('إضافة رد'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([]);
    }
}
