<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserGarageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_garage')->insert([
            [
                'user_id' => 2, // Carlos
                'model_id' => 22, // Ferrari F8
                'motor_id' => 22,
                'car_nickname' => 'La Rossa',
                'description' => 'Mi sueño hecho realidad. La compré después de mi primer podio en F1. Cada vez que la conduzco, me recuerda por qué amo este deporte.',
                'is_current_car' => true,
                'photo_url' => 'https://example.com/ferrari-carlos.jpg',
                'verified_owner' => true,
            ],
            [
                'user_id' => 2,
                'model_id' => 14, // Ford Mustang
                'motor_id' => 14,
                'car_nickname' => 'El Americano',
                'description' => 'Mi primer muscle car. Lo tuve durante 3 años antes de pasarme a coches europeos. Muchos recuerdos y kilómetros.',
                'is_current_car' => false,
                'photo_url' => 'https://example.com/mustang-carlos.jpg',
                'verified_owner' => true,
            ],
            [
                'user_id' => 3, // María
                'model_id' => 16, // Tesla Model 3
                'motor_id' => 16,
                'car_nickname' => 'El Silencioso',
                'description' => 'Mi primer coche eléctrico y no podría estar más contenta. Económico, tecnológico y sostenible. Perfecto para la ciudad.',
                'is_current_car' => true,
                'photo_url' => 'https://example.com/tesla-maria.jpg',
                'verified_owner' => true,
            ],
            [
                'user_id' => 4, // Juan (tuning)
                'model_id' => 10, // Golf
                'motor_id' => 10,
                'car_nickname' => 'El Bicho',
                'description' => 'Mi proyecto de tuning. Stage 2, llantas de 19", suspensión deportiva... ¡350 CV de pura adrenalina!',
                'is_current_car' => true,
                'photo_url' => 'https://example.com/golf-juan.jpg',
                'verified_owner' => true,
            ],
            [
                'user_id' => 4,
                'model_id' => 18, // Honda Civic
                'motor_id' => 18,
                'car_nickname' => 'El Primer Amor',
                'description' => 'Mi primer coche. Un Civic Type R con el que aprendí todo sobre mecánica y tuning. Lo vendí hace 2 años pero siempre lo recordaré.',
                'is_current_car' => false,
                'photo_url' => 'https://example.com/civic-juan.jpg',
                'verified_owner' => true,
            ],
        ]);
    }
}
