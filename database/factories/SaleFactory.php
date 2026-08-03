<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\BusinessType;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'subtotal' => 50000,
            'discount' => 0,
            'tax' => 0,
            'total' => 50000,
            'paid_amount' => 50000,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
        ];
    }
}
