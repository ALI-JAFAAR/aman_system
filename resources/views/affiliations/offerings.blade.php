@props([
    // required
    'affiliation',

    // optional: show only offerings from the same org as this affiliation
    'restrictToAffiliationOrg' => false,
])

@php
//    dd($affiliation);
    // Build the query safely + eager-load everything we need
    $q = $affiliation->userOfferings()
        ->with([
            'partnerOffering:id,organization_id,package_id,price',
            'partnerOffering.organization:id,name',
            'partnerOffering.package:id,name',
        ])
        ->latest('id');

    if ($restrictToAffiliationOrg) {
        $q->whereHas('partnerOffering', fn ($qq) =>
            $qq->where('organization_id', $affiliation->organization_id)
        );
    }

    $items = $q->get();
@endphp

<div
    x-data="{ open: true }"
    class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
>
    <div class="flex items-center justify-between px-4 py-3 bg-gray-50/60 dark:bg-gray-800/40">
        <h3 class="text-base font-semibold">باقات التأمين ({{ $items->count() }})</h3>

        <button type="button"
                class="text-sm text-primary-600 hover:underline"
                @click="open = !open"
                x-text="open ? 'إخفاء' : 'عرض'">
        </button>
    </div>

    <div x-show="open" x-collapse class="divide-y divide-gray-100 dark:divide-gray-800">
        @forelse ($items as $uo)
            @php
                $company = data_get($uo, 'partnerOffering.organization.name');
                $package = data_get($uo, 'partnerOffering.package.name');
                $price   = number_format((float) data_get($uo, 'partnerOffering.price', 0));
                $status  = strtolower((string) $uo->status);

                $badgeClass = match ($status) {
                    'active'                 => 'bg-green-100 text-green-700 ring-green-600/20',
                    'applied','pending'      => 'bg-amber-100 text-amber-700 ring-amber-600/20',
                    'rejected'               => 'bg-rose-100 text-rose-700 ring-rose-600/20',
                    default                  => 'bg-gray-100 text-gray-700 ring-gray-600/20',
                };
            @endphp

            <div class="px-4 py-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-medium">{{ $company ?: '—' }}</span>
                    <span class="text-gray-400">•</span>
                    <span class="text-sm">{{ $package ?: '—' }}</span>
                    <span class="text-gray-400">•</span>
                    <span class="text-sm">{{ $price }} IQD</span>
                    <span class="text-gray-400">•</span>
                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs ring-1 ring-inset {{ $badgeClass }}">
                        {{ __($status) }}
                    </span>
                </div>

                <div class="mt-2 grid grid-cols-1 sm:grid-cols-4 gap-2 text-sm">
                    <div><span class="text-gray-500">رقم المنصّة:</span> <span class="font-mono">{{ $uo->platform_generated_number ?: '—' }}</span></div>
                    <div><span class="text-gray-500">رقم الشريك:</span>  <span class="font-mono">{{ $uo->partner_filled_number   ?: '—' }}</span></div>
                    <div><span class="text-gray-500">تقديم:</span>       <span>{{ optional($uo->applied_at)->format('Y-m-d')   ?: '—' }}</span></div>
                    <div><span class="text-gray-500">تفعيل:</span>       <span>{{ optional($uo->activated_at)->format('Y-m-d') ?: '—' }}</span></div>
                </div>
            </div>
        @empty
            <div class="px-4 py-6 text-sm text-gray-500">
                لا توجد باقات لهذا العضو.
            </div>
        @endforelse
    </div>
</div>
