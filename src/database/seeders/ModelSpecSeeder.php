<?php

namespace Database\Seeders;

use App\Models\ModelSpec;
use Illuminate\Database\Seeder;

class ModelSpecSeeder extends Seeder
{
    public function run(): void
    {
        ModelSpec::factory()->count(50)->create();
    }
}
