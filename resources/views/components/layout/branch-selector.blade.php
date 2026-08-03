@php
    $branches = \App\Models\Branch::active()->get();
    $currentBranchId = session('branch_id');
    $currentBranch = $branches->firstWhere('id', $currentBranchId);
    $user = auth()->user();
    $canSwitch = $user && ($user->isSuperadmin() || $user->isOwner());
@endphp

@if ($canSwitch && $branches->count() > 1)
    <div class="mb-1 border-b border-white/10 pb-2">
        <p class="sidebar-section-label px-3 text-xs font-semibold uppercase tracking-widest mb-1">Cabang</p>
        <div class="space-y-0.5">
            @foreach ($branches as $branch)
                @php $isActive = $branch->id === $currentBranchId; @endphp
                <a href="{{ route('switch-branch', $branch) }}"
                   class="group flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium
                        {{ $isActive ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <span class="h-2 w-2 rounded-full shrink-0 {{ $branch->is_active ? 'bg-emerald-500' : 'bg-stone-300' }}"></span>
                    <span class="flex-1 truncate">{{ $branch->name }}</span>
                    @if ($isActive)
                        <span class="h-1.5 w-1.5 rounded-full bg-theme-primary shadow-sm shadow-theme-shadow"></span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
@endif
