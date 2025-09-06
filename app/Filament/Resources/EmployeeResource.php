<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource{
    protected static ?string $model = Employee::class;

    // التصنيف في القائمة
    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?string $navigationLabel = 'الموظفون';
    protected static ?int    $navigationSort  = 10;
    protected static ?string $navigationIcon  = 'heroicon-o-users';

    public static function form(Forms\Form $form): Forms\Form{
        return $form->schema([
            Forms\Components\Section::make('بيانات التعيين')->schema([
                Forms\Components\Select::make('user_id')
                    ->label('المستخدم / المنتسب')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('organization_id')
                    ->label('الجهة')
                    ->options(fn () => Organization::orderBy('name')->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('job_title')
                    ->label('المسمّى الوظيفي')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('salary')
                    ->label('الراتب')
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
            ])->columns(2),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('الموظف')->searchable(),
                Tables\Columns\TextColumn::make('organization.name')->label('الجهة')->searchable(),
                Tables\Columns\TextColumn::make('job_title')->label('المسمّى'),
                Tables\Columns\TextColumn::make('salary')
                    ->label('الراتب'),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ التعيين')->date(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(), // لأن الموديل يستخدم SoftDeletes
                Tables\Filters\SelectFilter::make('organization_id')
                    ->label('تصفية حسب الجهة')
                    ->options(fn () => Organization::orderBy('name')->pluck('name', 'id')->toArray()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ]);
    }

    // لتمكين فلتر "المحذوفات" مع SoftDeletes
    public static function getEloquentQuery(): Builder{
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array{
        return [
            'index'  => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit'   => Pages\EditEmployee::route('/{record}/edit'),
            'view'   => Pages\ViewEmployee::route('/{record}'),
        ];
    }
}
