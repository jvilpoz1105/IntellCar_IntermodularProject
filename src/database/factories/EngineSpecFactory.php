<?php

namespace Database\Factories;

use App\Models\CarEngine;
use App\Models\EngineSpec;
use Illuminate\Database\Eloquent\Factories\Factory;

class EngineSpecFactory extends Factory
{
    protected $model = EngineSpec::class;

    public function definition(): array
    {
        $specs = [
            ['key' => 'Cilindrada', 'unit' => 'cc'],
            ['key' => 'Número de Cilindros', 'unit' => ''],
            ['key' => 'Relación de Compresión', 'unit' => ':1'],
            ['key' => 'Presión Turbo', 'unit' => 'bar'],
            ['key' => 'Consumo Medio', 'unit' => 'l/100km'],
        ];

        $spec = fake()->randomElement($specs);

        return [
            'sp_key' => $spec['key'],
            'sp_value' => (string)fake()->randomFloat(1, 1, 3000),
            'measurement_unit' => $spec['unit'],
            'variable_type' => 'numeric',
            'sp_engine' => CarEngine::query()->inRandomOrder()->value('engine_id') ?? CarEngine::factory(),
        ];
    }
}
