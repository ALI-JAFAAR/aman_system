@php
    use App\Models\InvoiceItem;
    use App\Models\UserAffiliation;
    use App\Models\Organization;

    /** @var \App\Models\Invoice $invoice */
    $items = InvoiceItem::where('invoice_id', $invoice->id)
        ->where('item_type', 'affiliation_fee')
        ->get();
@endphp

@if ($items->isEmpty())
    <div class="text-gray-500">لا توجد بنود.</div>
@else
    <table class="w-full text-sm">
        <thead class="border-b font-semibold">
        <tr class="text-right">
            <th class="py-2">الوصف</th>
            <th class="py-2">المبلغ</th>
            <th class="py-2">الجهة</th>
            <th class="py-2">رقم الهوية</th>
            <th class="py-2">تاريخ الانضمام</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($items as $it)
            @php
                $orgName = $it->organization_id
                    ? Organization::where('id', $it->organization_id)->value('name')
                    : '—';
                $ua = $it->reference_id ? UserAffiliation::find($it->reference_id) : null;
            @endphp
            <tr class="border-b last:border-0">
                <td class="py-2">{{ $it->description }}</td>
                <td class="py-2">{{ number_format((float)$it->line_total) }} IQD</td>
                <td class="py-2">{{ $orgName ?? '—' }}</td>
                <td class="py-2">{{ $ua?->identity_number ?? '—' }}</td>
                <td class="py-2">{{ optional($ua?->joined_at)->format('Y-m-d') ?? '—' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
