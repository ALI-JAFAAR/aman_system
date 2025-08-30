@php
    // كل قيود الفواتير الخاصة بالمستخدم
    $rows = \App\Models\LedgerEntry::whereIn(
        'reference_id',
        $user->invoices()->pluck('id')
    )->orderBy('id')->get();
@endphp
<table class="min-w-full text-sm">
    <thead>
    <tr class="text-gray-500">
        <th class="text-start p-2">الحساب</th>
        <th class="text-start p-2">نوع القيد</th>
        <th class="text-start p-2">المبلغ</th>
        <th class="text-start p-2">الوصف</th>
    </tr>
    </thead>
    <tbody>
    @forelse($rows as $l)
        <tr class="border-t">
            <td class="p-2">{{ $l->account_code }}</td>
            <td class="p-2">{{ $l->entry_type }}</td>
            <td class="p-2">{{ number_format((float)$l->amount) }} IQD</td>
            <td class="p-2">{{ $l->description }}</td>
        </tr>
    @empty
        <tr><td colspan="4" class="p-3 text-gray-500">لا توجد قيود.</td></tr>
    @endforelse
    </tbody>
</table>
