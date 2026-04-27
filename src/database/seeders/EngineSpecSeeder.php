<?php

namespace Database\Seeders;

use App\Models\EngineSpec;
use Illuminate\Database\Seeder;

class EngineSpecSeeder extends Seeder
{
    public function run(): void
    {
        EngineSpec::factory()->count(40)->create();
    }
}
