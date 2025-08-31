<?php

namespace App\Filament\Pages;

use App\Models\Organization;
use App\Models\UserAffiliation;
use App\Models\InvoiceItem;
use Filament\Pages\Page;

class AffiliationStatement extends Page
{
    protected static ?string $navigationGroup = 'الفوترة والمالية';
    protected static ?string $navigationLabel = 'كشف انتسابات لجهة';
    protected static ?int    $navigationSort  = 50;
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-list';

    protected static string $view = 'filament.pages.affiliation-statement';

    // عوامل التصفية (binding مع الواجهة)
    public ?int    $orgId = null;
    public ?string $from  = null;
    public ?string $to    = null;

    public function mount(): void
    {
        // افتراضي: آخر 30 يوم
        $this->from = request('from', now()->subDays(30)->toDateString());
        $this->to   = request('to',   now()->toDateString());
        $this->orgId = request('orgId') ? (int) request('orgId') : null;
    }

    public static function getNavigationBadge(): ?string
    {
        return null;
    }

    public function getOrganizationsProperty()
    {
        // كل الجهات الممكن الانتساب لها (حسب أنواعك)
        return Organization::query()
            ->whereIn('type', ['organization','guild','trade_union','sub_union','general_union'])
            ->orderBy('name')->get(['id','name']);
    }

    public function getRowsProperty()
    {
        if (!$this->orgId || !$this->from || !$this->to) return collect();

        // الانتسابات ضمن المدة
        $q = UserAffiliation::query()
            ->with(['user:id,name,email'])
            ->where('organization_id', $this->orgId)
            ->whereBetween('joined_at', [$this->from, $this->to]);

        $affs = $q->get();

        // جلب الفاتورة / الرسوم عبر invoice_items (item_type = affiliation_fee)
        $items = InvoiceItem::query()
            ->whereIn('reference_id', $affs->pluck('id'))
            ->where('reference_type', UserAffiliation::class)
            ->where('item_type', 'affiliation_fee')
            ->with('invoice:id,number,issued_at')
            ->get()
            ->keyBy('reference_id');

        return $affs->map(function ($a) use ($items) {
            $ii = $items->get($a->id);
            return [
                'id'         => $a->id,
                'identity'   => $a->identity_number ?: '—',
                'user'       => optional($a->user)->name,
                'email'      => optional($a->user)->email,
                'joined_at'  => optional($a->joined_at)->format('Y-m-d'),
                'fee'        => (float) optional($ii)->unit_price ?: 0,
                'invoice_no' => optional(optional($ii)->invoice)->number,
            ];
        });
    }

    public function getTotalsProperty(){
        $rows = $this->rows;
        return [
            'count' => $rows->count(),
            'sum'   => number_format((float) $rows->sum('fee')) . ' IQD',
        ];
    }
}
