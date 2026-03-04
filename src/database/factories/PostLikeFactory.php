<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PostLikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => DB::table('app_user')->inRandomOrder()->value('user_id') ?? 1,
            'post_id' => DB::table('post')->inRandomOrder()->value('post_id') ?? 1,
            'created_at' => fake()->dateTimeBetween('-4 months', 'now'),
        ];
    }
}
