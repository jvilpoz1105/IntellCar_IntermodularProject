<?php

namespace Database\Factories;

use App\Models\Paddock;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaddockFactory extends Factory
{
    protected $model = Paddock::class;

    public function definition(): array
    {
        $moods = ['Clásicos', 'Deportivos', 'Tuning', 'Off-Road', 'Eléctricos', 'Vintage', 'Lujo', 'JDM'];
        
        return [
            'paddock_name' => fake()->unique()->randomElement($moods),
            'paddock_description' => fake()->sentence(10),
        ];
    }
}
