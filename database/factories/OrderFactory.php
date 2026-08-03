<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = \App\Models\Order::class;

    public function definition(): array
    {
        return [
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->phoneNumber(),
            'customer_email' => fake()->email(),
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'order_status' => 'pending',
            'subtotal' => 0,
            'discount' => 0,
            'tax' => 0,
            'total' => 0,
            'user_id' => User::factory(),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attrs) => [
            'payment_status' => 'paid',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attrs) => [
            'payment_status' => 'paid',
            'order_status' => 'completed',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attrs) => [
            'payment_status' => 'failed',
            'order_status' => 'cancelled',
        ]);
    }
}
