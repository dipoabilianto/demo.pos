@props([
    'variant' => 'primary',
    'size' => 'default',
    'href' => null,
    'type' => 'button',
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'class' => '',
])

@php
$sizes = [
    'xs' => 'px-2.5 py-1.5 text-xs rounded-lg',
    'sm' => 'px-3.5 py-2 text-sm rounded-xl',
    'default' => 'px-5 py-2.5 text-sm rounded-xl',
    'lg' => 'px-6 py-3 text-base rounded-xl',
    'xl' => 'px-8 py-4 text-lg rounded-2xl',
];

$variants = [
    'primary' => 'btn-primary',
    'secondary' => 'btn-secondary',
    'danger' => 'btn-danger',
    'ghost' => 'btn-ghost',
    'success' => 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20 hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-600/30',
    'warning' => 'bg-amber-500 text-white shadow-md shadow-amber-500/20 hover:bg-amber-600 hover:shadow-lg hover:shadow-amber-500/30',
];

$classes = trim(sprintf(
    '%s %s %s',
    $variants[$variant] ?? $variants['primary'],
    $sizes[$size] ?? $sizes['default'],
    $class,
));

$tag = $href ? 'a' : 'button';
$attributes = $href ? ['href' => $href] : ['type' => $type, 'disabled' => $disabled];
@endphp

<{{ $tag }}
    {{ $attributes->merge(['class' => $classes]) }}
    @if ($loading) disabled @endif
    @if (!$href) wire:loading.attr="disabled" @endif
>
    @if ($loading)
        <svg class="animate-spin -ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
    @elseif ($icon)
        @svg($icon, 'h-4 w-4')
    @endif
    {{ $slot }}
</{{ $tag }}>
