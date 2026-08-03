@props([
    'class' => '',
    'padding' => true,
    'hover' => false,
    'header' => null,
    'footer' => null,
])

<div class="card-base {{ $padding ? 'p-6' : '' }} {{ $hover ? 'hover:-translate-y-0.5 hover:shadow-lg' : '' }} {{ $class }}">
    @if ($header)
        <div class="mb-4">
            {{ $header }}
        </div>
    @endif

    @if ($header && !$slot->isEmpty())
        <div class="border-t border-stone-100 pt-4 -mx-6 px-6">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif

    @if ($footer)
        <div class="mt-4 pt-4 border-t border-stone-100">
            {{ $footer }}
        </div>
    @endif
</div>
