<?php

namespace Database\Seeders;

use App\Models\AppUser;
use App\Models\CarAdvert;
use App\Models\Post;
use App\Models\EventKdd;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\UserGarage;
use Illuminate\Database\Seeder;

class FactoryDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generar 20 usuarios extra
        AppUser::factory(20)->create();

        $users = AppUser::all();

        // Generar 50 anuncios de coches
        CarAdvert::factory(50)->create();

        // Generar 40 posts sociales
        Post::factory(40)->create()->each(function ($post) use ($users) {
            // Cada post tiene entre 1 y 5 comentarios
            PostComment::factory(rand(1, 5))->create(['post_id' => $post->post_id]);
            
            // Cada post tiene entre 5 y 15 likes de usuarios únicos
            $likeCount = rand(5, min(15, $users->count()));
            $likers = $users->random($likeCount);
            foreach ($likers as $user) {
                PostLike::create([
                    'user_id' => $user->user_id,
                    'post_id' => $post->post_id,
                    'created_at' => fake()->dateTimeBetween('-4 months', 'now'),
                ]);
            }
        });

        // Generar 10 eventos KDD
        EventKdd::factory(10)->create();

        // Generar 30 entradas en garajes de usuarios
        UserGarage::factory(30)->create();
    }
}
