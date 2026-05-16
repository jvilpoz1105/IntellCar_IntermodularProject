<?php

namespace Database\Factories;

use App\Models\AppUser;
use App\Models\CarAdvert;
use App\Models\CarEngine;
use App\Models\CarModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarAdvert>
 */
class CarAdvertFactory extends Factory
{
    protected $model = CarAdvert::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ad_title' => $this->faker->sentence(4),
            'ad_type' => $this->faker->randomElement(['new', 'km0', 'used', 'renting', 'leasing', 'supcription']),
            'ad_details' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 4500, 120000),
            'kilometers' => $this->faker->numberBetween(0, 240000),
            'car_color' => $this->faker->randomElement(['blanco', 'negro', 'gris', 'plata', 'rojo', 'azul', 'verde', 'amarillo', 'naranja', 'otro']),
            'year_manufacture' => $this->faker->numberBetween(2000, (int) date('Y')),
            'region' => $this->faker->state(),
            'city' => $this->faker->city(),
            'visible' => $this->faker->boolean(85),
            'publish_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'model_id' => CarModel::query()->inRandomOrder()->value('model_id') ?? CarModel::factory(),
            'engine_id' => CarEngine::query()->inRandomOrder()->value('engine_id') ?? CarEngine::factory(),
            'seller_id' => AppUser::query()->inRandomOrder()->value('user_id') ?? AppUser::factory(),
        ];
    }
}
