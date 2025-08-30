<?php

namespace App\Filament\Resources;

use App\Enums\OrganizationType;
use App\Filament\Resources\HostAccountResource\Pages;
use App\Models\Organization;
use App\Models\UserOffering;
use App\Models\LedgerEntry;
use App\Services\AffiliationPostingService as COA;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HostAccountResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationGroup = 'الحسابات';
    protected static ?string $navigationLabel = 'حسابات الجهات';
    protected static ?string $pluralLabel     = 'حسابات الجهات (مضيف)';
    protected static ?string $modelLabel      = 'حساب الجهة';
    protected static ?string $navigationIcon  = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                // أنواع الجهات (مؤسسة/نقابة/اتحاد بأنواعه)
                $hostTypes = [
                    'organization',
                    'guild',
                    'trade_union',
                    'sub_union',
                    'general_union',
                ];

                // نجلب رصيد 2200 عبر join على invoice_items للحصول على organization_id للـ host
                $sub = LedgerEntry::query()
                    ->selectRaw("
                        SUM(CASE WHEN ledger_entries.entry_type = 'credit' THEN ledger_entries.amount ELSE 0 END)
                      - SUM(CASE WHEN ledger_entries.entry_type = 'debit'  THEN ledger_entries.amount ELSE 0 END)
                    ")
                    ->join('user_offerings as uo', function ($j) {
                        $j->on('ledger_entries.reference_id', '=', 'uo.id')
                            ->where('ledger_entries.reference_type', '=', UserOffering::class);
                    })
                    ->join('invoice_items as ii', function ($j) {
                        $j->on('ii.invoice_id', '=', 'ledger_entries.invoice_id')
                            ->on('ii.reference_id', '=', 'ledger_entries.reference_id')
                            ->where('ii.item_type', '=', 'offering');
                    })
                    ->whereColumn('ii.organization_id', 'organizations.id')
                    ->where('ledger_entries.account_code', COA::ACC_PAY_HOST);

                return Organization::query()
                    ->whereIn('type', $hostTypes)
                    ->select('organizations.*')
                    ->selectSub($sub, 'host_balance');
            })

            ->columns([
                Tables\Columns\TextColumn::make('name')->label('الجهة')->searchable(),
                Tables\Columns\TextColumn::make('type')->label('النوع')->badge()->sortable(),
                Tables\Columns\TextColumn::make('host_balance')
                    ->label('الرصيد المستحق (2200)')
                    ->formatStateUsing(fn ($v) => number_format((float) $v) . ' IQD')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('has_balance')
                    ->label('إظهار ذوي الرصيد فقط')
                    ->trueLabel('رصيد > 0')
                    ->falseLabel('رصيد = 0')
                    ->queries(
                        true:  fn (Builder $q) => $q->whereRaw('(host_balance) > 0'),
                        false: fn (Builder $q) => $q->whereRaw('(host_balance) = 0'),
                        blank: fn (Builder $q) => $q,
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('تفاصيل'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHostAccounts::route('/'),
            'view'  => Pages\ViewHostAccount::route('/{record}'),
        ];
    }
}
