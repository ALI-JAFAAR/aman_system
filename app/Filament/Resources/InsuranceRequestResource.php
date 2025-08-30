<?php

namespace App\Filament\Resources;

use App\Enums\OrganizationType;
use App\Filament\Resources\InsuranceRequestResource\Pages;
use App\Models\UserOffering;
use App\Models\Employee;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms;
use Filament\Tables\Table;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;

class InsuranceRequestResource extends Resource{
    protected static ?string $model = UserOffering::class;

    protected static ?string $navigationGroup = 'التأمينات';
    protected static ?string $navigationLabel = 'طلبات التأمين';
    protected static ?string $pluralLabel     = 'طلبات التأمين';
    protected static ?string $modelLabel      = 'طلب التأمين';
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form{
        return $form->schema([
            Forms\Components\Section::make('تعبئة بيانات التأمين')
                ->schema([
                    Forms\Components\TextInput::make('platform_generated_number')
                        ->label('رقم المنصّة (إن وُجد)')
                        ->disabled(),

                    Forms\Components\TextInput::make('partner_filled_number')
                    ->label('رقم التأمين (من الشريك)')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\ToggleButtons::make('status')
                        ->label('حالة الطلب')
                        ->options([
                            'applied' => 'قيد المعالجة',
                            'active'  => 'فعّال',
                            'rejected'=> 'مرفوض',
                        ])
                        ->required()
                        ->inline()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            // إذا تم التفعيل ولم يُضبط تاريخ التفعيل، عيّنه الآن
                            if ($state === 'active' && blank($set('activated_at'))) {
                                $set('activated_at', now());
                            }
                        }),

                    Forms\Components\DateTimePicker::make('activated_at') // <- was approved_at
                    ->label('تاريخ التفعيل')
                        ->seconds(false)
                        ->visible(fn ($get) => $get('status') === 'active')
                        ->required(fn ($get) => $get('status') === 'active'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table{
        return $table
            ->query(function (): Builder {
                $q = UserOffering::query()
                    ->with(['user', 'partnerOffering.organization', 'partnerOffering.package'])
                    ->whereHas('partnerOffering', fn ($qq) =>
                    $qq->where('partner_must_fill_number', true) // 1/true both OK
                    );

                // Scope to partner org ONLY if the logged-in employee is from an insurance company
                if ($userId = auth()->id()) {
                    $emp = Employee::with('organization')->where('user_id', $userId)->first();
                    if ($emp && $emp->organization && $emp->organization->type === OrganizationType::INSURANCE_COMPANY) {
                        $q->whereHas('partnerOffering', fn ($qq) => $qq->where('organization_id', $emp->organization_id));
                    }
                }

                return $q; // <-- no whereNull/whereIn here
            })

            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('المنتسب')->searchable(),
                Tables\Columns\TextColumn::make('partnerOffering.organization.name')->label('الشريك')->searchable(),
                Tables\Columns\TextColumn::make('partnerOffering.package.name')->label('الباقة')->toggleable(),
                Tables\Columns\TextColumn::make('platform_generated_number')->label('رقم المنصّة')->wrap(),
                Tables\Columns\TextColumn::make('partner_filled_number')->label('رقم الشريك')->wrap(),
                Tables\Columns\TextColumn::make('status')->label('الحالة')->badge()->colors(['warning' => 'applied', 'success' => 'active', 'danger' => 'rejected']),
                Tables\Columns\TextColumn::make('applied_at')->label('أنشئ')->dateTime('Y-m-d H:i')->sortable(),
                Tables\Columns\TextColumn::make('activated_at')->label('فُعِّل')->dateTime('Y-m-d H:i')->sortable(),
            ])

            ->filters([
                // Default to “needs number”, but allow switching to ALL / filled
                Tables\Filters\TernaryFilter::make('needs_number')
                    ->label('بحاجة لرقم الشريك؟')
                    ->placeholder('الكل')
                    ->trueLabel('بحاجة لرقم (فارغ)')
                    ->falseLabel('مكتمل الرقم')
                    ->default(true) // <— shows “needing number” by default
                    ->queries(
                        true:  fn (Builder $q) => $q->whereNull('partner_filled_number')
                            ->whereIn('status', ['applied', 'pending']),
                        false: fn (Builder $q) => $q->whereNotNull('partner_filled_number'),
                        blank: fn (Builder $q) => $q, // ALL
                    ),

                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'applied' => 'قيد المعالجة',
                        'active'  => 'فعّال',
                        'rejected'=> 'مرفوض',
                    ]),
            ])

            ->actions([
                Tables\Actions\EditAction::make()->label('تعبئة/تحديث'),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label('حذف'),
            ]);
    }


    public static function getPages(): array{
        return [
            'index' => Pages\ListInsuranceRequests::route('/'),
            'edit'  => Pages\EditInsuranceRequest::route('/{record}/edit'),
            'view'  => Pages\ViewInsuranceRequest::route('/{record}'),
        ];
    }
}
