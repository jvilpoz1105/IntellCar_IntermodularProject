<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llamar a todos los seeders en orden correcto
        $this->call([
            // 1. Base (No dependencies)
            PaddockSeeder::class,
            MakeSeeder::class,

            // 2. Catalog (Dependencies on Paddock/Make)
            CarModelSeeder::class,
            CarEngineSeeder::class,

            // 3. User related (Dependencies on Paddock)
            AppUserSeeder::class,
            UserFollowSeeder::class,

            // 4. Specs (Dependencies on CarModel/CarEngine)
            ModelSpecSeeder::class,
            EngineSpecSeeder::class,

            // 5. Social (Dependencies on AppUser, CarModel/Engine)
            PostSeeder::class,
            PostMediaSeeder::class,
            PostMoodSeeder::class,
            PostLikeSeeder::class,
            PostCommentSeeder::class,

            // 6. Marketplace (Dependencies on AppUser, CarModel/Engine)
            CarAdvertSeeder::class,
            AdMediaSeeder::class,
            AdvertMoodSeeder::class,

            // 7. Garage, Tools & Notifs (Dependencies on AppUser, CarModel)
            UserGarageSeeder::class,
            SavedSearchSeeder::class,
            NotificationSeeder::class,

            // 8. Events (Dependencies on AppUser, Paddock)
            EventKddSeeder::class,
            EventAttendanceSeeder::class,
        ]);

        $this->command->info('✅ Base de datos poblada correctamente con datos de prueba!');
        $this->command->info('📧 Credenciales de acceso:');
        $this->command->info('   Admin: admin@intellcar.com / admin123');
        $this->command->info('   Pro: carlos@ferrari.com / ferrari123');
        $this->command->info('   Individual: maria.gonzalez@email.com / password123');
        $this->command->info('   Tuning: juan.perez@tuning.com / tuning123');
        $this->command->info('   Press: laura@motorpress.com / press123');
    }
}
