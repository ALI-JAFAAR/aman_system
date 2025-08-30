@php
    $wallet = $user->wallet;
@endphp
<div class="grid grid-cols-3 gap-4">
    <div>
        <div class="text-gray-500">الرصيد</div>
        <div class="font-semibold">
            {{ number_format((float) ($wallet->balance ?? 0)) }} {{ $wallet->currency ?? 'IQD' }}
        </div>
    </div>
    <div>
        <div class="text-gray-500">المعرّف</div>
        <div class="font-mono">{{ $wallet?->id ?? '—' }}</div>
    </div>
    <div>
        <div class="text-gray-500">آخر تحديث</div>
        <div>{{ optional($wallet?->updated_at)->format('Y-m-d H:i') ?? '—' }}</div>
    </div>
</div>
