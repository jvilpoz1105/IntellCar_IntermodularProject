<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('make')->insert([
            ['make_name' => 'Toyota', 'origin_country' => 'Japón', 'official_website' => 'https://www.toyota.com', 'status' => 'mass-market'],
            ['make_name' => 'BMW', 'origin_country' => 'Alemania', 'official_website' => 'https://www.bmw.com', 'status' => 'premium'],
            ['make_name' => 'Mercedes-Benz', 'origin_country' => 'Alemania', 'official_website' => 'https://www.mercedes-benz.com', 'status' => 'luxury'],
            ['make_name' => 'Volkswagen', 'origin_country' => 'Alemania', 'official_website' => 'https://www.volkswagen.com', 'status' => 'mass-market'],
            ['make_name' => 'Audi', 'origin_country' => 'Alemania', 'official_website' => 'https://www.audi.com', 'status' => 'premium'],
            ['make_name' => 'Ford', 'origin_country' => 'Estados Unidos', 'official_website' => 'https://www.ford.com', 'status' => 'mass-market'],
            ['make_name' => 'Tesla', 'origin_country' => 'Estados Unidos', 'official_website' => 'https://www.tesla.com', 'status' => 'premium'],
            ['make_name' => 'Honda', 'origin_country' => 'Japón', 'official_website' => 'https://www.honda.com', 'status' => 'mass-market'],
            ['make_name' => 'Porsche', 'origin_country' => 'Alemania', 'official_website' => 'https://www.porsche.com', 'status' => 'luxury'],
            ['make_name' => 'Ferrari', 'origin_country' => 'Italia', 'official_website' => 'https://www.ferrari.com', 'status' => 'luxury'],
            ['make_name' => 'SEAT', 'origin_country' => 'España', 'official_website' => 'https://www.seat.com', 'status' => 'mass-market'],
            ['make_name' => 'Dacia', 'origin_country' => 'Rumanía', 'official_website' => 'https://www.dacia.com', 'status' => 'low-cost'],
        ]);
    }
}
