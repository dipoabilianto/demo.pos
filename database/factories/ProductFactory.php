<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->unique()->words(2, true),
            'sku' => fake()->unique()->ean8(),
            'price' => fake()->numberBetween(10000, 50000),
            'cost_price' => fake()->numberBetween(5000, 25000),
            'stock' => 50,
            'min_stock' => 5,
            'is_active' => true,
            'is_unlimited' => false,
            'is_sold_out' => false,
        ];
    }

    public function unlimited(): static
    {
        return $this->state(fn (array $attrs) => [
            'is_unlimited' => true,
            'stock' => 0,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attrs) => [
            'is_active' => false,
        ]);
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attrs) => [
            'stock' => 2,
            'min_stock' => 5,
            'is_unlimited' => false,
        ]);
    }
}
