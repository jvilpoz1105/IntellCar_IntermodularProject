<?php

namespace Database\Factories;

use App\Models\AppUser;
use App\Models\SavedSearch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SavedSearch>
 */
class SavedSearchFactory extends Factory
{
    protected $model = SavedSearch::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => AppUser::query()->inRandomOrder()->value('user_id') ?? AppUser::factory(),
            'search_name' => fake()->randomElement(['SUV familiar', 'Compactos baratos', 'Eléctricos 2024', 'Deportivos usados']),
            'filters_json' => [
                'price_min' => fake()->numberBetween(0, 10000),
                'price_max' => fake()->numberBetween(12000, 120000),
                'year_min' => fake()->numberBetween(2005, 2022),
                'fuel_type' => fake()->randomElement(['gasolina', 'diesel', 'electrico', 'hibrido', 'glp']),
                'city' => fake()->city(),
            ],
            'created_at' => fake()->dateTimeBetween('-5 months', 'now'),
        ];
    }
}
