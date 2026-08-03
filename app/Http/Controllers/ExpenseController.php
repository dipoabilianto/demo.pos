<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\RawMaterial;
use App\Models\StockTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Expense::query();

        if ($request->category) {
            $query->where('category', $request->category);
        }
        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        $query->dateRange($request->date_from, $request->date_to, 'expense_date');

        $expenses = $query->latest()->paginate(15);
        $categories = Expense::distinct('category')->pluck('category')->filter();
        $totalExpenses = Expense::sum('amount');

        return view('expenses.index', compact('expenses', 'categories', 'totalExpenses'));
    }

    public function create(): View
    {
        return view('expenses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'expense_date' => 'required|date',
            'raw_material_name' => 'required_if:category,Stok Bahan Baku|string|max:255|nullable',
            'raw_material_unit' => 'required_if:category,Stok Bahan Baku|string|max:20|nullable',
            'stock_quantity' => 'required_if:category,Stok Bahan Baku|numeric|min:0.01|nullable',
        ]);

        $expense = Expense::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'category' => $validated['category'],
            'expense_date' => $validated['expense_date'],
            'branch_id' => session('branch_id'),
        ]);

        if ($request->category === 'Stok Bahan Baku' && $request->raw_material_name && $request->stock_quantity > 0) {
            $material = RawMaterial::firstOrCreate(
                ['name' => trim($request->raw_material_name)],
                ['unit' => $request->raw_material_unit, 'current_stock' => 0, 'min_stock' => 0, 'branch_id' => session('branch_id')],
            );

            StockTransaction::create([
                'raw_material_id' => $material->id,
                'type' => 'in',
                'quantity' => $request->stock_quantity,
                'unit_price' => $request->stock_quantity > 0 ? $request->amount / $request->stock_quantity : 0,
                'note' => 'Pembelian: ' . $request->title,
                'user_id' => auth()->id(),
                'expense_id' => $expense->id,
                'branch_id' => session('branch_id'),
            ]);
            $material->increment('current_stock', $request->stock_quantity);
        }

        return redirect()->route('expenses.index')->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function edit(Expense $expense): View
    {
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'expense_date' => 'required|date',
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        foreach ($expense->stockTransactions as $st) {
            $material = $st->rawMaterial;
            if ($material) {
                $material->decrement('current_stock', $st->quantity);
            }
            $st->delete();
        }

        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
