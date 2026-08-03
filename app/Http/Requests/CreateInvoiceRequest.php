<?php

namespace App\Http\Requests;

use App\Models\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;

class CreateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $activeCodes = PaymentMethod::active()->pluck('code')->toArray();
        if (! in_array('xendit', $activeCodes)) {
            $activeCodes[] = 'xendit';
        }

        return [
            'payment_method' => 'required|string|in:' . implode(',', $activeCodes),
            'is_public' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => 'Metode pembayaran harus dipilih.',
            'payment_method.in' => 'Metode pembayaran tidak valid.',
        ];
    }
}
