<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paddock>
 */
class PaddockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'paddock_name' => fake()->unique()->randomElement([
                'Clásicos',
                'Deportivos',
                'Tuning',
                'Off-Road',
                'Eléctricos',
                'JDM',
                'Americanos',
                'Europeos',
            ]).' '.fake()->unique()->numberBetween(1, 999),
            'paddock_description' => fake()->sentence(),
        ];
    }
}
