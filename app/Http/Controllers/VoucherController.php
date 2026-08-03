<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VoucherController extends Controller
{
    public function index(Request $request): View
    {
        $query = Voucher::with('creator');

        if ($search = $request->search) {
            $query->where('code', 'like', "%{$search}%");
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->status === 'active') {
            $query->valid();
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        } elseif ($request->status === 'expired') {
            $query->where('is_active', true)
                ->where('expires_at', '<', now());
        }

        $vouchers = $query->latest()->paginate(15);

        return view('vouchers.index', compact('vouchers'));
    }

    public function create(): View
    {
        return view('vouchers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
            'type' => 'required|in:percentage,nominal',
            'value' => 'required|numeric|min:0',
            'min_order' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:0',
            'max_uses_per_user' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
        ]);

        $validated['min_order'] = $validated['min_order'] ?? 0;
        $validated['max_uses'] = $validated['max_uses'] ?? 0;
        $validated['max_uses_per_user'] = $validated['max_uses_per_user'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');
        $validated['created_by'] = auth()->id();

        Voucher::create($validated);

        return redirect()->route('vouchers.index')->with('success', 'Voucher berhasil dibuat.');
    }

    public function edit(Voucher $voucher): View
    {
        return view('vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'type' => 'required|in:percentage,nominal',
            'value' => 'required|numeric|min:0',
            'min_order' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:0',
            'max_uses_per_user' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
        ]);

        $validated['min_order'] = $validated['min_order'] ?? 0;
        $validated['max_uses'] = $validated['max_uses'] ?? 0;
        $validated['max_uses_per_user'] = $validated['max_uses_per_user'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        $voucher->update($validated);

        return redirect()->route('vouchers.index')->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        if ($voucher->used_count > 0) {
            $voucher->update(['is_active' => false]);

            return redirect()->route('vouchers.index')->with('success', 'Voucher sudah pernah digunakan, hanya dinonaktifkan.');
        }

        $voucher->delete();

        return redirect()->route('vouchers.index')->with('success', 'Voucher berhasil dihapus.');
    }
}
