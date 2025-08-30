<?php
namespace App\Filament\Resources;

use App\Filament\Resources\ReconciliationResource\Pages;
use App\Models\Reconciliation;
use App\Models\Organization;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;

class ReconciliationResource extends Resource
{
    protected static ?string $model = Reconciliation::class;
    protected static ?string $navigationGroup = 'الحسابات';
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'التسويات';
    protected static ?string $pluralLabel     = 'التسويات';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات التسوية')
                ->schema([
                    Forms\Components\Radio::make('kind')
                        ->label('نوع التسوية')
                        ->options(['partner' => 'الشريك (2100)', 'host' => 'الجهة المضيفة (2200)'])
                        ->default('partner')
                        ->required()
                        ->reactive(),

                    Forms\Components\Select::make('organization_id')
                        ->label('الجهة')
                        ->options(fn(Forms\Get $get) =>
                        Organization::query()
                            ->when($get('kind') === 'partner',
                                fn($q)=>$q->where('type','insurance_company'),
                                fn($q)=>$q->whereIn('type',['organization','guild','trade_union','sub_union','general_union'])
                            )
                            ->orderBy('name')->pluck('name','id')->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\DatePicker::make('period_start')->label('بداية الفترة')->required(),
                    Forms\Components\DatePicker::make('period_end')->label('نهاية الفترة')->required(),

                    Forms\Components\TextInput::make('contract_id')->label('عقد (اختياري)')->numeric()->nullable(),
                ])->columns(2),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table{
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
            Tables\Columns\TextColumn::make('organization.name')->label('الجهة')->searchable(),
            Tables\Columns\TextColumn::make('period_start')->label('من')->date(),
            Tables\Columns\TextColumn::make('period_end')->label('إلى')->date(),
            Tables\Columns\TextColumn::make('total_gross_amount')->label('إجمالي المبيعات')->formatStateUsing(fn($v)=>number_format((float)$v).' IQD'),
            Tables\Columns\TextColumn::make('total_partner_share')->label('حصة الشريك')->formatStateUsing(fn($v)=>number_format((float)$v).' IQD'),
            Tables\Columns\TextColumn::make('total_organization_share')->label('حصة الجهة')->formatStateUsing(fn($v)=>number_format((float)$v).' IQD'),
            Tables\Columns\TextColumn::make('total_platform_share')->label('حصة المنصّة')->formatStateUsing(fn($v)=>number_format((float)$v).' IQD'),
            Tables\Columns\TextColumn::make('status')->badge()
                ->colors([
                    'warning' => 'draft',
                    'info'    => 'platform_reconciled',
                    'success' => 'partner_reconciled',
                    'gray'    => 'closed',
                ]),
            Tables\Columns\TextColumn::make('platform_reconciled_at')->label('تاريخ منصّة')->dateTime()->toggleable(),
        ])->filters([
            Tables\Filters\SelectFilter::make('status')->label('الحالة')->options([
                'draft'=>'مسودة','platform_reconciled'=>'مصالحة المنصّة',
                'partner_reconciled'=>'مصالحة الشريك','closed'=>'مقفلة'
            ]),
        ])->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\Action::make('platformOk')->label('تأكيد المنصّة')
                ->visible(fn($record)=>$record->status==='draft')
                ->action(function (Reconciliation $record) {
                    app(\App\Services\ReconciliationBuilder::class)
                        ->markPlatformReconciled($record, optional(auth()->user()?->employee)->id ?? null);
                }),
            Tables\Actions\Action::make('partnerOk')->label('اعتماد الشريك')
                ->visible(fn($record)=>in_array($record->status,['draft','platform_reconciled']))
                ->action(function (Reconciliation $record) {
                    app(\App\Services\ReconciliationBuilder::class)
                        ->markPartnerReconciled($record, optional(auth()->user()?->employee)->id ?? null);
                }),
            Tables\Actions\Action::make('close')->label('إقفال')
                ->color('gray')
                ->requiresConfirmation()
                ->visible(fn($record)=>in_array($record->status,['platform_reconciled','partner_reconciled']))
                ->action(function (Reconciliation $record) {
                    app(\App\Services\ReconciliationBuilder::class)->close($record, true);
                }),
        ])->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ReconciliationResource\Pages\ListReconciliations::route('/'),
            'create' => ReconciliationResource\Pages\CreateReconciliation::route('/create'),
            'view'   => ReconciliationResource\Pages\ViewReconciliation::route('/{record}'),
        ];
    }
}
