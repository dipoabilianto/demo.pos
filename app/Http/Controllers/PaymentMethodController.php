<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('settings.general', ['tab' => 'payment']);
    }

    public function toggle(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $paymentMethod->update(['is_active' => !$paymentMethod->is_active]);

        $status = $paymentMethod->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('settings.general', ['tab' => 'payment'])->with('success', "Metode pembayaran {$paymentMethod->name} berhasil {$status}.");
    }
}
