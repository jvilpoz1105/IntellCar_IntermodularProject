<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PostMoodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => DB::table('post')->inRandomOrder()->value('post_id') ?? 1,
            'mood_id' => DB::table('paddock')->inRandomOrder()->value('paddock_id') ?? 1,
        ];
    }
}
