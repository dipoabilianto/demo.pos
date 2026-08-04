<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\ShiftUser;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShiftController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $isSuper = $user->isSuperadmin();

        $branches = $isSuper
            ? \App\Models\Branch::with('shifts.schedules.user', 'users')->get()
            : \App\Models\Branch::where('id', session('branch_id'))->with('shifts.schedules.user', 'users')->get();

        $dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        return view('shifts.index', compact('branches', 'dayNames'));
    }

    public function create(): View
    {
        $branches = auth()->user()->isSuperadmin()
            ? \App\Models\Branch::all()
            : \App\Models\Branch::where('id', session('branch_id'))->get();

        return view('shifts.create', compact('branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'is_24_hours' => 'boolean',
        ]);

        if (!($validated['is_24_hours'] ?? false) && $validated['end_time'] <= $validated['start_time']) {
            return back()->withErrors(['end_time' => 'Jam selesai harus setelah jam mulai untuk shift non-24 jam.'])->withInput();
        }

        $validated['is_24_hours'] = $validated['is_24_hours'] ?? false;

        Shift::create($validated);

        return redirect()->route('settings.shifts.index')->with('success', 'Shift berhasil ditambahkan.');
    }

    private function assertShiftBranchAccess(Shift $shift): void
    {
        if (! auth()->user()->isSuperadmin() && $shift->branch_id !== session('branch_id')) {
            abort(403, 'Anda tidak memiliki akses ke shift cabang lain.');
        }
    }

    public function edit(Shift $shift): View
    {
        $this->assertShiftBranchAccess($shift);

        return view('shifts.edit', compact('shift'));
    }

    public function update(Request $request, Shift $shift): RedirectResponse
    {
        $this->assertShiftBranchAccess($shift);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'is_active' => 'boolean',
            'is_24_hours' => 'boolean',
        ]);

        if (!($validated['is_24_hours'] ?? false) && $validated['end_time'] <= $validated['start_time']) {
            return back()->withErrors(['end_time' => 'Jam selesai harus setelah jam mulai untuk shift non-24 jam.'])->withInput();
        }

        $validated['is_24_hours'] = $validated['is_24_hours'] ?? false;

        $shift->update($validated);

        return redirect()->route('settings.shifts.index')->with('success', 'Shift berhasil diperbarui.');
    }

    public function destroy(Shift $shift): RedirectResponse
    {
        $this->assertShiftBranchAccess($shift);

        $shift->schedules()->delete();
        $shift->delete();

        return redirect()->route('settings.shifts.index')->with('success', 'Shift berhasil dihapus.');
    }

    public function scheduleStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'user_id' => 'required|exists:users,id',
            'day_of_week' => 'required|integer|between:1,7',
        ]);

        $this->assertShiftBranchAccess(Shift::findOrFail($validated['shift_id']));

        $exists = ShiftUser::where($validated)->exists();
        if ($exists) {
            return back()->withErrors('Karyawan sudah terdaftar di shift ini pada hari tersebut.');
        }

        ShiftUser::create($validated);

        return redirect()->route('settings.shifts.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function scheduleDestroy(ShiftUser $schedule): RedirectResponse
    {
        $this->assertShiftBranchAccess($schedule->shift);

        $schedule->delete();

        return redirect()->route('settings.shifts.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}
