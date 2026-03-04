<?php

namespace Database\Factories;

use App\Models\AppUser;
use App\Models\CarEngine;
use App\Models\CarModel;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'author_id' => AppUser::query()->inRandomOrder()->value('user_id') ?? AppUser::factory(),
            'title' => fake()->boolean(85) ? fake()->sentence(6) : null,
            'content' => fake()->paragraphs(2, true),
            'model_id' => fake()->boolean(60)
                ? (CarModel::query()->inRandomOrder()->value('model_id') ?? CarModel::factory())
                : null,
            'engine_id' => fake()->boolean(60)
                ? (CarEngine::query()->inRandomOrder()->value('engine_id') ?? CarEngine::factory())
                : null,
            'created_at' => fake()->dateTimeBetween('-8 months', 'now'),
        ];
    }
}
