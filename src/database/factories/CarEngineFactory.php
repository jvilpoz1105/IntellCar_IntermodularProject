<?php

namespace Database\Factories;

use App\Models\CarEngine;
use App\Models\Make;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarEngine>
 */
class CarEngineFactory extends Factory
{
    protected $model = CarEngine::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'engine_name' => fake()->randomElement(['1.0 TSI', '1.5 TSI', '2.0 TDI', '2.0 Turbo', '3.0 V6', 'V8 4.0'])
                .' '.fake()->randomElement(['EVO', 'Sport', 'Hybrid', 'Performance']),
            'engine_description' => fake()->sentence(),
            'fuel_type' => fake()->randomElement(['gasolina', 'diesel', 'electrico', 'hibrido', 'glp']),
            'make_id' => Make::query()->inRandomOrder()->value('make_id') ?? Make::factory(),
        ];
    }
}
