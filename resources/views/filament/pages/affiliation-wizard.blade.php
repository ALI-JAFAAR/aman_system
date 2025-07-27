@php
    // يجلب الحالة دون إطلاق أخطاء التحقق
//    $state   = $this->form->getStateQuietly();
    $active  = $state['_step']  ?? 0;
    $steps   = count($state['_wizard'] ?? []);
    $isFirst = $active === 0;
    $isLast  = $active === $steps - 1;
@endphp


<x-filament::page>
    <form wire:submit.prevent="submit" class="space-y-6">
        {{-- هذا السطر يعرض الـ Wizard كامل --}}
        {{ $this->form }}

        <div class="flex justify-between items-center mt-4">
            {{-- زرّ “الرجوع” إذا لم نكن في الخطوة الأولى --}}

                <x-filament::button type="submit" color="primary">
                    إنهاء التسجيل
                </x-filament::button>

        </div>

    </form>
</x-filament::page>
