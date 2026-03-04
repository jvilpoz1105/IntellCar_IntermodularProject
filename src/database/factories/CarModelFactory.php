<?php

namespace Database\Factories;

use App\Models\CarModel;
use App\Models\Make;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarModel>
 */
class CarModelFactory extends Factory
{
    protected $model = CarModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'model_name' => fake()->unique()->randomElement([
                'Corolla', 'RAV4', 'Supra', 'Serie 3', 'M3', 'Clase C', 'A4', 'Golf',
                'Mustang', 'Model 3', 'Model Y', 'Civic', '911', 'Cayenne', 'F8', 'León'
            ]).' '.fake()->numberBetween(1, 999),
            'make_id' => Make::query()->inRandomOrder()->value('make_id') ?? Make::factory(),
            'model_description' => fake()->sentence(),
        ];
    }
}
