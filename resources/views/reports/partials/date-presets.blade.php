@php
    $presets = [
        ['label' => 'Hari Ini', 'from' => now()->format('Y-m-d'), 'to' => now()->format('Y-m-d')],
        ['label' => '1 Minggu', 'from' => now()->subDays(6)->format('Y-m-d'), 'to' => now()->format('Y-m-d')],
        ['label' => '1 Bulan',  'from' => now()->subDays(29)->format('Y-m-d'), 'to' => now()->format('Y-m-d')],
    ];
@endphp
<div class="flex items-end gap-1 pb-1">
    @foreach($presets as $p)
        @php $active = request('from') === $p['from'] && request('to') === $p['to']; @endphp
        <a href="{{ request()->fullUrlWithQuery(['from' => $p['from'], 'to' => $p['to']]) }}"
           class="rounded-xl px-5 py-2.5 text-sm font-semibold whitespace-nowrap shadow-sm transition-all duration-200
                  {{ $active ? 'bg-theme-primary text-white shadow-theme-primary/20' : 'bg-white border border-warm-200 text-warm-700 hover:bg-warm-50 hover:border-theme-primary/30 active:scale-95' }}">
            {{ $p['label'] }}
        </a>
    @endforeach
</div>
