<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AffiliationResource\Pages;
use App\Models\UserAffiliation;
use App\Models\Employee;
use App\Models\Organization;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select as FormSelect;
use Filament\Tables;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AffiliationResource extends Resource
{
    protected static ?string $model = UserAffiliation::class;

    protected static ?string $navigationGroup = 'الانتساب';
    protected static ?string $navigationLabel = 'سجلّ الانتسابات';
    protected static ?int    $navigationSort  = 20;
    protected static ?string $navigationIcon  = 'heroicon-o-users';

    protected static ?string $pluralLabel = 'سجلّ الانتسابات';
    protected static ?string $modelLabel  = 'انتساب';

    /** حصر النتائج بأنواع الجهات المعتمدة */
    public static function getEloquentQuery(): Builder{
        $types = ['guild','trade_union','sub_union','general_union','organization'];

        return parent::getEloquentQuery()
            ->with(['user','organization'])
            ->whereHas('organization', fn ($q) => $q->whereIn('type', $types));
    }

    /** نموذج التحرير البسيط */
    public static function form(Forms\Form $form): Forms\Form{
        return $form->schema([
            Section::make('بيانات الانتساب')->schema([
                TextInput::make('identity_number')->label('رقم الهوية')->maxLength(255),
                DatePicker::make('joined_at')->label('تاريخ الانضمام'),
                FormSelect::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending'  => 'معلّق',
                        'active'   => 'موافق عليه',
                        'rejected' => 'مرفوض',
                        'suspended'=> 'مجمّد',
                    ])
                    ->required(),
            ])->columns(2),
        ]);
    }

    private static function orgTypeLabel($state): string
    {
        $key = $state instanceof \BackedEnum ? $state->value : (string) $state;

        $map = [
            'guild'         => 'نقابة',
            'trade_union'   => 'اتحاد مهني',
            'sub_union'     => 'اتحاد فرعي',
            'general_union' => 'اتحاد عام',
            'organization'  => 'مؤسسة',
        ];

        return $map[$key] ?? $key;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('user.name')->label('العضو')->searchable(),
                TextColumn::make('organization.name')->label('الجهة')->searchable(),
                TextColumn::make('organization.type')
                    ->label('نوع الجهة')
                    ->formatStateUsing(fn ($state) => self::orgTypeLabel($state))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('identity_number')->label('رقم الهوية')->copyable()->toggleable(),
                TextColumn::make('status')->label('الحالة')->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'active',
                        'danger'  => 'rejected',
                        'gray'    => 'suspended',
                    ]),
                TextColumn::make('joined_at')->label('تاريخ الانضمام')->date('Y-m-d')->sortable(),
                TextColumn::make('created_at')->label('أُنشئ')->dateTime('Y-m-d H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('organization_type')
                    ->label('نوع الجهة')
                    ->options([
                        'guild'         => 'نقابة',
                        'trade_union'   => 'اتحاد مهني',
                        'sub_union'     => 'اتحاد فرعي',
                        'general_union' => 'اتحاد عام',
                        'organization'  => 'مؤسسة',
                    ])
                    ->query(function (Builder $q, array $data) {
                        $value = $data['value'] ?? null;

                        if ($value) {
                            dd($q->whereHas('organization', fn ($qq) => $qq->where('type', $value)));
                        }
                    }),

                SelectFilter::make('status')
                    ->label('حالة الانتساب')
                    ->options([
                        'pending'  => 'قيد المعالجة',
                        'active'   => 'فعّال',
                        'rejected' => 'مرفوض',
                        'suspended'=> 'مجمّد',
                    ])
                    ->query(function (Builder $q, array $data) {
                        if (! empty($data['value'])) {
                            $q->where('status', $data['value']);
                        }
                    }),

                Filter::make('joined_between')
                    ->form([
                        DatePicker::make('from')->label('من'),
                        DatePicker::make('to')->label('إلى'),
                    ])
                    ->query(function (Builder $q, array $data) {
                        $q->when($data['from'] ?? null, fn ($qq, $d) => $qq->whereDate('joined_at', '>=', $d))
                            ->when($data['to']   ?? null, fn ($qq, $d) => $qq->whereDate('joined_at', '<=', $d));
                    }),
            ])
            ->actions([
                ViewAction::make()->label('عرض التفصيل'),

                // زر تعديل — سيفتح صفحة تحرير تعتمد على form أعلاه
                EditAction::make()->label('تعديل'),

                // زر سريع لتغيير الحالة
                Action::make('changeStatus')
                    ->label('تغيير الحالة')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'pending'  => 'معلّق',
                                'approved'   => 'موافق عليه',
                                'rejected' => 'مرفوض',
                            ])
                            ->required(),
                    ])
                    ->action(function (UserAffiliation $record, array $data) {
                        $record->update(['status' => $data['status']]);
                    }),

                // زر "تعيين كموظّف"
                Action::make('assignAsEmployee')
                    ->label('تعيين كموظّف')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('job_title')->label('المسمّى الوظيفي')->required(),
                        Forms\Components\TextInput::make('salary')->label('الراتب')->numeric()->default(0),
                        Forms\Components\Select::make('organization_id')
                            ->label('الجهة')
                            ->options(fn () => Organization::orderBy('name')->pluck('name', 'id')->toArray())
                            ->default(fn (UserAffiliation $record) => $record->organization_id)
                            ->required(),
                    ])
                    ->action(function (UserAffiliation $record, array $data) {
                        Employee::firstOrCreate(
                            [
                                'user_id'        => $record->user_id,
                                'organization_id'=> (int) $data['organization_id'],
                            ],
                            [
                                'job_title' => $data['job_title'],
                                'salary'    => (float) ($data['salary'] ?? 0),
                            ]
                        );
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAffiliations::route('/'),
            'view'  => Pages\ViewAffiliation::route('/{record}'),
            'edit'  => Pages\EditAffiliation::route('/{record}/edit'),
        ];
    }
}
