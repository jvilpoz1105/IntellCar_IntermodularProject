<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\AdMedia;
use Illuminate\Database\Seeder;

class AdMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AdMedia::factory()->count(100)->create();
    }
}
