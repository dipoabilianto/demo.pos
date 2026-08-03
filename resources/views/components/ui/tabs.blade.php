@props([
    'tabs' => [],
    'active' => null,
    'name' => 'tab',
])

<div
    x-data="{ active: '{{ $active ?? array_key_first($tabs) }}' }"
    {{ $attributes->merge(['class' => 'space-y-4']) }}
>
    <div class="flex gap-1 p-1 bg-stone-100 rounded-xl" role="tablist">
        @foreach ($tabs as $key => $label)
            <button
                type="button"
                role="tab"
                x-on:click="active = '{{ $key }}'"
                x-bind:class="active === '{{ $key }}' ? 'bg-white shadow-sm text-stone-900 font-medium' : 'text-stone-500 hover:text-stone-700'"
                class="flex-1 px-4 py-2 text-sm rounded-lg transition-all duration-200"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    @foreach ($tabs as $key => $label)
        <div x-show="active === '{{ $key }}'" x-transition:enter="animate-fade-in">
            {{ ${$key} ?? $slot }}
        </div>
    @endforeach
</div>
