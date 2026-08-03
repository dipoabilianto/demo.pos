<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Pesanan harus memiliki setidaknya satu produk.',
            'items.min' => 'Pesanan harus memiliki setidaknya satu produk.',
            'items.*.product_id.required' => 'Produk tidak valid.',
            'items.*.product_id.exists' => 'Produk tidak ditemukan.',
            'items.*.quantity.min' => 'Jumlah produk minimal 1.',
        ];
    }
}
