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
            'user_name' => $this->faker->name(),
            'email_address' => $this->faker->unique()->safeEmail(),
            'contact_email' => $this->faker->safeEmail(),
            'address' => $this->faker->address(),
            'phone' => '+34'.$this->faker->unique()->numerify('6########'),
            'user_password' => Hash::make('password123'),
            'user_tag' => $this->faker->randomElement(['admin', 'pro', 'indv', 'press']),
            'registration_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'is_active' => $this->faker->boolean(90),
            'paddock_id' => $this->faker->boolean(80)
                ? (Paddock::query()->inRandomOrder()->value('paddock_id') ?? Paddock::factory())
                : null,
        ];
    }
}
