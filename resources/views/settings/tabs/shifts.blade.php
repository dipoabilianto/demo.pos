<div class="max-w-7xl mx-auto p-6" x-data="{ addModal: false, addShiftId: null, addDay: null }">
    <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-warm-100 mb-6">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-warm-900">Shift</h3>
                <p class="text-xs text-warm-400">Atur jadwal shift karyawan</p>
            </div>
        </div>
        @if(auth()->user()->hasPermission('shifts.create'))
        <a href="{{ route('settings.shifts.create') }}" class="rounded-xl bg-theme-gradient-r px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 transition-all">
            + Shift Baru
        </a>
        @endif
    </div>

    @foreach ($branches as $branch)
    @php $shifts = $branch->shifts; @endphp
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-stone-200 mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-stone-100">
            <h2 class="text-base font-bold text-stone-900">{{ $branch->name }}</h2>
        </div>
        @if ($shifts->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-stone-50">
                        <th class="text-left px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-stone-400 w-24 min-w-[90px]">Hari</th>
                        @foreach ($shifts as $shift)
                        <th class="text-left px-3 py-3.5 min-w-[150px]">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-semibold text-stone-900 leading-tight">{{ $shift->name }}</div>
                                    <div class="text-xs text-stone-400 mt-0.5">{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }} WIB @if($shift->is_24_hours)<span class="inline-flex items-center gap-0.5 ml-1 text-amber-600 bg-amber-50 px-1 py-0.5 rounded text-[10px] font-medium"><svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>24h</span>@endif</div>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[10px] {{ $shift->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-100 text-stone-400' }} px-2 py-0.5 rounded-full font-medium">{{ $shift->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    @if(auth()->user()->hasPermission('shifts.edit'))
                                    <a href="{{ route('settings.shifts.edit', $shift) }}" class="text-stone-300 hover:text-amber-600 p-1 rounded-md hover:bg-amber-50 transition-all">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dayNames as $dayIndex => $dayName)
                    @php $dayNum = $dayIndex + 1; $isToday = $dayNum === now()->dayOfWeekIso; @endphp
                    <tr class="{{ $isToday ? 'bg-theme-primary/[0.04]' : ($dayIndex % 2 ? 'bg-stone-50/50' : '') }} border-t border-stone-100">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if($isToday)
                                <span class="h-2 w-2 rounded-full bg-theme-primary shrink-0"></span>
                                @endif
                                <span class="text-sm font-semibold {{ $isToday ? 'text-theme-primary' : 'text-stone-500' }}">{{ $dayName }}</span>
                            </div>
                        </td>
                        @foreach ($shifts as $shift)
                        @php $usersOnDay = $shift->schedules->where('day_of_week', $dayNum); @endphp
                        <td class="px-3 py-3 align-top">
                            <div class="flex flex-wrap items-center gap-1.5">
                                @foreach ($usersOnDay as $schedule)
                                <span class="inline-flex items-center gap-1 rounded-lg bg-white ring-1 ring-stone-200 pl-2 pr-1 py-1 text-xs text-stone-700 leading-none group hover:ring-stone-300 transition-all">
                                    <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-stone-200 text-[9px] font-bold text-stone-500 shrink-0">{{ strtoupper(substr($schedule->user->name, 0, 1)) }}</span>
                                    <span class="leading-none py-0.5">{{ $schedule->user->name }}</span>
                                    @if(auth()->user()->hasPermission('shifts.edit'))
                                    <form method="POST" action="{{ route('settings.shifts.schedule.destroy', $schedule) }}" onsubmit="return confirm('Hapus {{ $schedule->user->name }}?')" class="inline-flex items-center self-stretch">
                                        @csrf @method('DELETE')
                                        <button class="inline-flex items-center justify-center text-stone-200 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-all size-4">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                </span>
                                @endforeach
                                @if(auth()->user()->hasPermission('shifts.edit'))
                                <button @click="addModal = true; addShiftId = {{ $shift->id }}; addDay = {{ $dayNum }}" class="inline-flex items-center justify-center rounded-lg border border-dashed border-stone-300 w-7 h-7 text-stone-400 hover:border-theme-primary hover:text-theme-primary hover:bg-theme-primary/5 transition-all" title="Tambah karyawan">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-8 text-center text-sm text-stone-400">Belum ada shift untuk cabang ini.</div>
        @endif
    </div>
    @endforeach

    <template x-teleport="body">
        <div x-show="addModal" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
            <div class="absolute inset-0 bg-black/40" @click="addModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6" @click.outside="addModal = false">
                <h3 class="text-base font-bold text-stone-900 mb-4">Tambah Karyawan</h3>
                <form method="POST" action="{{ route('settings.shifts.schedule.store') }}">
                    @csrf
                    <input type="hidden" name="shift_id" :value="addShiftId">
                    <input type="hidden" name="day_of_week" :value="addDay">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-stone-700 mb-1.5">Karyawan</label>
                        <select name="user_id" class="w-full rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white" required>
                            <option value="">Pilih karyawan</option>
                            @foreach ($branches as $b)
                                @foreach ($b->users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $b->name }})</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="addModal = false" class="rounded-xl px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100 transition-all">Batal</button>
                        <button type="submit" class="rounded-xl bg-theme-gradient-r px-4 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-all">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>