@props(['paginator'])

@if ($paginator->hasPages())
    <nav class="flex items-center justify-between gap-4 mt-4">
        <p class="text-sm text-stone-500">
            Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }}
        </p>

        <div class="flex items-center gap-1.5">
            @if ($paginator->onFirstPage())
                <span class="btn-ghost px-3 py-1.5 rounded-lg text-xs opacity-30 cursor-not-allowed">
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn-ghost px-3 py-1.5 rounded-lg text-xs hover:bg-stone-100 transition-colors">
                    Sebelumnya
                </a>
            @endif

            @foreach ($paginator->render()->elements as $element)
                @if (is_string($element))
                    <span class="px-2 py-1 text-xs text-stone-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-100 text-amber-700">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1.5 rounded-lg text-xs text-stone-600 hover:bg-stone-100 transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn-ghost px-3 py-1.5 rounded-lg text-xs hover:bg-stone-100 transition-colors">
                    Selanjutnya
                </a>
            @else
                <span class="btn-ghost px-3 py-1.5 rounded-lg text-xs opacity-30 cursor-not-allowed">
                    Selanjutnya
                </span>
            @endif
        </div>
    </nav>
@endif
