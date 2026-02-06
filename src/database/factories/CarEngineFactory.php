<?php

namespace Database\Factories;

use App\Models\CarEngine;
use App\Models\Make;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarEngineFactory extends Factory
{
    protected $model = CarEngine::class;

    public function definition(): array
    {
        $fuelTypes = ['gasolina', 'diesel', 'electrico', 'hibrido', 'glp'];
        $fuelType = fake()->randomElement($fuelTypes);
        
        $engineNames = [
            'gasolina' => ['1.6 TSI', '2.0 TFSI', '3.0 V6', '1.4 Turbo'],
            'diesel' => ['2.0 TDI', '1.6 HDi', '2.2 CDI', '3.0 TDI'],
            'electrico' => ['Motor Eléctrico 150kW', 'Motor Eléctrico 200kW', 'Dual Motor AWD'],
            'hibrido' => ['1.8 Hybrid', '2.5 Hybrid Synergy Drive', 'Plug-in Hybrid'],
            'glp' => ['1.2 LPG', '1.6 Autogas'],
        ];
        
        return [
            'engine_name' => fake()->randomElement($engineNames[$fuelType]),
            'engine_description' => fake()->sentence(12),
            'fuel_type' => $fuelType,
            'make_id' => Make::inRandomOrder()->first()->make_id,
        ];
    }
}
