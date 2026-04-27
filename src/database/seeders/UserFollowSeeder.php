<?php

namespace Database\Seeders;

use App\Models\AppUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserFollowSeeder extends Seeder
{
    public function run(): void
    {
        $users = AppUser::all();
        foreach ($users as $user) {
            // Each user follows a small random number of other users
            $otherUsers = $users->where('user_id', '!=', $user->user_id);
            if ($otherUsers->isNotEmpty()) {
                $count = rand(1, min(5, $otherUsers->count()));
                $followedUsers = $otherUsers->random($count);
                foreach ($followedUsers as $followed) {
                    DB::table('user_follow')->insertOrIgnore([
                        'follower_id' => $user->user_id,
                        'followed_id' => $followed->user_id,
                    ]);
                }
            }
        }
    }
}
