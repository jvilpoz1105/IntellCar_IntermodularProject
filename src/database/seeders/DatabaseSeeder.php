<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Paddock;
use App\Models\Make;
use App\Models\CarModel;
use App\Models\CarEngine;
use App\Models\AppUser;
use App\Models\CarAdvert;
use App\Models\Post;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Paddock::factory()->count(8)->create();

        // 2. Crear Marcas de coches
        Make::factory()->count(10)->create();

        // 3. Crear Modelos de coches
        CarModel::factory()->count(30)->create();

        // 4. Crear Motores
        CarEngine::factory()->count(40)->create();

        // 5. Crear usuarios de prueba
        // Usuario administrador
        AppUser::create([
            'user_name' => 'Admin IntellCar',
            'email_address' => 'admin@intellcar.com',
            'phone' => '+34600000000',
            'user_password' => Hash::make('admin123'),
            'user_tag' => 'admin',
            'is_active' => true,
        ]);

        // Usuario profesional (concesionario)
        AppUser::create([
            'user_name' => 'Concesionario Premium',
            'email_address' => 'dealer@intellcar.com',
            'phone' => '+34600000001',
            'user_password' => Hash::make('dealer123'),
            'user_tag' => 'pro',
            'is_active' => true,
        ]);

        // Usuario individual de prueba
        AppUser::create([
            'user_name' => 'Juan Pérez',
            'email_address' => 'user@intellcar.com',
            'phone' => '+34600000002',
            'user_password' => Hash::make('user123'),
            'user_tag' => 'indv',
            'is_active' => true,
            'paddock_id' => Paddock::inRandomOrder()->first()?->paddock_id,
        ]);

        // Más usuarios aleatorios
        AppUser::factory()->count(20)->create();

        // 6. Crear Anuncios
        CarAdvert::factory()->count(50)->create();

        // 7. Crear Posts
        Post::factory()->count(30)->create();

        $this->command->info('✅ Base de datos poblada correctamente con datos de prueba!');
        $this->command->info('📧 Credenciales de acceso:');
        $this->command->info('   Admin: admin@intellcar.com / admin123');
        $this->command->info('   Dealer: dealer@intellcar.com / dealer123');
        $this->command->info('   User: user@intellcar.com / user123');
    }
}
