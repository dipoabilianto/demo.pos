@props([
    'count' => 1,
    'class' => 'h-4 w-full',
])

@for ($i = 0; $i < $count; $i++)
    <div class="skeleton {{ $class }}" style="animation-delay: {{ $i * 0.1 }}s"></div>
@endfor
