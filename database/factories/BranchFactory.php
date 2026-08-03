<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => fake()->unique()->slug(),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
            'is_online' => true,
        ];
    }

    public function offline(): static
    {
        return $this->state(fn (array $attrs) => [
            'is_online' => false,
        ]);
    }
}
