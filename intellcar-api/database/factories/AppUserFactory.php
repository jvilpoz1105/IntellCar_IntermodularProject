<?php

namespace Database\Factories;

use App\Models\AppUser;
use App\Models\Paddock;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class AppUserFactory extends Factory
{
    protected $model = AppUser::class;

    public function definition(): array
    {
        return [
            'user_name' => fake()->name(),
            'email_address' => fake()->unique()->safeEmail(),
            'contact_email' => fake()->optional()->safeEmail(),
            'address' => fake()->address(),
            'phone' => fake()->unique()->phoneNumber(),
            'user_password' => Hash::make('password123'),
            'user_tag' => fake()->randomElement(['admin', 'pro', 'indv', 'press']),
            'is_active' => true,
            'paddock_id' => Paddock::inRandomOrder()->first()?->paddock_id,
        ];
    }
}
