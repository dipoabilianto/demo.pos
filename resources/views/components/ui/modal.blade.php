@props([
    'name' => 'modal',
    'title' => '',
    'maxWidth' => 'lg',
    'show' => false,
])

@php
$maxWidths = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    '3xl' => 'max-w-3xl',
    '4xl' => 'max-w-4xl',
    '5xl' => 'max-w-5xl',
];
@endphp

<div
    x-data="{ show: @js($show) }"
    x-show="show"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') show = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') show = false"
    x-on:keydown.escape.window="show = false"
    x-transition:enter="transition-all duration-300 ease-out"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-all duration-200 ease-in"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div
        class="absolute inset-0 bg-black/40 backdrop-blur-sm"
        x-on:click="show = false"
    ></div>

    <div
        class="relative w-full {{ $maxWidths[$maxWidth] ?? 'max-w-lg' }} animate-scale-in"
        x-on:click.stop
    >
        <div class="bg-white/90 backdrop-blur-2xl rounded-2xl border border-white/50 shadow-2xl">
            @if ($title)
                <div class="flex items-center justify-between px-6 py-4 border-b border-stone-200/60">
                    <h2 class="text-lg font-semibold text-stone-900">{{ $title }}</h2>
                    <button
                        type="button"
                        x-on:click="show = false"
                        class="btn-ghost p-1.5 rounded-lg"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            <div class="px-6 py-4">
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-stone-200/60 bg-stone-50/50 rounded-b-2xl">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
