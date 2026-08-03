@extends('layouts.app')
@section('title', 'Kategori')
@section('subtitle', 'Kelola kategori produk.')
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="animate-fade-in-up rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50">
        <div class="flex items-center gap-3 pb-4 border-b border-warm-100 mb-5">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br bg-theme-primary/10 text-theme-primary">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            </div>
            <h3 class="text-base font-semibold text-warm-900">Tambah Kategori</h3>
        </div>
        <form method="POST" action="{{ route('categories.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Nama Kategori <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="2" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white"></textarea>
            </div>
            <button type="submit" class="rounded-xl bg-theme-gradient-r px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 transition-all duration-200">
                Simpan
            </button>
        </form>
    </div>

    <div class="animate-fade-in-up rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50" style="animation-delay: 0.1s">
        <div class="flex items-center gap-3 pb-4 border-b border-warm-100 mb-2">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-100 to-sky-50 text-sky-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
            </div>
            <h3 class="text-base font-semibold text-warm-900">Daftar Kategori</h3>
        </div>
        <form method="GET" class="mb-4 mt-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kategori..." class="block w-full rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
        </form>

        @if ($categories->count() > 0)
            <ul class="divide-y divide-warm-100">
                @foreach ($categories as $cat)
                    <li class="py-3.5 flex items-center justify-between group hover:bg-warm-50 -mx-2 px-2 rounded-lg transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-warm-100 text-warm-600 text-xs font-bold">
                                {{ substr($cat->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-warm-900">{{ $cat->name }}</p>
                                <p class="text-xs text-warm-400">{{ $cat->products_count }} produk</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('categories.destroy', $cat) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus kategori ini?')" class="text-sm text-rose-600 hover:text-rose-500 font-medium transition-colors">Hapus</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
            @if ($categories->hasPages())
                <div class="pt-4 border-t border-warm-100 mt-2">{{ $categories->links() }}</div>
            @endif
        @else
            <div class="text-center py-8">
                <svg class="h-10 w-10 text-warm-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
                <p class="text-sm text-warm-400">Belum ada kategori.</p>
            </div>
        @endif
    </div>
</div>
@endsection
