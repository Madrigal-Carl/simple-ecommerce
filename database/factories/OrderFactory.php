<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference_number' => now()->format('Y').'-'.strtoupper(fake()->unique()->bothify('????####')),
            'user_id' => User::factory(),
            'price' => fake()->randomFloat(2, 1, 5000),
        ];
    }
}
