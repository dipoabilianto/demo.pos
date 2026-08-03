<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\StockTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RawMaterialController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'list');

        if ($tab === 'opname') {
            $query = RawMaterial::with('transactions');

            if ($search = $request->search) {
                $query->where('name', 'like', "%{$search}%");
            }

            $materials = $query->latest()->paginate(15);
            return view('raw-materials.index', compact('materials', 'tab'));
        }

        $query = RawMaterial::query();

        if ($search = $request->search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $materials = $query->latest()->paginate(20);
        return view('raw-materials.index', compact('materials', 'tab'));
    }

    public function create(): View
    {
        return view('raw-materials.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:20',
            'current_stock' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
        ]);

        RawMaterial::create($validated);

        return redirect()->route('raw-materials.index')->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function edit(RawMaterial $rawMaterial): View
    {
        return view('raw-materials.edit', compact('rawMaterial'));
    }

    public function update(Request $request, RawMaterial $rawMaterial): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:20',
            'current_stock' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
        ]);

        $rawMaterial->update($validated);

        return redirect()->route('raw-materials.index')->with('success', 'Bahan baku berhasil diperbarui.');
    }

    public function destroy(RawMaterial $rawMaterial): RedirectResponse
    {
        $rawMaterial->delete();
        return redirect()->route('raw-materials.index')->with('success', 'Bahan baku berhasil dihapus.');
    }
}
