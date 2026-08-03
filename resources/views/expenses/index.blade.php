@extends('layouts.app')
@section('title', 'Pengeluaran')
@section('subtitle', 'Total: Rp ' . number_format($totalExpenses, 0, ',', '.'))
@section('content')
<div class="flex items-center justify-between mb-6">
    <div></div>
    @if(auth()->user()->hasPermission('expenses.create'))
    <a href="{{ route('expenses.create') }}" class="rounded-xl bg-theme-gradient-r px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 hover:shadow-md transition-all duration-200">
        + Catat Pengeluaran
    </a>
    @endif
</div>

<form class="mb-6 flex flex-wrap gap-3" method="GET">
    <div>
        <label class="block text-xs font-medium text-warm-500 mb-1">Cari</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul / keterangan..." class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white w-48">
    </div>
    <div>
        <label class="block text-xs font-medium text-warm-500 mb-1">Kategori</label>
        <select name="category" class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-warm-500 mb-1">Dari Tanggal</label>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
    </div>
    <div>
        <label class="block text-xs font-medium text-warm-500 mb-1">Sampai Tanggal</label>
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
    </div>
    <div class="self-end">
        <button type="submit" class="rounded-xl bg-warm-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-warm-800 transition-all duration-200 shadow-sm">Filter</button>
    </div>
</form>

<div class="rounded-2xl bg-white shadow-md shadow-warm-900/5 border border-warm-200/50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-warm-100">
            <thead>
                <tr class="bg-warm-50/50">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Judul</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-warm-100">
                @forelse ($expenses as $expense)
                    <tr class="hover:bg-warm-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-warm-900">{{ $expense->title }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-lg bg-warm-100 px-2.5 py-1 text-xs font-medium text-warm-700">{{ $expense->category ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-rose-600">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm text-warm-400">{{ $expense->expense_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            @if(auth()->user()->hasPermission('expenses.edit'))
                            <a href="{{ route('expenses.edit', $expense) }}" class="inline-flex items-center gap-1 rounded-lg bg-theme-primary/10 px-3 py-1.5 text-sm font-medium text-theme-primary hover:bg-theme-primary/20 transition-colors ring-1 ring-theme-primary/20">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                Edit
                            </a>
                            @endif
                            @if(auth()->user()->hasPermission('expenses.delete'))
                            <form method="POST" action="{{ route('expenses.destroy', $expense) }}" class="inline" onsubmit="return confirm('Hapus pengeluaran ini?')">
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
                        <td colspan="5" class="px-6 py-12 text-center">
                            <svg class="h-12 w-12 text-warm-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.25l.213-.427A1.377 1.377 0 0010.18 13h3.66a1.37 1.37 0 00.958.573m.959-.927l.252.504M6.75 4.5h10.5a2.25 2.25 0 012.25 2.25v10.5a2.25 2.25 0 01-2.25 2.25H6.75a2.25 2.25 0 01-2.25-2.25V6.75a2.25 2.25 0 012.25-2.25z" /></svg>
                            <p class="text-sm text-warm-400">Belum ada pengeluaran.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($expenses->hasPages())
        <div class="px-6 py-4 border-t border-warm-100 bg-warm-50/30">{{ $expenses->links() }}</div>
    @endif
</div>
@endsection
