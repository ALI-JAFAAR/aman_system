<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AffiliationResource\Pages;
use App\Models\UserAffiliation;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AffiliationResource extends Resource
{
    protected static ?string $model = UserAffiliation::class;

    protected static ?string $navigationGroup = 'الانتساب';
    protected static ?string $navigationLabel = 'سجلّ الانتسابات';
    protected static ?int    $navigationSort  = 20;
    protected static ?string $navigationIcon  = 'heroicon-o-users';

    protected static ?string $pluralLabel     = 'سجلّ الانتسابات';
    protected static ?string $modelLabel      = 'انتساب';

    /** Limit to guilds/unions/organizations */
    public static function getEloquentQuery(): Builder
    {
        $types = ['guild','trade_union','sub_union','general_union','organization'];

        return parent::getEloquentQuery()
            ->with(['user','organization'])
            ->whereHas('organization', fn ($q) => $q->whereIn('type', $types));
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]); // read-only
    }

    private static function orgTypeLabel($state): string{
        // Accept enum or string
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

    public static function table(Table $table): Table{
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
                    ->colors(['warning' => 'pending', 'success' => 'active', 'danger' => 'rejected']),
                TextColumn::make('joined_at')->label('تاريخ الانضمام')->date('Y-m-d')->sortable(),
                TextColumn::make('created_at')->label('أُنشئ')->dateTime('Y-m-d H:i')->toggleable(isToggledHiddenByDefault: true),
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
                            $q->whereHas('organization', fn ($qq) => $qq->where('type', $value));
                        }
                    }),
                SelectFilter::make('status')
                    ->label('حالة الانتساب')
                    ->options([
                        'pending'  => 'قيد المعالجة',
                        'active'   => 'فعّال',
                        'rejected' => 'مرفوض',
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
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAffiliations::route('/'),
            'view'  => Pages\ViewAffiliation::route('/{record}'),
        ];
    }
}
