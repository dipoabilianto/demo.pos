@props([
    'icon' => null,
    'title' => 'Tidak ada data',
    'description' => null,
    'action' => null,
    'class' => '',
])

<div class="flex flex-col items-center justify-center py-12 px-4 {{ $class }}">
    @if ($icon)
        <div class="mb-4 rounded-full bg-stone-100 p-3">
            <svg class="h-8 w-8 text-stone-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
            </svg>
        </div>
    @else
        <div class="mb-4 rounded-full bg-stone-100 p-3">
            <svg class="h-8 w-8 text-stone-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
        </div>
    @endif

    <h3 class="text-base font-medium text-stone-900 mb-1">{{ $title }}</h3>

    @if ($description)
        <p class="text-sm text-stone-500 text-center max-w-sm mb-4">{{ $description }}</p>
    @endif

    @if ($action)
        {{ $action }}
    @endif
</div>
