<div x-data="roleManager()" class="space-y-6 p-6">
    <div class="flex items-center justify-between pb-4 border-b border-warm-100">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-warm-900">Role & Hak Akses</h3>
                <p class="text-xs text-warm-400">Atur role dan izin yang dimiliki setiap role</p>
            </div>
        </div>
        @if(auth()->user()->hasPermission('roles.create'))
        <button @click="openAdd()" class="rounded-xl bg-theme-gradient-r px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 transition-all">
            + Role Baru
        </button>
        @endif
    </div>

    <div class="grid gap-6">
        @forelse ($roles as $role)
            <div class="rounded-2xl bg-white shadow-md shadow-warm-900/5 border border-warm-200/50 overflow-hidden">
                <div class="px-5 py-4 border-b border-warm-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br bg-theme-primary/10 text-theme-primary font-bold text-sm">
                            {{ strtoupper(substr($role->label, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-warm-900">{{ $role->label }}</h3>
                            <p class="text-xs text-warm-400">
                                <code class="text-warm-300">{{ $role->name }}</code>
                                &middot; {{ $role->users_count }} pengguna
                                @if ($role->description)
                                    &middot; {{ $role->description }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if(auth()->user()->hasPermission('roles.edit') && $role->name !== 'superadmin')
                        <button @click="openEdit({{ Js::from($role) }})" class="rounded-lg p-2 text-warm-500 hover:bg-warm-100 hover:text-warm-700 transition-all" title="Edit">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                        </button>
                        @endif
                        @if(auth()->user()->hasPermission('roles.delete') && !in_array($role->name, ['admin','superadmin','kasir','produksi','gudang','owner']))
                        <form method="POST" action="{{ route('settings.roles.destroy', $role) }}" onsubmit="return confirm('Hapus role {{ $role->label }}?')" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="rounded-lg p-2 text-warm-500 hover:bg-rose-50 hover:text-rose-600 transition-all" title="Hapus">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @php
                    $perms = $role->permissions ?? [];
                    $isWildcard = in_array('*', $perms);
                @endphp
                <div class="px-5 py-3 space-y-2">
                    @if ($isWildcard)
                        <p class="text-sm text-theme-primary font-medium">Semua izin (wildcard)</p>
                    @elseif (empty($perms))
                        <p class="text-sm text-warm-400 italic">Tidak ada izin khusus</p>
                    @else
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($permissionModules as $module)
                                @php
                                    $modulePerms = collect($module['permissions'])->pluck('key')->toArray();
                                    $hasAny = !empty(array_intersect($perms, $modulePerms));
                                    $hasAll = empty(array_diff($modulePerms, $perms));
                                @endphp
                                @if ($hasAny)
                                    <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-medium {{ $hasAll ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-sky-50 text-sky-700 border border-sky-200' }}">
                                        @if ($hasAll)
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                        @endif
                                        {{ $module['label'] }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <p class="text-sm text-warm-400">Belum ada role.</p>
            </div>
        @endforelse
    </div>

    {{-- Add Role Modal --}}
    <template x-teleport="body">
        <div x-show="addOpen" class="fixed inset-0 z-50" x-cloak>
            <div class="fixed inset-0 bg-black/30" @click="addOpen = false"></div>
            <div class="fixed inset-0 flex items-center justify-center p-4">
                <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-xl max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between pb-4 border-b border-warm-100 mb-5">
                        <h3 class="text-base font-semibold text-warm-900">Tambah Role</h3>
                        <button @click="addOpen = false" class="text-warm-400 hover:text-warm-600"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
                    </div>
                    <form method="POST" action="{{ route('settings.roles.store') }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-warm-700 mb-1">Nama Role <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" inputmode="text" required class="block w-full rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-warm-700 mb-1">Label <span class="text-rose-500">*</span></label>
                                <input type="text" name="label" inputmode="text" required class="block w-full rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-warm-700 mb-1">Deskripsi</label>
                            <textarea name="description" rows="2" inputmode="text" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white"></textarea>
                        </div>
                        <div x-data="{ perms: [] }">
                            <label class="block text-sm font-medium text-warm-700 mb-3">Hak Akses</label>
                            <div class="space-y-3 max-h-64 overflow-y-auto border border-warm-200 rounded-xl p-4">
                                @foreach ($permissionModules as $module)
                                    <div>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" class="rounded border-warm-300 text-theme-primary focus:ring-theme-primary/20"
                                                @change="
                                                    const checked = $event.target.checked;
                                                    document.querySelectorAll('#add-perms-{{ $module['key'] }} .perm-cb').forEach(cb => cb.checked = checked);
                                                ">
                                            <span class="text-sm font-semibold text-warm-900">{{ $module['label'] }}</span>
                                        </label>
                                        <div class="ml-6 mt-1 space-y-1" id="add-perms-{{ $module['key'] }}">
                                            @foreach ($module['permissions'] as $perm)
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="checkbox" name="permissions[]" value="{{ $perm['key'] }}"
                                                        class="rounded border-warm-300 text-theme-primary focus:ring-theme-primary/20 perm-cb"
                                                        @change="
                                                            const parent = $event.target.closest('div[id^=\'add-perms-\']');
                                                            const all = parent.querySelectorAll('.perm-cb');
                                                            const checked = parent.querySelectorAll('.perm-cb:checked');
                                                            parent.previousElementSibling.querySelector('input[type=\'checkbox\']').checked = all.length === checked.length;
                                                        ">
                                                    <span class="text-sm text-warm-600">{{ $perm['label'] }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="addOpen = false" class="rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-warm-700 ring-1 ring-warm-200 hover:bg-warm-50 transition-all">Batal</button>
                            <button type="submit" class="rounded-xl bg-theme-gradient-r px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 transition-all">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    {{-- Edit Role Modal --}}
    <template x-teleport="body">
        <div x-show="editOpen" class="fixed inset-0 z-50" x-cloak>
            <div class="fixed inset-0 bg-black/30" @click="editOpen = false"></div>
            <div class="fixed inset-0 flex items-center justify-center p-4">
                <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-xl max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between pb-4 border-b border-warm-100 mb-5">
                        <h3 class="text-base font-semibold text-warm-900">Edit Role</h3>
                        <button @click="editOpen = false" class="text-warm-400 hover:text-warm-600"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
                    </div>
                    <form method="POST" x-bind:action="editAction" x-ref="editForm" class="space-y-4">
                        @csrf @method('PUT')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-warm-700 mb-1">Nama Role <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" x-model="editName" inputmode="text" required class="block w-full rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-warm-700 mb-1">Label <span class="text-rose-500">*</span></label>
                                <input type="text" name="label" x-model="editLabel" inputmode="text" required class="block w-full rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-warm-700 mb-1">Deskripsi</label>
                            <textarea name="description" x-model="editDescription" rows="2" inputmode="text" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-warm-700 mb-3">Hak Akses</label>
                            <div class="space-y-3 max-h-64 overflow-y-auto border border-warm-200 rounded-xl p-4">
                                @foreach ($permissionModules as $module)
                                    <div>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" class="rounded border-warm-300 text-theme-primary focus:ring-theme-primary/20"
                                                @change="
                                                    const checked = $event.target.checked;
                                                    $refs.editForm.querySelectorAll('#edit-perms-{{ $module['key'] }} .perm-cb').forEach(cb => cb.checked = checked);
                                                ">
                                            <span class="text-sm font-semibold text-warm-900">{{ $module['label'] }}</span>
                                        </label>
                                        <div class="ml-6 mt-1 space-y-1" id="edit-perms-{{ $module['key'] }}">
                                            @foreach ($module['permissions'] as $perm)
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="checkbox" name="permissions[]" value="{{ $perm['key'] }}"
                                                        class="rounded border-warm-300 text-theme-primary focus:ring-theme-primary/20 perm-cb"
                                                        @change="
                                                            const parent = $event.target.closest('div[id^=\'edit-perms-\']');
                                                            const all = parent.querySelectorAll('.perm-cb');
                                                            const checked = parent.querySelectorAll('.perm-cb:checked');
                                                            parent.previousElementSibling.querySelector('input[type=\'checkbox\']').checked = all.length === checked.length;
                                                        ">
                                                    <span class="text-sm text-warm-600">{{ $perm['label'] }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="editOpen = false" class="rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-warm-700 ring-1 ring-warm-200 hover:bg-warm-50 transition-all">Batal</button>
                            <button type="submit" class="rounded-xl bg-theme-gradient-r px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 transition-all">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('roleManager', () => ({
        addOpen: false,
        editOpen: false,
        editAction: '',
        editName: '',
        editLabel: '',
        editDescription: '',

        openAdd() {
            this.addOpen = true;
        },

        openEdit(role) {
            this.editAction = '{{ url("settings/roles") }}/' + role.id;
            this.editName = role.name;
            this.editLabel = role.label;
            this.editDescription = role.description || '';
            this.editOpen = true;

            this.$nextTick(() => {
                const perms = role.permissions || [];
                const form = this.$refs.editForm;
                form.querySelectorAll('.perm-cb').forEach(cb => {
                    cb.checked = perms.includes(cb.value);
                });
                form.querySelectorAll('div[id^="edit-perms-"]').forEach(container => {
                    const all = container.querySelectorAll('.perm-cb');
                    const checked = container.querySelectorAll('.perm-cb:checked');
                    const moduleCb = container.previousElementSibling.querySelector('input[type="checkbox"]');
                    if (moduleCb) moduleCb.checked = all.length > 0 && all.length === checked.length;
                });
            });
        }
    }));
});
</script>
@endpush