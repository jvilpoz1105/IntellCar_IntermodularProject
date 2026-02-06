<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\AppUser;
use App\Models\CarModel;
use App\Models\CarEngine;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'author_id' => AppUser::inRandomOrder()->first()->user_id,
            'title' => fake()->sentence(8),
            'content' => fake()->paragraphs(3, true),
            'model_id' => fake()->optional(0.7)->randomElement(CarModel::pluck('model_id')->toArray()),
            'engine_id' => fake()->optional(0.5)->randomElement(CarEngine::pluck('engine_id')->toArray()),
        ];
    }
}
