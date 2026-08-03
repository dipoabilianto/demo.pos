<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BusinessType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(): View
    {
        $branches = Branch::with('businessTypes')->latest()->get();
        $businessTypes = BusinessType::with('branches')->latest()->get();
        $allBusinessTypes = BusinessType::all();

        return view('settings.cabang', compact('branches', 'businessTypes', 'allBusinessTypes'));
    }

    public function toggleOnline(Request $request, Branch $branch): JsonResponse
    {
        $branch->update(['is_online' => ! $branch->is_online]);

        return response()->json([
            'success' => true,
            'is_online' => $branch->fresh()->is_online,
        ]);
    }

    public function storeBranch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:branches,slug',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'is_online' => 'boolean',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius_meters' => 'nullable|integer|min:10|max:10000',
            'business_types' => 'nullable|array',
            'business_types.*' => 'exists:business_types,id',
        ]);

        $branch = Branch::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'is_online' => $request->boolean('is_online'),
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'radius_meters' => $validated['radius_meters'] ?? 100,
        ]);

        if (! empty($validated['business_types'])) {
            $branch->businessTypes()->attach($validated['business_types']);
        }

        return redirect()->route('settings.cabang')->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function updateBranch(Request $request, Branch $branch): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:branches,slug,'.$branch->id,
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'is_online' => 'boolean',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius_meters' => 'nullable|integer|min:10|max:10000',
            'business_types' => 'nullable|array',
            'business_types.*' => 'exists:business_types,id',
        ]);

        $branch->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'is_online' => $request->boolean('is_online'),
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'radius_meters' => $validated['radius_meters'] ?? 100,
        ]);

        $branch->businessTypes()->sync($validated['business_types'] ?? []);

        return redirect()->route('settings.cabang')->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroyBranch(Branch $branch): RedirectResponse
    {
        if ($branch->users()->exists()) {
            return back()->withErrors('Tidak dapat menghapus cabang yang masih memiliki karyawan.');
        }

        $branch->delete();

        return redirect()->route('settings.cabang')->with('success', 'Cabang berhasil dihapus.');
    }
}
