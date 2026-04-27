<?php

namespace Database\Seeders;

use App\Models\Paddock;
use App\Models\CarAdvert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdvertMoodSeeder extends Seeder
{
    public function run(): void
    {
        $adverts = CarAdvert::all();
        $paddocks = Paddock::all();

        foreach ($adverts as $advert) {
            // Assign 1-2 random moods to each advert
            $randomPaddocks = $paddocks->random(rand(1, 2));
            foreach ($randomPaddocks as $paddock) {
                DB::table('advert_moods')->insert([
                    'ad_id' => $advert->ad_id,
                    'mood_id' => $paddock->paddock_id,
                ]);
            }
        }
    }
}
