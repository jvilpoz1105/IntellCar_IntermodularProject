<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EventAttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => DB::table('event_kdd')->inRandomOrder()->value('event_id') ?? 1,
            'user_id' => DB::table('app_user')->inRandomOrder()->value('user_id') ?? 1,
            'joined_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
