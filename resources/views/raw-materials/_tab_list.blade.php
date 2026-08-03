<div class="flex items-center justify-between mb-6">
    <form method="GET" class="flex-1 max-w-sm">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari bahan baku..." class="block w-full rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
    </form>
    @if(auth()->user()->hasPermission('raw-materials.create'))
    <a href="{{ route('raw-materials.create') }}" class="rounded-xl bg-theme-gradient-r px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 hover:shadow-md transition-all duration-200">
        + Tambah Bahan Baku
    </a>
    @endif
</div>

<div class="rounded-2xl bg-white shadow-md shadow-warm-900/5 border border-warm-200/50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-warm-100">
            <thead>
                <tr class="bg-warm-50/50">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Satuan</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Min. Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-warm-100">
                @forelse ($materials as $material)
                    <tr class="hover:bg-warm-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-warm-900">{{ $material->name }}</td>
                        <td class="px-6 py-4 text-sm text-warm-600">{{ $material->unit }}</td>
                        <td class="px-6 py-4 text-sm font-semibold {{ $material->isLowStock() ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ number_format($material->current_stock, 0) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-warm-500">{{ number_format($material->min_stock, 0) }}</td>
                        <td class="px-6 py-4">
                            @if ($material->isLowStock())
                                <span class="inline-flex items-center rounded-lg bg-rose-100 px-2.5 py-1 text-xs font-medium text-rose-700">
                                    <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                    Stok Menipis
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-lg bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                    <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Tersedia
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            @if(auth()->user()->hasPermission('stock-opname.adjust'))
                            <a href="{{ route('stock-opname.adjust-form', $material) }}" class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-3 py-1.5 text-sm font-medium text-emerald-700 hover:bg-emerald-100 transition-colors ring-1 ring-emerald-200/50">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v9m-6.364-.636l12.728 0M4.5 21h15" /><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12.75l1.5-9h7.5l1.5 9" /></svg>
                                Opname
                            </a>
                            @endif
                            @if(auth()->user()->hasPermission('raw-materials.edit'))
                            <a href="{{ route('raw-materials.edit', $material) }}" class="inline-flex items-center gap-1 rounded-lg bg-theme-primary/10 px-3 py-1.5 text-sm font-medium text-theme-primary hover:bg-theme-primary/20 transition-colors ring-1 ring-theme-primary/20">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                Edit
                            </a>
                            @endif
                            @if(auth()->user()->hasPermission('raw-materials.delete'))
                            <form method="POST" action="{{ route('raw-materials.destroy', $material) }}" class="inline" onsubmit="return confirm('Hapus bahan baku ini? Stok terkait akan ikut terhapus.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-rose-50 px-3 py-1.5 text-sm font-medium text-rose-600 hover:bg-rose-100 transition-colors ring-1 ring-rose-200/50">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                    Hapus
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="h-12 w-12 text-warm-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                            <p class="text-sm text-warm-400">Belum ada bahan baku. Tambahkan bahan baku terlebih dahulu.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($materials->hasPages())
        <div class="px-6 py-4 border-t border-warm-100 bg-warm-50/30">{{ $materials->links() }}</div>
    @endif
</div>
