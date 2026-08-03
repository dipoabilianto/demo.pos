@props([
    'variant' => 'neutral',
    'size' => 'default',
    'class' => '',
    'dot' => false,
])

@php
$sizes = [
    'sm' => 'px-1.5 py-0.5 text-[10px]',
    'default' => 'px-2.5 py-0.5 text-xs',
    'lg' => 'px-3 py-1 text-sm',
];

$classes = sprintf(
    'badge-%s %s %s',
    $variant,
    $sizes[$size] ?? $sizes['default'],
    $class,
);
@endphp

<span class="{{ $classes }}">
    @if ($dot)
        <span class="h-1.5 w-1.5 rounded-full bg-current opacity-50"></span>
    @endif
    {{ $slot }}
</span>
