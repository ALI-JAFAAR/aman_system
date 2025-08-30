@php
    $children = $user->dependents()->get();
@endphp
<table class="min-w-full text-sm">
    <thead>
    <tr class="text-gray-500">
        <th class="text-start p-2">الاسم</th>
        <th class="text-start p-2">البريد</th>
        <th class="text-start p-2">صلة القرابة</th>
    </tr>
    </thead>
    <tbody>
    @forelse($children as $c)
        <tr class="border-t">
            <td class="p-2">{{ $c->name }}</td>
            <td class="p-2">{{ $c->email }}</td>
            <td class="p-2">{{ $c->family_relation ?? '—' }}</td>
        </tr>
    @empty
        <tr><td colspan="3" class="p-3 text-gray-500">لا يوجد أفراد عائلة.</td></tr>
    @endforelse
    </tbody>
</table>
