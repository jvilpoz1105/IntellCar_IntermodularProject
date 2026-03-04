<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('car_model')->insert([
            // Toyota
            ['model_name' => 'Corolla', 'make_id' => 1, 'model_description' => 'Sedán compacto confiable'],
            ['model_name' => 'RAV4', 'make_id' => 1, 'model_description' => 'SUV híbrido popular'],
            ['model_name' => 'Supra', 'make_id' => 1, 'model_description' => 'Deportivo icónico'],
            
            // BMW
            ['model_name' => 'Serie 3', 'make_id' => 2, 'model_description' => 'Sedán deportivo premium'],
            ['model_name' => 'X5', 'make_id' => 2, 'model_description' => 'SUV de lujo'],
            ['model_name' => 'M3', 'make_id' => 2, 'model_description' => 'Deportivo de alta gama'],
            
            // Mercedes-Benz
            ['model_name' => 'Clase C', 'make_id' => 3, 'model_description' => 'Sedán ejecutivo'],
            ['model_name' => 'GLE', 'make_id' => 3, 'model_description' => 'SUV premium'],
            ['model_name' => 'AMG GT', 'make_id' => 3, 'model_description' => 'Superdeportivo'],
            
            // Volkswagen
            ['model_name' => 'Golf', 'make_id' => 4, 'model_description' => 'Compacto versátil'],
            ['model_name' => 'Tiguan', 'make_id' => 4, 'model_description' => 'SUV compacto'],
            
            // Audi
            ['model_name' => 'A4', 'make_id' => 5, 'model_description' => 'Sedán premium'],
            ['model_name' => 'Q5', 'make_id' => 5, 'model_description' => 'SUV premium'],
            
            // Ford
            ['model_name' => 'Mustang', 'make_id' => 6, 'model_description' => 'Muscle car americano'],
            ['model_name' => 'F-150', 'make_id' => 6, 'model_description' => 'Pickup robusto'],
            
            // Tesla
            ['model_name' => 'Model 3', 'make_id' => 7, 'model_description' => 'Sedán eléctrico'],
            ['model_name' => 'Model Y', 'make_id' => 7, 'model_description' => 'SUV eléctrico'],
            
            // Honda
            ['model_name' => 'Civic', 'make_id' => 8, 'model_description' => 'Compacto deportivo'],
            ['model_name' => 'CR-V', 'make_id' => 8, 'model_description' => 'SUV familiar'],
            
            // Porsche
            ['model_name' => '911', 'make_id' => 9, 'model_description' => 'Deportivo legendario'],
            ['model_name' => 'Cayenne', 'make_id' => 9, 'model_description' => 'SUV deportivo'],
            
            // Ferrari
            ['model_name' => 'F8 Tributo', 'make_id' => 10, 'model_description' => 'Superdeportivo italiano'],
            
            // SEAT
            ['model_name' => 'León', 'make_id' => 11, 'model_description' => 'Compacto deportivo español'],
            
            // Dacia
            ['model_name' => 'Sandero', 'make_id' => 12, 'model_description' => 'Compacto económico'],
        ]);
    }
}
