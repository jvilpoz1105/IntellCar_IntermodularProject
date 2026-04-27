<?php

namespace Database\Seeders;

use App\Models\AppUser;
use App\Models\EventKdd;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $events = EventKdd::all();
        $users = AppUser::all();

        foreach ($events as $event) {
            // Randomly join 3-10 users to each event
            $participants = $users->random(rand(3, min(10, $users->count())));
            foreach ($participants as $user) {
                DB::table('event_attendance')->insert([
                    'event_id' => $event->event_id,
                    'user_id' => $user->user_id,
                    'joined_at' => now(),
                ]);
            }
        }
    }
}
