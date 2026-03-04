<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaddockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('paddock')->insert([
            ['paddock_name' => 'Clásicos', 'paddock_description' => 'Comunidad de coches clásicos y vintage'],
            ['paddock_name' => 'Deportivos', 'paddock_description' => 'Amantes de coches deportivos y supercars'],
            ['paddock_name' => 'Tuning', 'paddock_description' => 'Modificaciones y personalización de vehículos'],
            ['paddock_name' => 'Off-Road', 'paddock_description' => 'Todoterrenos y vehículos 4x4'],
            ['paddock_name' => 'Eléctricos', 'paddock_description' => 'Vehículos eléctricos y sostenibles'],
            ['paddock_name' => 'JDM', 'paddock_description' => 'Japanese Domestic Market - Coches japoneses'],
            ['paddock_name' => 'Americanos', 'paddock_description' => 'Muscle cars y vehículos americanos'],
            ['paddock_name' => 'Europeos', 'paddock_description' => 'Coches europeos de lujo y rendimiento'],
        ]);
    }
}
