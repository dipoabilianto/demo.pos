@props([
    'variant' => 'info',
    'dismissible' => false,
    'class' => '',
    'title' => null,
])

@php
$variants = [
    'success' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
    'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
    'danger' => 'bg-red-50 border-red-200 text-red-800',
    'info' => 'bg-sky-50 border-sky-200 text-sky-800',
    'neutral' => 'bg-stone-50 border-stone-200 text-stone-700',
];
$classes = sprintf(
    'relative rounded-xl border p-4 %s %s',
    $variants[$variant] ?? $variants['info'],
    $class,
);
@endphp

<div
    class="{{ $classes }}"
    @if ($dismissible)
        x-data="{ show: true }" x-show="show"
        x-transition:leave="transition-all duration-300 ease-in"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 translate-x-4"
    @endif
    role="alert"
>
    <div class="flex gap-3">
        <div class="flex-1">
            @if ($title)
                <p class="text-sm font-medium mb-1">{{ $title }}</p>
            @endif
            <div class="text-sm opacity-90">{{ $slot }}</div>
        </div>

        @if ($dismissible)
            <button type="button" x-on:click="show = false" class="shrink-0 p-1 rounded-lg hover:bg-black/5 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        @endif
    </div>
</div>
