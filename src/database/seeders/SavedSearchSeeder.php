<?php

namespace Database\Seeders;

use App\Models\SavedSearch;
use Illuminate\Database\Seeder;

class SavedSearchSeeder extends Seeder
{
    public function run(): void
    {
        SavedSearch::factory()->count(20)->create();
    }
}
