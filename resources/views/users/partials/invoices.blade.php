@php
    $rows = $user->invoices()->latest()->get();
@endphp
<table class="min-w-full text-sm">
    <thead>
    <tr class="text-gray-500">
        <th class="text-start p-2">رقم</th>
        <th class="text-start p-2">التاريخ</th>
        <th class="text-start p-2">الإجمالي</th>
        <th class="text-start p-2">المسدد</th>
        <th class="text-start p-2">المتبقي</th>
        <th class="text-start p-2">الحالة</th>
        <th class="text-start p-2">إجراء</th>
    </tr>
    </thead>
    <tbody>
    @forelse($rows as $inv)
        <tr class="border-t">
            <td class="p-2 font-mono">{{ $inv->number }}</td>
            <td class="p-2">{{ $inv->issued_at?->format('Y-m-d H:i') }}</td>
            <td class="p-2">{{ number_format((float)$inv->total) }} IQD</td>
            <td class="p-2">{{ number_format((float)$inv->paid) }} IQD</td>
            <td class="p-2">{{ number_format((float)$inv->balance) }} IQD</td>
            <td class="p-2">{{ $inv->status }}</td>
            <td class="p-2">
                <a class="text-primary-600 underline" target="_blank"
                   href="{{ route('filament.admin.resources.invoices.view', $inv) }}">عرض</a>
            </td>
        </tr>
    @empty
        <tr><td colspan="7" class="p-3 text-gray-500">لا توجد فواتير.</td></tr>
    @endforelse
    </tbody>
</table>
