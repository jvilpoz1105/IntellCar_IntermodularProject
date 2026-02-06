<?php

namespace Database\Factories;

use App\Models\CarAdvert;
use App\Models\CarModel;
use App\Models\CarEngine;
use App\Models\AppUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarAdvertFactory extends Factory
{
    protected $model = CarAdvert::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['new', 'km0', 'used', 'renting', 'leasing', 'supcription']);
        $km = $type === 'new' ? 0 : ($type === 'km0' ? fake()->numberBetween(10, 500) : fake()->numberBetween(5000, 200000));
        
        return [
            'ad_title' => fake()->sentence(6),
            'ad_type' => $type,
            'ad_details' => fake()->paragraph(5),
            'price' => fake()->randomFloat(2, 5000, 80000),
            'kilometers' => $km,
            'car_color' => fake()->randomElement(['blanco', 'negro', 'gris', 'plata', 'rojo', 'azul', 'verde', 'amarillo', 'naranja', 'otro']),
            'year_manufacture' => fake()->numberBetween(2010, 2024),
            'region' => fake()->state(),
            'city' => fake()->city(),
            'visible' => fake()->boolean(80), // 80% visibles
            'model_id' => CarModel::inRandomOrder()->first()->model_id,
            'engine_id' => CarEngine::inRandomOrder()->first()->engine_id,
            'seller_id' => AppUser::inRandomOrder()->first()->user_id,
        ];
    }
}
