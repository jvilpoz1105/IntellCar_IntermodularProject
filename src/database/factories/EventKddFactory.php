<?php

namespace Database\Factories;

use App\Models\AppUser;
use App\Models\EventKdd;
use App\Models\Paddock;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventKdd>
 */
class EventKddFactory extends Factory
{
    protected $model = EventKdd::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'creator_id' => AppUser::query()->inRandomOrder()->value('user_id') ?? AppUser::factory(),
            'paddock_id' => fake()->boolean(80)
                ? (Paddock::query()->inRandomOrder()->value('paddock_id') ?? Paddock::factory())
                : null,
            'title' => 'KDD '.fake()->city().' '.fake()->randomElement(['Nocturna', 'Sport', 'Weekend', 'Friends']),
            'event_description' => fake()->paragraph(),
            'event_date' => fake()->dateTimeBetween('now', '+8 months'),
            'location_name' => fake()->company().' Parking',
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'latitude' => fake()->latitude(36.0, 43.8),
            'longitude' => fake()->longitude(-9.5, 3.5),
            'max_participants' => fake()->randomElement([0, 20, 30, 40, 50, 75, 100]),
        ];
    }
}
