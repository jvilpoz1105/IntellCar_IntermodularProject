<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarEngineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('car_engine')->insert([
            // Toyota
            ['engine_name' => '1.8 Hybrid', 'engine_description' => 'Motor híbrido eficiente', 'fuel_type' => 'hibrido', 'make_id' => 1],
            ['engine_name' => '2.5 Hybrid', 'engine_description' => 'Híbrido de alto rendimiento', 'fuel_type' => 'hibrido', 'make_id' => 1],
            ['engine_name' => '3.0 Twin-Turbo', 'engine_description' => 'Motor deportivo potente', 'fuel_type' => 'gasolina', 'make_id' => 1],
            
            // BMW
            ['engine_name' => '2.0 TwinPower Turbo', 'engine_description' => 'Gasolina turbo 4 cilindros', 'fuel_type' => 'gasolina', 'make_id' => 2],
            ['engine_name' => '3.0 Inline-6 Turbo', 'engine_description' => '6 cilindros en línea', 'fuel_type' => 'gasolina', 'make_id' => 2],
            ['engine_name' => '2.0d Diesel', 'engine_description' => 'Diésel eficiente', 'fuel_type' => 'diesel', 'make_id' => 2],
            
            // Mercedes-Benz
            ['engine_name' => '2.0 Turbo', 'engine_description' => 'Motor turbo equilibrado', 'fuel_type' => 'gasolina', 'make_id' => 3],
            ['engine_name' => '4.0 V8 Biturbo', 'engine_description' => 'V8 de alto rendimiento', 'fuel_type' => 'gasolina', 'make_id' => 3],
            ['engine_name' => '2.0d BlueEfficiency', 'engine_description' => 'Diésel ecológico', 'fuel_type' => 'diesel', 'make_id' => 3],
            
            // Volkswagen
            ['engine_name' => '1.5 TSI', 'engine_description' => 'Gasolina turbo moderno', 'fuel_type' => 'gasolina', 'make_id' => 4],
            ['engine_name' => '2.0 TDI', 'engine_description' => 'Diésel confiable', 'fuel_type' => 'diesel', 'make_id' => 4],
            
            // Audi
            ['engine_name' => '2.0 TFSI', 'engine_description' => 'Turbo gasolina premium', 'fuel_type' => 'gasolina', 'make_id' => 5],
            ['engine_name' => '3.0 TDI', 'engine_description' => 'V6 diésel potente', 'fuel_type' => 'diesel', 'make_id' => 5],
            
            // Ford
            ['engine_name' => '5.0 V8', 'engine_description' => 'V8 americano clásico', 'fuel_type' => 'gasolina', 'make_id' => 6],
            ['engine_name' => '2.3 EcoBoost', 'engine_description' => '4 cilindros turbo eficiente', 'fuel_type' => 'gasolina', 'make_id' => 6],
            
            // Tesla
            ['engine_name' => 'Dual Motor AWD', 'engine_description' => 'Motor eléctrico de doble eje', 'fuel_type' => 'electrico', 'make_id' => 7],
            ['engine_name' => 'Performance', 'engine_description' => 'Motor eléctrico de máximo rendimiento', 'fuel_type' => 'electrico', 'make_id' => 7],
            
            // Honda
            ['engine_name' => '1.5 VTEC Turbo', 'engine_description' => 'Turbo deportivo', 'fuel_type' => 'gasolina', 'make_id' => 8],
            ['engine_name' => '2.0 i-VTEC', 'engine_description' => 'Motor aspirado suave', 'fuel_type' => 'gasolina', 'make_id' => 8],
            
            // Porsche
            ['engine_name' => '3.0 Turbo Boxer', 'engine_description' => 'Boxer 6 cilindros turbo', 'fuel_type' => 'gasolina', 'make_id' => 9],
            ['engine_name' => '4.0 V8', 'engine_description' => 'V8 de alto rendimiento', 'fuel_type' => 'gasolina', 'make_id' => 9],
            
            // Ferrari
            ['engine_name' => '3.9 V8 Turbo', 'engine_description' => 'V8 biturbo italiano', 'fuel_type' => 'gasolina', 'make_id' => 10],
            
            // SEAT
            ['engine_name' => '1.5 TSI', 'engine_description' => 'Turbo gasolina eficiente', 'fuel_type' => 'gasolina', 'make_id' => 11],
            
            // Dacia
            ['engine_name' => '1.0 TCe', 'engine_description' => 'Turbo gasolina económico', 'fuel_type' => 'gasolina', 'make_id' => 12],
        ]);
    }
}
