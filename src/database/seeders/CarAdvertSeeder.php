<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarAdvertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('car_advert')->insert([
            [
                'ad_title' => 'Toyota Corolla Hybrid 2023 - Como nuevo',
                'ad_type' => 'km0',
                'ad_details' => 'Vehículo con solo 2.000 km, garantia oficial, todos los extras',
                'price' => 28500.00,
                'kilometers' => 2000,
                'car_color' => 'blanco',
                'year_manufacture' => 2023,
                'region' => 'Madrid',
                'city' => 'Madrid',
                'visible' => true,
                'model_id' => 1,
                'engine_id' => 1,
                'seller_id' => 2,
            ],
            [
                'ad_title' => 'BMW Serie 3 2021 - Impecable',
                'ad_type' => 'used',
                'ad_details' => 'Un solo propietario, revisión reciente, paquete M Sport',
                'price' => 42000.00,
                'kilometers' => 35000,
                'car_color' => 'negro',
                'year_manufacture' => 2021,
                'region' => 'Cataluña',
                'city' => 'Barcelona',
                'visible' => true,
                'model_id' => 4,
                'engine_id' => 4,
                'seller_id' => 3,
            ],
            [
                'ad_title' => 'Tesla Model 3 Performance - Eléctrico puro',
                'ad_type' => 'used',
                'ad_details' => 'Autopilot mejorado, actualizaciones de software al día, batería 100%',
                'price' => 52000.00,
                'kilometers' => 18000,
                'car_color' => 'rojo',
                'year_manufacture' => 2022,
                'region' => 'Comunidad Valenciana',
                'city' => 'Valencia',
                'visible' => true,
                'model_id' => 16,
                'engine_id' => 17,
                'seller_id' => 3,
            ],
            [
                'ad_title' => 'Ford Mustang 5.0 V8 - Muscle Car',
                'ad_type' => 'used',
                'ad_details' => 'V8 de 450 CV, escape deportivo, llantas de 19", interior en Recaro',
                'price' => 45000.00,
                'kilometers' => 28000,
                'car_color' => 'azul',
                'year_manufacture' => 2020,
                'region' => 'Andalucía',
                'city' => 'Sevilla',
                'visible' => true,
                'model_id' => 14,
                'engine_id' => 14,
                'seller_id' => 4,
            ],
            [
                'ad_title' => 'Porsche 911 Carrera - Deportivo legendario',
                'ad_type' => 'used',
                'ad_details' => 'Mantenimiento oficial Porsche, libro completo, estado impecable',
                'price' => 98000.00,
                'kilometers' => 45000,
                'car_color' => 'plata',
                'year_manufacture' => 2019,
                'region' => 'Madrid',
                'city' => 'Madrid',
                'visible' => true,
                'model_id' => 20,
                'engine_id' => 20,
                'seller_id' => 2,
            ],
            [
                'ad_title' => 'SEAT León FR 2024 - Nuevo',
                'ad_type' => 'new',
                'ad_details' => 'Vehículo nuevo a estrenar, Full LED, Digital Cockpit, navegador',
                'price' => 32000.00,
                'kilometers' => 0,
                'car_color' => 'gris',
                'year_manufacture' => 2024,
                'region' => 'Cataluña',
                'city' => 'Barcelona',
                'visible' => true,
                'model_id' => 23,
                'engine_id' => 23,
                'seller_id' => 4,
            ],
        ]);
    }
}
