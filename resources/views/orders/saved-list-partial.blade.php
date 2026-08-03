@if ($savedOrders->isNotEmpty())
<div class="px-0.5">
    <div class="flex items-center gap-2 mb-3">
        <div class="flex h-5 w-5 items-center justify-center rounded-lg bg-emerald-50">
            <svg class="h-3 w-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="text-sm font-bold text-stone-800">Tersimpan</h3>
        <span class="text-[11px] text-stone-400">{{ $savedOrders->count() }} pesanan</span>
    </div>
    <div class="space-y-2">
        @foreach ($savedOrders as $order)
        <div class="flex items-center gap-1.5 bg-white rounded-lg px-3 py-2.5 ring-1 ring-stone-100 hover:ring-amber-200 hover:bg-amber-50/30 transition-all group">
            <a href="{{ route('orders.show', $order) }}" class="flex items-center justify-between flex-1 min-w-0">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="inline-flex items-center justify-center w-[22px] h-[16px] rounded bg-stone-800 text-white text-[8px] font-bold shrink-0">#{{ $order->queue_number ? str_pad($order->queue_number, 2, '0', STR_PAD_LEFT) : $order->id }}</span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-stone-900 truncate leading-tight">{{ $order->customer_name }}</p>
                        <p class="text-[10px] text-stone-400 truncate">{{ $order->order_number }}</p>
                    </div>
                </div>
                <div class="text-right shrink-0 ml-2">
                    <p class="text-sm font-bold text-theme-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                    <p class="text-[9px] text-emerald-600 font-medium">{{ $order->items->sum('quantity') }} item</p>
                </div>
            </a>
            <button onclick="editOrder({{ $order->id }})" class="shrink-0 flex h-7 w-7 items-center justify-center rounded-lg text-stone-400 hover:text-amber-600 hover:bg-amber-100/50 transition-all" title="Edit pesanan">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
            </button>
        </div>
        @endforeach
    </div>
</div>
@endif