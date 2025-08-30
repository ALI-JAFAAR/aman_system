@php
    use App\Models\LedgerEntry;

    /** @var \App\Models\Invoice $invoice */
    $rows = LedgerEntry::where('reference_id', $invoice->id)->orderBy('id')->get();
@endphp

@if ($rows->isEmpty())
    <div class="text-gray-500">لا توجد قيود.</div>
@else
    <table class="w-full text-sm">
        <thead class="border-b font-semibold">
        <tr class="text-right">
            <th class="py-2">الحساب</th>
            <th class="py-2">النوع</th>
            <th class="py-2">المبلغ</th>
            <th class="py-2">الوصف</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($rows as $l)
            <tr class="border-b last:border-0">
                <td class="py-2">{{ $l->account_code }}</td>
                <td class="py-2">{{ $l->entry_type }}</td>
                <td class="py-2">{{ number_format((float)$l->amount) }} IQD</td>
                <td class="py-2">{{ $l->description }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
