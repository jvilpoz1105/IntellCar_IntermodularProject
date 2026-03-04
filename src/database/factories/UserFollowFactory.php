<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class UserFollowFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $followerId = DB::table('app_user')->inRandomOrder()->value('user_id') ?? 1;
        $followedId = DB::table('app_user')
            ->where('user_id', '!=', $followerId)
            ->inRandomOrder()
            ->value('user_id') ?? $followerId;

        return [
            'follower_id' => $followerId,
            'followed_id' => $followedId,
            'created_at' => fake()->dateTimeBetween('-4 months', 'now'),
        ];
    }
}
