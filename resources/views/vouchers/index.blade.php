@extends('layouts.app')
@section('title', 'Voucher')
@section('subtitle', 'Kelola kode voucher diskon.')
@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex gap-3">
        @if(auth()->user()->hasPermission('vouchers.create'))
        <a href="{{ route('vouchers.create') }}" class="rounded-xl bg-theme-gradient-r px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 hover:shadow-md transition-all duration-200">
            + Voucher Baru
        </a>
        @endif
    </div>
</div>

<div class="mb-6">
    <form method="GET" class="flex flex-wrap gap-3">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-warm-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode voucher..." class="w-full rounded-xl border-warm-200 pl-10 pr-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
        </div>
        <select name="type" class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
            <option value="">Semua Tipe</option>
            <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>Persen</option>
            <option value="nominal" {{ request('type') === 'nominal' ? 'selected' : '' }}>Nominal</option>
        </select>
        <select name="status" class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
            <option value="">Semua Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
        </select>
        <button type="submit" class="rounded-xl bg-warm-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-warm-800 transition-all duration-200 shadow-sm">Filter</button>
    </form>
</div>

<div class="rounded-2xl bg-white shadow-md shadow-warm-900/5 border border-warm-200/50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-warm-50/80 text-left">
                    <th class="px-5 py-3.5 font-semibold text-warm-700 text-xs uppercase tracking-wider">Kode</th>
                    <th class="px-5 py-3.5 font-semibold text-warm-700 text-xs uppercase tracking-wider">Tipe</th>
                    <th class="px-5 py-3.5 font-semibold text-warm-700 text-xs uppercase tracking-wider">Nilai</th>
                    <th class="px-5 py-3.5 font-semibold text-warm-700 text-xs uppercase tracking-wider">Min Belanja</th>
                    <th class="px-5 py-3.5 font-semibold text-warm-700 text-xs uppercase tracking-wider">Pemakaian</th>
                    <th class="px-5 py-3.5 font-semibold text-warm-700 text-xs uppercase tracking-wider">Masa Berlaku</th>
                    <th class="px-5 py-3.5 font-semibold text-warm-700 text-xs uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3.5 font-semibold text-warm-700 text-xs uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-warm-100">
                @forelse ($vouchers as $v)
                    <tr class="hover:bg-warm-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <span class="font-mono font-bold text-warm-900">{{ $v->code }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @if ($v->type === 'percentage')
                                <span class="inline-flex items-center rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">Persen</span>
                            @else
                                <span class="inline-flex items-center rounded-lg bg-purple-50 px-2.5 py-1 text-xs font-semibold text-purple-700">Nominal</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 font-semibold text-warm-800">
                            @if ($v->type === 'percentage')
                                {{ number_format($v->value, 0) }}%
                                @if ($v->max_discount)
                                    <span class="text-xs text-warm-400 font-normal">(max Rp {{ number_format($v->max_discount, 0, ',', '.') }})</span>
                                @endif
                            @else
                                Rp {{ number_format($v->value, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-5 py-4 text-warm-600">
                            @if ($v->min_order > 0)
                                Rp {{ number_format($v->min_order, 0, ',', '.') }}
                            @else
                                <span class="text-warm-400">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-sm">
                                <span class="font-semibold {{ $v->max_uses > 0 && $v->used_count >= $v->max_uses ? 'text-rose-600' : 'text-warm-800' }}">{{ $v->used_count }}</span>
                                @if ($v->max_uses > 0)
                                    <span class="text-warm-400"> / {{ $v->max_uses }}</span>
                                @else
                                    <span class="text-warm-400"> / unlimited</span>
                                @endif
                            </div>
                            @if ($v->max_uses_per_user > 0)
                                <div class="text-[10px] text-warm-400 mt-0.5">max {{ $v->max_uses_per_user }}x per orang</div>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-xs text-warm-600">
                            @if ($v->starts_at || $v->expires_at)
                                @if ($v->starts_at)
                                    <div>{{ $v->starts_at->format('d/m/Y') }}</div>
                                @endif
                                @if ($v->expires_at)
                                    <div class="text-warm-400">&rarr; {{ $v->expires_at->format('d/m/Y') }}</div>
                                @endif
                            @else
                                <span class="text-warm-400">Tanpa batas</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $now = now();
                                $isExpired = $v->expires_at && $now->gt($v->expires_at);
                                $isUpcoming = $v->starts_at && $now->lt($v->starts_at);
                            @endphp
                            @if ($isExpired)
                                <span class="inline-flex items-center rounded-lg bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-600">Kadaluarsa</span>
                            @elseif (!$v->is_active)
                                <span class="inline-flex items-center rounded-lg bg-warm-100 px-2.5 py-1 text-xs font-semibold text-warm-500">Nonaktif</span>
                            @elseif ($isUpcoming)
                                <span class="inline-flex items-center rounded-lg bg-theme-primary/10 px-2.5 py-1 text-xs font-semibold text-theme-primary">Akan Datang</span>
                            @else
                                <span class="inline-flex items-center rounded-lg bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">Aktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                @if(auth()->user()->hasPermission('vouchers.edit'))
                                <a href="{{ route('vouchers.edit', $v) }}" class="rounded-lg p-2 text-warm-500 hover:bg-warm-100 hover:text-warm-700 transition-all" title="Edit">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                </a>
                                @endif
                                @if(auth()->user()->hasPermission('vouchers.delete'))
                                @if ($v->used_count === 0)
                                <form method="POST" action="{{ route('vouchers.destroy', $v) }}" onsubmit="return confirm('Hapus voucher {{ $v->code }} secara permanen?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-lg p-2 text-rose-500 hover:bg-rose-50 hover:text-rose-700 transition-all" title="Hapus">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('vouchers.destroy', $v) }}" onsubmit="return confirm('Nonaktifkan voucher {{ $v->code }}?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-lg p-2 text-warm-500 hover:bg-rose-50 hover:text-rose-600 transition-all" title="Nonaktifkan">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75H6.912a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H15M9 3.75a2.25 2.25 0 012.25-2.25h1.5A2.25 2.25 0 0115 3.75M9 3.75h6M12 9.75v6m-3-3h6" /></svg>
                                    </button>
                                </form>
                                @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center text-warm-400">
                                <svg class="h-10 w-10 mb-3 text-warm-300" fill="none" viewBox="0 0 24 24" stroke-width="0.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>
                                <p class="text-sm font-medium">Belum ada voucher</p>
                                <p class="text-xs mt-0.5">Buat voucher pertama untuk mulai memberikan diskon!</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($vouchers->hasPages())
        <div class="border-t border-warm-100 px-5 py-3">
            {{ $vouchers->links() }}
        </div>
    @endif
</div>
@endsection
