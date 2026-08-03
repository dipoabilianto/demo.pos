<?php

namespace Database\Factories;

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherFactory extends Factory
{
    protected $model = Voucher::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->bothify('VCH-????####')),
            'type' => 'percentage',
            'value' => 10,
            'min_order' => 0,
            'max_discount' => null,
            'max_uses' => 0,
            'max_uses_per_user' => 0,
            'used_count' => 0,
            'is_active' => true,
        ];
    }

    public function percentage(float $value = 10, ?float $maxDiscount = null): static
    {
        return $this->state(fn (array $attrs) => [
            'type' => 'percentage',
            'value' => $value,
            'max_discount' => $maxDiscount,
        ]);
    }

    public function nominal(float $value = 5000): static
    {
        return $this->state(fn (array $attrs) => [
            'type' => 'nominal',
            'value' => $value,
        ]);
    }

    public function limited(int $maxUses = 1): static
    {
        return $this->state(fn (array $attrs) => [
            'max_uses' => $maxUses,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attrs) => [
            'expires_at' => now()->subDay(),
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attrs) => [
            'is_active' => false,
        ]);
    }
}
