<?php

namespace Database\Factories;

use App\Models\Make;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Make>
 */
class MakeFactory extends Factory
{
    protected $model = Make::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'make_name' => fake()->unique()->company(),
            'origin_country' => fake()->country(),
            'official_website' => fake()->url(),
            'status' => fake()->randomElement(['low-cost', 'mass-market', 'premium', 'luxury']),
        ];
    }
}
