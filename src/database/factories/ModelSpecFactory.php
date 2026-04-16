<?php

namespace Database\Factories;

use App\Models\CarModel;
use App\Models\ModelSpec;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModelSpecFactory extends Factory
{
    protected $model = ModelSpec::class;

    public function definition(): array
    {
        $specs = [
            ['key' => 'Potencia', 'unit' => 'CV'],
            ['key' => 'Par Motor', 'unit' => 'Nm'],
            ['key' => '0-100 km/h', 'unit' => 's'],
            ['key' => 'Velocidad Máxima', 'unit' => 'km/h'],
            ['key' => 'Peso', 'unit' => 'kg'],
            ['key' => 'Capacidad Maletero', 'unit' => 'L'],
        ];

        $spec = fake()->randomElement($specs);

        return [
            'sp_key' => $spec['key'],
            'sp_value' => (string)fake()->randomFloat(1, 4, 600),
            'measurement_unit' => $spec['unit'],
            'variable_type' => 'numeric',
            'sp_model' => CarModel::query()->inRandomOrder()->value('model_id') ?? CarModel::factory(),
        ];
    }
}
