<x-filament::page>
    <div class="space-y-6">
        <h2 class="text-xl font-bold">الهيكل الهرمي للمنظمات</h2>

        @if (empty($this->tree))
            <div class="text-gray-500">لا توجد بيانات.</div>
        @else
            <div class="rounded-lg border p-4">
                @include('filament.pages.partials.org-tree', ['nodes' => $this->tree])
            </div>
        @endif
    </div>
</x-filament::page>
