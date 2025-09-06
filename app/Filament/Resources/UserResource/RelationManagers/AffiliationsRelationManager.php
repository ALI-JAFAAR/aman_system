<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Organization;
use App\Models\Profession;
use App\Models\Specialization;
use App\Models\UserAffiliation;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Get;

class AffiliationsRelationManager extends RelationManager
{
    protected static string $relationship = 'userAffiliations';
    protected static ?string $title = 'سجلات الانتساب';

    public  function form(Forms\Form $form): Forms\Form{
        return $form->schema([
            Forms\Components\Select::make('organization_id')
                ->label('الجهة')
                ->options(fn () => Organization::orderBy('name')->pluck('name', 'id')->toArray())
                ->searchable()->preload()->required(),

            Forms\Components\Select::make('profession_id')
                ->label('المهنة')
                ->options(fn () => Profession::orderBy('name')->pluck('name', 'id')->toArray())
                ->searchable()->preload()->required()->live(),

            Forms\Components\Select::make('specialization_id')
                ->label('التخصص')
                ->options(fn (Get $get) =>
                $get('profession_id')
                    ? Specialization::where('profession_id', $get('profession_id'))
                    ->orderBy('name')->pluck('name', 'id')->toArray()
                    : []
                )
                ->searchable()->preload(),

            Forms\Components\TextInput::make('affiliation_fee')->label('رسوم الانتساب')->numeric()->minValue(0)->default(0),

            Forms\Components\Select::make('status')
                ->label('الحالة')
                ->options([
                    'pending'  => 'معلّق',
                    'approved' => 'موافق عليه',
                    'rejected' => 'مرفوض',
                    'active'   => 'فعّال',
                    'suspended'=> 'موقوف',
                ])
                ->default('pending')
                ->required(),
        ])->columns(2);
    }

    public  function table(Tables\Table $table): Tables\Table{
        return $table->columns([
            Tables\Columns\TextColumn::make('organization.name')->label('الجهة')->searchable(),
            Tables\Columns\TextColumn::make('profession.name')->label('المهنة'),
            Tables\Columns\TextColumn::make('specialization.name')->label('التخصص')->toggleable(),
            Tables\Columns\TextColumn::make('affiliation_fee')->label('الرسوم')->formatStateUsing(fn ($v) => number_format((float)$v).' IQD'),
            Tables\Columns\TextColumn::make('status')->label('الحالة')->badge()
                ->colors([
                    'warning' => 'pending',
                    'success' => ['approved','active'],
                    'danger'  => 'rejected',
                    'gray'    => 'suspended',
                ]),
        ])->headerActions([
            Tables\Actions\CreateAction::make()->label('إضافة انتساب'),
        ])->actions([
            Tables\Actions\EditAction::make()->label('تعديل'),

            Tables\Actions\Action::make('changeStatus')
                ->label('تغيير الحالة')
                ->icon('heroicon-o-adjustments-horizontal')
                ->form([
                    Forms\Components\Select::make('status')
                        ->label('الحالة')
                        ->options([
                            'pending'  => 'معلّق',
                            'approved' => 'موافق عليه',
                            'rejected' => 'مرفوض',
                            'active'   => 'فعّال',
                            'suspended'=> 'موقوف',
                        ])
                        ->required(),
                ])
                ->action(function (UserAffiliation $record, array $data) {
                    $record->update(['status' => $data['status']]);
                }),

            Tables\Actions\DeleteAction::make()->label('حذف'),
        ]);
    }
}
