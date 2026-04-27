<?php

namespace Database\Seeders;

use App\Models\AppUser;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostLikeSeeder extends Seeder
{
    public function run(): void
    {
        $posts = Post::all();
        $users = AppUser::all();

        foreach ($posts as $post) {
            // Randomly like posts (3-15 likes per post)
            $likers = $users->random(rand(3, min(15, $users->count())));
            foreach ($likers as $user) {
                DB::table('post_like')->insertOrIgnore([
                    'user_id' => $user->user_id,
                    'post_id' => $post->post_id,
                ]);
            }
        }
    }
}
