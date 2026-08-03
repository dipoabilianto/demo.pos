@extends('layouts.app')
@section('title', 'Produk')
@section('subtitle', 'Kelola stok dan data produk.')
@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex gap-3">
        <a href="{{ route('categories.index') }}" class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-warm-700 ring-1 ring-warm-200 hover:bg-warm-50 hover:ring-warm-300 transition-all duration-200">Kategori</a>
        @if(auth()->user()->hasPermission('products.create'))
        <a href="{{ route('products.create') }}" class="rounded-xl bg-theme-gradient-r px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 hover:shadow-md transition-all duration-200">
            + Produk Baru
        </a>
        @endif
    </div>
</div>

<div class="mb-6" x-data="{ search: @js(request('search')), category: @js(request('category_id')), stock: @js(request('stock_status')) }">
    <form method="GET" class="flex flex-wrap gap-3">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-warm-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
            <input type="text" name="search" x-model="search" placeholder="Cari nama atau SKU..." class="w-full rounded-xl border-warm-200 pl-10 pr-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
        </div>
        <select name="category_id" x-model="category" class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
        <select name="stock_status" x-model="stock" class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
            <option value="">Semua Stok</option>
            <option value="low">Stok Menipis</option>
        </select>
        <button type="submit" class="rounded-xl bg-warm-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-warm-800 transition-all duration-200 shadow-sm">Filter</button>
    </form>
</div>

<div class="rounded-2xl bg-white shadow-md shadow-warm-900/5 border border-warm-200/50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-warm-100">
            <thead>
                <tr class="bg-warm-50/50">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Harga</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-warm-100">
                @forelse ($products as $product)
                    <tr class="hover:bg-warm-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br bg-theme-primary/10 text-theme-primary text-xs font-bold shadow-sm overflow-hidden">
                                    @if ($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" width="40" height="40" loading="lazy" class="h-full w-full object-cover">
                                    @else
                                        {{ substr($product->name, 0, 2) }}
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-warm-900">{{ $product->name }}</p>
                                    @if($product->description)
                                        <p class="text-xs text-warm-400 truncate max-w-[200px]">{{ $product->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-warm-500 font-mono">{{ $product->sku ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-lg bg-warm-100 px-2.5 py-1 text-xs font-medium text-warm-700">{{ $product->category->name ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-warm-900">
                            @if ($product->sale_price)
                                <span class="text-warm-400 line-through mr-1">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                <span class="text-rose-600">Rp {{ number_format($product->sale_price, 0, ',', '.') }}</span>
                            @else
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if ($product->isUnlimited())
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-sky-50 text-sky-700 border border-sky-200">
                                    ∞ Unlimited
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $product->isLowStock() ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-warm-100 text-warm-700 border border-warm-200' }}">
                                    {{ $product->stock }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $product->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-warm-100 text-warm-500 border border-warm-200' }}">
                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if(auth()->user()->hasPermission('products.edit'))
                            <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center gap-1 rounded-lg bg-theme-primary/10 px-3 py-1.5 text-sm font-medium text-theme-primary hover:bg-theme-primary/20 transition-colors ring-1 ring-theme-primary/20">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                Edit
                            </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="h-12 w-12 text-warm-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M4 7.5h16" /></svg>
                            <p class="text-sm text-warm-400">Belum ada produk.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($products->hasPages())
        <div class="px-6 py-4 border-t border-warm-100 bg-warm-50/30">{{ $products->links() }}</div>
    @endif
</div>
@endsection
