<?php

namespace Database\Factories;

use App\Models\AppUser;
use App\Models\CarEngine;
use App\Models\CarModel;
use App\Models\UserGarage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserGarage>
 */
class UserGarageFactory extends Factory
{
    protected $model = UserGarage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => AppUser::query()->inRandomOrder()->value('user_id') ?? AppUser::factory(),
            'model_id' => CarModel::query()->inRandomOrder()->value('model_id') ?? CarModel::factory(),
            'motor_id' => fake()->boolean(80)
                ? (CarEngine::query()->inRandomOrder()->value('engine_id') ?? CarEngine::factory())
                : null,
            'car_nickname' => fake()->boolean(75) ? fake()->words(2, true) : null,
            'description' => fake()->boolean(80) ? fake()->paragraph() : null,
            'is_current_car' => fake()->boolean(65),
            'photo_url' => fake()->boolean(85) ? 'https://fastly.picsum.photos/id/133/2742/1828.jpg' : null,
            'verified_owner' => fake()->boolean(60),
        ];
    }
}
