<?php

namespace Database\Factories;

use App\Models\Make;
use Illuminate\Database\Eloquent\Factories\Factory;

class MakeFactory extends Factory
{
    protected $model = Make::class;

    public function definition(): array
    {
        $makes = [
            ['name' => 'Toyota', 'country' => 'Japón', 'status' => 'mass-market'],
            ['name' => 'BMW', 'country' => 'Alemania', 'status' => 'premium'],
            ['name' => 'Mercedes-Benz', 'country' => 'Alemania', 'status' => 'luxury'],
            ['name' => 'Ford', 'country' => 'Estados Unidos', 'status' => 'mass-market'],
            ['name' => 'Volkswagen', 'country' => 'Alemania', 'status' => 'mass-market'],
            ['name' => 'Audi', 'country' => 'Alemania', 'status' => 'premium'],
            ['name' => 'Tesla', 'country' => 'Estados Unidos', 'status' => 'premium'],
            ['name' => 'Honda', 'country' => 'Japón', 'status' => 'mass-market'],
            ['name' => 'Porsche', 'country' => 'Alemania', 'status' => 'luxury'],
            ['name' => 'Dacia', 'country' => 'Rumanía', 'status' => 'low-cost'],
        ];

        $make = fake()->unique()->randomElement($makes);
        
        return [
            'make_name' => $make['name'],
            'origin_country' => $make['country'],
            'official_website' => 'https://www.' . strtolower($make['name']) . '.com',
            'status' => $make['status'],
        ];
    }
}
