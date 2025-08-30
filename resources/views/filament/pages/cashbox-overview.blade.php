@php
    use App\Models\LedgerEntry;
    use App\Services\AffiliationPostingService as COA;

    $accounts = [
        COA::ACC_CASH     => 'الصندوق',
        COA::ACC_POS      => 'POS',
        COA::ACC_ZAINCASH => 'زين كاش',
        COA::ACC_BANK     => 'البنك',
    ];

    $rows = [];
    $grand = 0;

    foreach ($accounts as $code => $label) {
        $balance = LedgerEntry::query()
            ->where('account_code', $code)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN entry_type='debit'  THEN amount ELSE 0 END),0)
              - COALESCE(SUM(CASE WHEN entry_type='credit' THEN amount ELSE 0 END),0) AS bal
            ")
            ->value('bal') ?? 0;

        $rows[] = ['label' => $label, 'code' => $code, 'balance' => $balance];
        $grand += $balance;
    }
@endphp

<x-filament::page>
    <div class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($rows as $r)
                <div class="rounded-xl border p-4 bg-white dark:bg-gray-900">
                    <div class="text-sm text-gray-500">{{ $r['label'] }} ({{ $r['code'] }})</div>
                    <div class="mt-1 text-2xl font-semibold">{{ number_format($r['balance']) }} IQD</div>
                </div>
            @endforeach
        </div>

        <div class="rounded-xl border p-4 bg-white dark:bg-gray-900">
            <div class="text-sm text-gray-500">الإجمالي</div>
            <div class="mt-1 text-2xl font-semibold">{{ number_format($grand) }} IQD</div>
        </div>

        <div class="rounded-xl border overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800">
                <h3 class="font-semibold">آخر 100 حركة على حسابات القاصة</h3>
            </div>
            <div class="divide-y">
                @php
                    $latest = LedgerEntry::query()
                        ->whereIn('account_code', array_keys($accounts))
                        ->orderByDesc('id')
                        ->limit(100)
                        ->get();
                @endphp

                @forelse ($latest as $e)
                    <div class="px-4 py-2 text-sm flex items-center gap-3">
                        <div class="w-36 text-gray-500">{{ optional($e->posted_at)->format('Y-m-d H:i') }}</div>
                        <div class="w-20 font-mono">{{ $e->account_code }}</div>
                        <div class="flex-1">{{ $e->description }}</div>
                        <div class="w-28 text-right">{{ $e->entry_type === 'debit' ? number_format($e->amount) : '—' }}</div>
                        <div class="w-28 text-right">{{ $e->entry_type === 'credit' ? number_format($e->amount) : '—' }}</div>
                    </div>
                @empty
                    <div class="px-4 py-6 text-gray-500">لا توجد حركات.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-filament::page>
