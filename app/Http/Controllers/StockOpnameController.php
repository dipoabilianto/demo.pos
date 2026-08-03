<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\StockTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockOpnameController extends Controller
{
    public function index(Request $request): View
    {
        $query = RawMaterial::with('transactions');

        if ($search = $request->search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $materials = $query->latest()->paginate(15);
        return view('stock-opname.index', compact('materials'));
    }

    public function adjustForm(RawMaterial $rawMaterial): View
    {
        return view('stock-opname.adjust', compact('rawMaterial'));
    }

    public function adjust(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'raw_material_id' => 'required|exists:raw_materials,id',
            'actual_stock' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated) {
            $material = RawMaterial::where('id', $validated['raw_material_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $difference = $validated['actual_stock'] - $material->current_stock;

            if ($difference != 0) {
                StockTransaction::create([
                    'raw_material_id' => $material->id,
                    'type' => 'opname',
                    'quantity' => $difference,
                    'note' => $validated['note'] ?? 'Penyesuaian stok opname',
                    'user_id' => auth()->id(),
                    'branch_id' => session('branch_id'),
                ]);

                $material->update(['current_stock' => $validated['actual_stock']]);
            }
        });

        return redirect()->route('raw-materials.index', ['tab' => 'opname'])->with('success', 'Stok berhasil disesuaikan.');
    }

    public function history(Request $request): View
    {
        $query = StockTransaction::with('rawMaterial', 'user');

        if ($request->raw_material_id) {
            $query->where('raw_material_id', $request->raw_material_id);
        }
        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('note', 'like', "%{$search}%");
            });
        }

        $transactions = $query->latest()->paginate(30);
        $materials = RawMaterial::orderBy('name')->get();

        return view('stock-opname.history', compact('transactions', 'materials'));
    }
}
