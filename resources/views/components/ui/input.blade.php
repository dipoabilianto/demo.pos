@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'inputmode' => null,
    'placeholder' => '',
    'value' => '',
    'error' => null,
    'required' => false,
    'disabled' => false,
    'class' => '',
    'icon' => null,
    'help' => null,
])

<div class="space-y-1.5">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-stone-700">
            {{ $label }}
            @if ($required) <span class="text-red-500">*</span> @endif
        </label>
    @endif

    <div class="relative">
        @if ($icon)
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                <svg class="h-4 w-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                </svg>
            </div>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            @if ($inputmode) inputmode="{{ $inputmode }}" @endif
            @if ($required) required @endif
            @if ($disabled) disabled @endif
            class="input-base {{ $icon ? 'pl-10' : '' }} {{ $error ? 'border-red-300 ring-red-500/15' : '' }} {{ $class }}"
        />
    </div>

    @if ($error)
        <p class="text-xs text-red-500">{{ $error }}</p>
    @endif

    @if ($help)
        <p class="text-xs text-stone-400">{{ $help }}</p>
    @endif
</div>
