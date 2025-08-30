<?php

namespace App\Filament\Resources;

use App\Enums\OrganizationType;
use App\Filament\Resources\PartnerAccountResource\Pages;
use App\Models\Organization;
use App\Models\UserOffering;
use App\Models\LedgerEntry;
use App\Services\AffiliationPostingService as COA;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PartnerAccountResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationGroup = 'الفوترة والمالية';
    protected static ?string $navigationLabel = 'حسابات الشركاء';
    protected static ?int    $navigationSort  = 20;
    protected static ?string $navigationIcon  = 'heroicon-o-building-office-2';

    protected static ?string $pluralLabel     = 'حسابات الشركاء';
    protected static ?string $modelLabel      = 'حساب الشريك';

    public static function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                // نجلب فقط شركات التأمين مع حقل مُشتق payable_balance
                $sub = LedgerEntry::query()
                    ->selectRaw("
                        SUM(CASE WHEN ledger_entries.entry_type = 'credit' THEN ledger_entries.amount ELSE 0 END)
                      - SUM(CASE WHEN ledger_entries.entry_type = 'debit'  THEN ledger_entries.amount ELSE 0 END)
                    ")
                    ->join('user_offerings as uo', function ($j) {
                        $j->on('ledger_entries.reference_id', '=', 'uo.id')
                            ->where('ledger_entries.reference_type', '=', UserOffering::class);
                    })
                    ->join('partner_offerings as po', 'po.id', '=', 'uo.partner_offering_id')
                    ->whereColumn('po.organization_id', 'organizations.id')
                    ->where('ledger_entries.account_code', COA::ACC_PAY_PARTNER);

                return Organization::query()
                    ->where('type', OrganizationType::INSURANCE_COMPANY)
                    ->select('organizations.*')
                    ->selectSub($sub, 'payable_balance');
            })

            ->columns([
                Tables\Columns\TextColumn::make('name')->label('الشريك')->searchable(),
                Tables\Columns\TextColumn::make('code')->label('الكود')->toggleable(),
                Tables\Columns\TextColumn::make('payable_balance')
                    ->label('الرصيد المستحق (2100)')
                    ->formatStateUsing(fn ($v) => number_format((float) $v) . ' IQD')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('has_balance')
                    ->label('إظهار ذوي الرصيد فقط')
                    ->trueLabel('رصيد > 0')
                    ->falseLabel('رصيد = 0')
                    ->queries(
                        true:  fn (Builder $q) => $q->whereRaw('(payable_balance) > 0'),
                        false: fn (Builder $q) => $q->whereRaw('(payable_balance) = 0'),
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
            'index' => Pages\ListPartnerAccounts::route('/'),
            'view'  => Pages\ViewPartnerAccount::route('/{record}'),
        ];
    }
}
