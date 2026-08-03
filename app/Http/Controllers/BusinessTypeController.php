<?php

namespace App\Http\Controllers;

use App\Models\BusinessType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BusinessTypeController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:business_types,slug',
            'description' => 'nullable|string|max:500',
        ]);

        BusinessType::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('settings.cabang')->with('success', 'Tipe bisnis berhasil ditambahkan.');
    }

    public function update(Request $request, BusinessType $businessType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:business_types,slug,'.$businessType->id,
            'description' => 'nullable|string|max:500',
        ]);

        $businessType->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('settings.cabang')->with('success', 'Tipe bisnis berhasil diperbarui.');
    }

    public function destroy(BusinessType $businessType): RedirectResponse
    {
        if ($businessType->branches()->exists()) {
            return back()->withErrors('Tidak dapat menghapus tipe bisnis yang masih digunakan oleh cabang.');
        }

        $businessType->delete();

        return redirect()->route('settings.cabang')->with('success', 'Tipe bisnis berhasil dihapus.');
    }
}
