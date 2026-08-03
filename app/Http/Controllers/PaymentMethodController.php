<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    public function index(): View
    {
        $methods = PaymentMethod::orderBy('group')->orderBy('name')->get();
        return view('settings.payment-methods', compact('methods'));
    }

    public function toggle(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $paymentMethod->update(['is_active' => !$paymentMethod->is_active]);

        $status = $paymentMethod->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('settings.payment-methods.index')->with('success', "Metode pembayaran {$paymentMethod->name} berhasil {$status}.");
    }
}
