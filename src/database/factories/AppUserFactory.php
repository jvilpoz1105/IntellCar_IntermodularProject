<?php

namespace Database\Factories;

use App\Models\AppUser;
use App\Models\Paddock;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppUser>
 */
class AppUserFactory extends Factory
{
    protected $model = AppUser::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_name' => fake()->name(),
            'email_address' => fake()->unique()->safeEmail(),
            'contact_email' => fake()->safeEmail(),
            'address' => fake()->address(),
            'phone' => '+34'.fake()->unique()->numerify('6########'),
            'user_password' => Hash::make('password123'),
            'user_tag' => fake()->randomElement(['admin', 'pro', 'indv', 'press']),
            'registration_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'is_active' => fake()->boolean(90),
            'paddock_id' => fake()->boolean(80)
                ? (Paddock::query()->inRandomOrder()->value('paddock_id') ?? Paddock::factory())
                : null,
        ];
    }
}
