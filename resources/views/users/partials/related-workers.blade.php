@php
    $rows = $user->relatedWorkers()->get();
@endphp
<table class="min-w-full text-sm">
    <thead>
    <tr class="text-gray-500">
        <th class="text-start p-2">الاسم</th>
        <th class="text-start p-2">البريد</th>
    </tr>
    </thead>
    <tbody>
    @forelse($rows as $rw)
        <tr class="border-t">
            <td class="p-2">{{ $rw->name }}</td>
            <td class="p-2">{{ $rw->email }}</td>
        </tr>
    @empty
        <tr><td colspan="2" class="p-3 text-gray-500">لا يوجد عاملون مرتبطون.</td></tr>
    @endforelse
    </tbody>
</table>
