@props([
    'label' => null,
    'name' => null,
    'options' => [],
    'value' => '',
    'placeholder' => 'Pilih...',
    'error' => null,
    'required' => false,
    'disabled' => false,
    'class' => '',
])

@php
    $options = $options instanceof \Illuminate\Support\Collection ? $options->toArray() : $options;
    $initialLabel = $placeholder;
    foreach ($options as $key => $option) {
        if ($value == $key) {
            $initialLabel = $option;
            break;
        }
    }
@endphp

<div
    x-data="{
        open: false,
        selectedValue: @js($value),
        selectedLabel: @js($initialLabel),
        options: @js(array_map(fn($k, $v) => ['value' => $k, 'label' => $v], array_keys($options), $options)),
        select(key, label) {
            this.selectedValue = key;
            this.selectedLabel = label;
            this.open = false;
        }
    }"
    @click.outside="open = false"
    class="space-y-1.5"
>
    @if ($label)
        <label class="block text-sm font-medium text-stone-700">
            {{ $label }}
            @if ($required) <span class="text-red-500">*</span> @endif
        </label>
    @endif

    <div class="relative">
        <button
            type="button"
            @click="open = !open"
            :class="open ? 'ring-2 ring-theme-primary/20 border-theme-primary' : ''"
            class="input-base flex w-full items-center justify-between gap-2 {{ $error ? 'border-red-300 ring-red-500/15' : '' }} {{ $class }}"
            @if ($disabled) disabled @endif
        >
            <span class="truncate" :class="selectedValue !== '' && selectedValue !== null ? '' : 'text-stone-400'" x-text="selectedLabel || '{{ $placeholder }}'"></span>
            <svg class="h-4 w-4 shrink-0 text-stone-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            </svg>
        </button>

        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute z-50 mt-1 w-full max-h-48 overflow-y-auto rounded-xl border border-stone-200 bg-white shadow-lg origin-top"
        >
            @if ($placeholder)
                <button
                    type="button"
                    @click="select('', '{{ $placeholder }}')"
                    class="w-full px-4 py-2.5 text-left text-sm text-stone-400 transition-colors hover:bg-stone-50"
                    :class="selectedValue === '' || selectedValue === null ? 'bg-stone-50 font-medium' : ''"
                >
                    {{ $placeholder }}
                </button>
            @endif
            <template x-for="opt in options" :key="opt.value">
                <button
                    type="button"
                    @click="select(opt.value, opt.label)"
                    class="w-full px-4 py-2.5 text-left text-sm text-stone-700 transition-colors hover:bg-theme-primary/5 hover:text-theme-primary"
                    :class="String(selectedValue) === String(opt.value) ? 'bg-theme-primary/5 text-theme-primary font-medium' : ''"
                    x-text="opt.label"
                ></button>
            </template>
        </div>
    </div>

    <input type="hidden" name="{{ $name }}" :value="selectedValue" @if ($required) required @endif>

    @if ($error)
        <p class="text-xs text-red-500">{{ $error }}</p>
    @endif
</div>
