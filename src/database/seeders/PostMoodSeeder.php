<?php

namespace Database\Seeders;

use App\Models\Paddock;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostMoodSeeder extends Seeder
{
    public function run(): void
    {
        $posts = Post::all();
        $paddocks = Paddock::all();

        foreach ($posts as $post) {
            // Assign 1-2 random moods to each post
            $randomPaddocks = $paddocks->random(rand(1, 2));
            foreach ($randomPaddocks as $paddock) {
                DB::table('post_moods')->insertOrIgnore([
                    'post_id' => $post->post_id,
                    'mood_id' => $paddock->paddock_id,
                ]);
            }
        }
    }
}
