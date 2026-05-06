<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\AdMedia;
use Illuminate\Database\Seeder;

class AdMediaSeeder extends Seeder
{
    /**
     * Run the database 2 seeders seeder.
     */
    public function run(): void
    {
        AdMedia::factory()->count(100)->create();
    }
}
