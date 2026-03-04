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
            PaddockSeeder::class,
            MakeSeeder::class,
            CarModelSeeder::class,
            CarEngineSeeder::class,
            AppUserSeeder::class,
            CarAdvertSeeder::class,
            PostSeeder::class,
            UserGarageSeeder::class,
            EventKddSeeder::class,
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
