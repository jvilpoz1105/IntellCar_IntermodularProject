<?php

namespace Database\Factories;

use App\Models\AppUser;
use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostComment>
 */
class PostCommentFactory extends Factory
{
    protected $model = PostComment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::query()->inRandomOrder()->value('post_id') ?? Post::factory(),
            'user_id' => AppUser::query()->inRandomOrder()->value('user_id') ?? AppUser::factory(),
            'comment_text' => fake()->sentence(14),
            'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
