<?php

namespace Database\Seeders;

use App\Models\PostComment;
use Illuminate\Database\Seeder;

class PostCommentSeeder extends Seeder
{
    public function run(): void
    {
        PostComment::factory()->count(100)->create();
    }
}
