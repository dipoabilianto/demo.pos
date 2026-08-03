<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'order_id' => 'nullable|exists:orders,id',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Pesanan harus memiliki setidaknya satu produk.',
            'items.*.product_id.exists' => 'Produk tidak ditemukan.',
            'items.*.quantity.min' => 'Jumlah produk minimal 1.',
        ];
    }
}
