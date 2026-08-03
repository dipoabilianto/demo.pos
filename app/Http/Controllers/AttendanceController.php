<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Shift;
use App\Models\ShiftUser;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        private SettingService $settingService,
    ) {}
    public function current(): JsonResponse
    {
        $user = auth()->user();
        if (!$user->canAttendance()) {
            return response()->json(['attendance' => null, 'shifts' => []]);
        }

        $shiftUser = ShiftUser::with('shift')
            ->where('user_id', $user->id)
            ->where('day_of_week', now()->dayOfWeekIso)
            ->first();

        $attendance = Attendance::with('shift')
            ->where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        $settings = $this->settingService->getSettings();

        $shifts = Shift::where('branch_id', $user->branch_id)
            ->active()
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'start_time' => \Carbon\Carbon::parse($s->start_time)->format('H:i'),
                'end_time' => \Carbon\Carbon::parse($s->end_time)->format('H:i'),
                'is_24_hours' => $s->is_24_hours,
            ]);

        return response()->json([
            'has_shift' => ! is_null($shiftUser),
            'shift' => $shiftUser?->shift ? [
                'id' => $shiftUser->shift->id,
                'name' => $shiftUser->shift->name,
                'start_time' => \Carbon\Carbon::parse($shiftUser->shift->start_time)->format('H:i'),
                'end_time' => \Carbon\Carbon::parse($shiftUser->shift->end_time)->format('H:i'),
                'is_24_hours' => $shiftUser->shift->is_24_hours,
            ] : null,
            'shifts' => $shifts,
            'attendance' => $attendance,
            'radius_mode' => $settings['attendance_radius_mode'] ?? 'warning',
            'branch' => $user->branch?->only(['id', 'name', 'latitude', 'longitude', 'radius_meters']),
        ]);
    }

    public function checkIn(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (!$user->canAttendance()) {
            return response()->json(['error' => 'Anda tidak memiliki akses absensi.'], 403);
        }
        $today = now()->toDateString();

        $existing = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($existing) {
            return response()->json(['error' => 'Anda sudah absen hadir hari ini.'], 422);
        }

        $validated = $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'opening_balance' => 'nullable|numeric|min:0',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ]);

        $settings = $this->settingService->getSettings();
        $radiusMode = $settings['attendance_radius_mode'] ?? 'warning';
        $branch = $user->branch;

        if ($radiusMode === 'hard_block' && $branch?->latitude && $branch?->longitude) {
            $distance = $this->haversine(
                (float) $validated['lat'],
                (float) $validated['lng'],
                (float) $branch->latitude,
                (float) $branch->longitude
            );

            if ($distance > ($branch->radius_meters ?: 100)) {
                return response()->json([
                    'error' => 'Anda berada di luar radius lokasi cabang. Jarak: ' . round($distance) . ' m.',
                ], 422);
            }
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'shift_id' => $validated['shift_id'],
            'branch_id' => $branch?->id ?? session('branch_id'),
            'date' => $today,
            'check_in_time' => now()->toTimeString(),
            'check_in_lat' => $validated['lat'] ?? null,
            'check_in_lng' => $validated['lng'] ?? null,
            'opening_balance' => $validated['opening_balance'] ?? 0,
            'status' => 'present',
        ]);

        $warning = null;
        if ($radiusMode === 'warning' && $branch?->latitude && $branch?->longitude) {
            $distance = $this->haversine(
                (float) $validated['lat'],
                (float) $validated['lng'],
                (float) $branch->latitude,
                (float) $branch->longitude
            );
            if ($distance > ($branch->radius_meters ?: 100)) {
                $warning = 'Anda berada di luar radius lokasi cabang. Jarak: ' . round($distance) . ' m.';
            }
        }

        return response()->json([
            'success' => true,
            'attendance' => $attendance,
            'warning' => $warning,
            'message' => 'Absen hadir berhasil. Uang awal shift: Rp ' . number_format($attendance->opening_balance, 0, ',', '.'),
        ]);
    }

    public function checkOut(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (!$user->canAttendance()) {
            return response()->json(['error' => 'Anda tidak memiliki akses absensi.'], 403);
        }
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (! $attendance) {
            return response()->json(['error' => 'Anda belum absen hadir hari ini.'], 422);
        }

        if ($attendance->check_out_time) {
            return response()->json(['error' => 'Anda sudah absen pulang hari ini.'], 422);
        }

        $validated = $request->validate([
            'closing_balance' => 'required|numeric|min:0',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ]);

        $settings = $this->settingService->getSettings();
        $radiusMode = $settings['attendance_radius_mode'] ?? 'warning';
        $branch = $user->branch;

        if ($radiusMode === 'hard_block' && $branch?->latitude && $branch?->longitude) {
            $distance = $this->haversine(
                (float) $validated['lat'],
                (float) $validated['lng'],
                (float) $branch->latitude,
                (float) $branch->longitude
            );

            if ($distance > ($branch->radius_meters ?: 100)) {
                return response()->json([
                    'error' => 'Anda berada di luar radius lokasi cabang. Jarak: ' . round($distance) . ' m.',
                ], 422);
            }
        }

        $diff = (float) $validated['closing_balance'] - (float) $attendance->opening_balance;

        $attendance->update([
            'check_out_time' => now()->toTimeString(),
            'check_out_lat' => $validated['lat'] ?? null,
            'check_out_lng' => $validated['lng'] ?? null,
            'closing_balance' => $validated['closing_balance'],
        ]);

        $warning = null;
        if ($radiusMode === 'warning' && $branch?->latitude && $branch?->longitude) {
            $distance = $this->haversine(
                (float) $validated['lat'],
                (float) $validated['lng'],
                (float) $branch->latitude,
                (float) $branch->longitude
            );
            if ($distance > ($branch->radius_meters ?: 100)) {
                $warning = 'Anda berada di luar radius lokasi cabang. Jarak: ' . round($distance) . ' m.';
            }
        }

        return response()->json([
            'success' => true,
            'attendance' => $attendance->fresh(),
            'warning' => $warning,
            'message' => 'Absen pulang berhasil. Selisih kas: Rp ' . number_format($diff, 0, ',', '.'),
            'diff' => $diff,
        ]);
    }

    public function index(): View
    {
        $user = auth()->user();
        if (!$user->canAttendance()) {
            abort(403, 'Anda tidak memiliki akses absensi.');
        }
        $attendances = Attendance::with('shift', 'user')
            ->where('user_id', $user->id)
            ->latest('date')
            ->paginate(20);

        return view('attendances.index', compact('attendances'));
    }

    public function report(): View
    {
        $user = auth()->user();
        if (!$user->canAttendance()) {
            abort(403, 'Anda tidak memiliki akses absensi.');
        }
        $this->authorize('view', 'attendances.report');

        $attendances = Attendance::with('shift', 'user', 'branch')
            ->latest('date')
            ->paginate(20);

        return view('attendances.report', compact('attendances'));
    }

    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
