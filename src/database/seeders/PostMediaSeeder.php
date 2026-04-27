<?php

namespace Database\Seeders;

use App\Models\PostMedia;
use Illuminate\Database\Seeder;

class PostMediaSeeder extends Seeder
{
    public function run(): void
    {
        PostMedia::factory()->count(100)->create();
    }
}
