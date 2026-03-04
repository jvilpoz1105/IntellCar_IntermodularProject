<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventKddSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('event_kdd')->insert([
            [
                'creator_id' => 2,
                'paddock_id' => 2, // Deportivos
                'title' => 'Kdd Deportivos Madrid - Circuito del Jarama',
                'event_description' => 'Jornada de track day en el circuito del Jarama. Abierto a todos los coches deportivos. Incluye tandas libres, instrucción básica y comida.',
                'event_date' => '2026-04-15 09:00:00',
                'location_name' => 'Circuito del Jarama',
                'address' => 'Autovía A-1, km 28',
                'city' => 'San Sebastián de los Reyes',
                'latitude' => 40.6218,
                'longitude' => -3.5858,
                'max_participants' => 30,
            ],
            [
                'creator_id' => 4,
                'paddock_id' => 3, // Tuning
                'title' => 'Concentración Tuning Barcelona',
                'event_description' => 'Quedada tuning en el parking del Port Olímpic. Trae tu proyecto, comparte experiencias y haz fotos. Premios a los mejores proyectos.',
                'event_date' => '2026-03-20 18:00:00',
                'location_name' => 'Parking Port Olímpic',
                'address' => 'Marina, 19-21',
                'city' => 'Barcelona',
                'latitude' => 41.3874,
                'longitude' => 2.1967,
                'max_participants' => 0, // Ilimitado
            ],
            [
                'creator_id' => 3,
                'paddock_id' => 5, // Eléctricos
                'title' => 'Ruta Eléctrica Valencia - Experiencia Zero Emisiones',
                'event_description' => 'Ruta turística en vehículos eléctricos por la Costa Valenciana. Pararemos en puntos de carga rápida. Perfecto para conocer otros propietarios de EVs.',
                'event_date' => '2026-03-28 10:00:00',
                'location_name' => 'Ciudad de las Artes y las Ciencias',
                'address' => 'Av. del Professor López Piñero, 7',
                'city' => 'Valencia',
                'latitude' => 39.4567,
                'longitude' => -0.3507,
                'max_participants' => 20,
            ],
            [
                'creator_id' => 2,
                'paddock_id' => 1, // Clásicos
                'title' => 'Encuentro de Clásicos - Retiro Madrid',
                'event_description' => 'Exhibición de coches clásicos en el Parque del Retiro. Solo vehículos de más de 30 años. Ambiente familiar y fotografías.',
                'event_date' => '2026-05-10 11:00:00',
                'location_name' => 'Parque del Retiro',
                'address' => 'Plaza de la Independencia, 7',
                'city' => 'Madrid',
                'latitude' => 40.4153,
                'longitude' => -3.6844,
                'max_participants' => 50,
            ],
        ]);
    }
}
