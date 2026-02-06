<?php

namespace Database\Factories;

use App\Models\CarModel;
use App\Models\Make;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarModelFactory extends Factory
{
    protected $model = CarModel::class;

    public function definition(): array
    {
        $models = ['Serie 3', 'Golf', 'Corolla', 'Civic', 'Model 3', 'A4', '911', 'Mustang', 'Clase C', 'Sandero'];
        
        return [
            'model_name' => fake()->randomElement($models) . ' ' . fake()->numberBetween(1, 10),
            'make_id' => Make::inRandomOrder()->first()->make_id,
            'model_description' => fake()->sentence(15),
        ];
    }
}
