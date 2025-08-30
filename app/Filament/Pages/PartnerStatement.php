<?php

namespace App\Filament\Pages;

use App\Models\Organization;
use App\Models\LedgerEntry;
use App\Models\UserOffering;
use App\Services\AffiliationPostingService as COA;
use App\Services\SettlementService;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class PartnerStatement extends Page
{
    protected static ?string $navigationGroup = 'التقارير المالية';
    protected static ?string $navigationLabel = 'كشف حساب الشريك';
    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static string $view = 'filament.pages.partner-statement';

    public ?int $orgId = null;
    public ?string $from = null;
    public ?string $to   = null;

    // Form للتسوية
    public ?float $amount = null;
    public string $method = 'cash';
    public ?string $note = null;
    public ?string $posted_at = null;

    public function mount(): void
    {
        $this->from = request('from', now()->subDays(30)->toDateString());
        $this->to   = request('to',   now()->toDateString());
        $this->orgId = request('orgId') ? (int) request('orgId') : null;

        $this->posted_at = now()->toDateString();
    }

    public function getPartnersProperty()
    {
        return Organization::query()
            ->where('type', 'insurance_company')
            ->orderBy('name')->get(['id','name']);
    }

    protected function baseQuery()
    {
        // قيود 2100 المرتبطة بباقات هذا الشريك
        return LedgerEntry::query()
            ->select([
                'ledger_entries.*',
                'invoices.number as invoice_number',
            ])
            ->leftJoin('invoices', 'invoices.id', '=', 'ledger_entries.invoice_id')
            ->join('user_offerings as uo', function ($j) {
                $j->on('ledger_entries.reference_id', '=', 'uo.id')
                    ->where('ledger_entries.reference_type', '=', UserOffering::class);
            })
            ->join('partner_offerings as po', 'po.id', '=', 'uo.partner_offering_id')
            ->where('ledger_entries.account_code', COA::ACC_PAY_PARTNER)
            ->where('po.organization_id', $this->orgId);
    }

    public function getOpeningProperty(): float
    {
        if (!$this->orgId) return 0;
        $q = (clone $this->baseQuery())->where('posted_at', '<', $this->from);
        $credit = (float) $q->clone()->where('entry_type','credit')->sum('amount');
        $debit  = (float) $q->clone()->where('entry_type','debit')->sum('amount');
        // 2100 (خصوم): الرصيد = دائن - مدين
        return round($credit - $debit, 2);
    }

    public function getRowsProperty()
    {
        if (!$this->orgId) return collect();

        return $this->baseQuery()
            ->whereBetween('posted_at', [$this->from, $this->to])
            ->orderBy('posted_at')->orderBy('ledger_entries.id')
            ->get()
            ->map(fn($e) => [
                'date'    => optional($e->posted_at)->format('Y-m-d H:i'),
                'inv'     => $e->invoice_number ?: '—',
                'desc'    => $e->description,
                'debit'   => $e->entry_type === 'debit'  ? (float) $e->amount : 0,
                'credit'  => $e->entry_type === 'credit' ? (float) $e->amount : 0,
            ]);
    }

    public function getTotalsProperty()
    {
        $rows = $this->rows;
        $debit  = (float) $rows->sum('debit');
        $credit = (float) $rows->sum('credit');
        $closing = $this->opening + ($credit - $debit); // 2100: دائن-مدين
        return compact('debit','credit','closing');
    }

    public function settle(): void
    {
        $this->validate([
            'orgId'     => ['required','integer','exists:organizations,id'],
            'amount'    => ['required','numeric','min:0.01'],
            'method'    => ['required','in:cash,pos,zaincash,bank'],
            'posted_at' => ['required','date'],
        ]);

        /** @var SettlementService $svc */
        $svc = app(SettlementService::class);
        $svc->settlePartner(
            $this->orgId,
            (float) $this->amount,
            $this->method,
            new \DateTimeImmutable($this->posted_at),
            optional(Auth::user()?->employee)->id ?? null,
            $this->note
        );

        $this->amount = null;
        $this->note = null;
        $this->dispatch('notify', type: 'success', message: 'تم تسجيل التسوية.');
    }
}
