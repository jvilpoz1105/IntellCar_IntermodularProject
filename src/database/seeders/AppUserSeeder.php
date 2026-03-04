<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AppUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('app_user')->insert([
            [
                'user_name' => 'Admin IntellCar',
                'email_address' => 'admin@intellcar.com',
                'contact_email' => 'admin@intellcar.com',
                'address' => 'Calle Principal 123, Madrid',
                'phone' => '+34600000001',
                'user_password' => Hash::make('admin123'),
                'user_tag' => 'admin',
                'is_active' => true,
                'paddock_id' => 1,
            ],
            [
                'user_name' => 'Carlos Sainz Jr.',
                'email_address' => 'carlos@ferrari.com',
                'contact_email' => 'carlos.sainz@racing.com',
                'address' => 'Avenida Deportiva 55, Barcelona',
                'phone' => '+34600000002',
                'user_password' => Hash::make('ferrari123'),
                'user_tag' => 'pro',
                'is_active' => true,
                'paddock_id' => 2,
            ],
            [
                'user_name' => 'María González',
                'email_address' => 'maria.gonzalez@email.com',
                'contact_email' => 'maria.gonzalez@email.com',
                'address' => 'Calle Mayor 45, Valencia',
                'phone' => '+34600000003',
                'user_password' => Hash::make('password123'),
                'user_tag' => 'indv',
                'is_active' => true,
                'paddock_id' => 5,
            ],
            [
                'user_name' => 'Juan Pérez',
                'email_address' => 'juan.perez@tuning.com',
                'contact_email' => 'juan@tuning.com',
                'address' => 'Polígono Industrial 7, Sevilla',
                'phone' => '+34600000004',
                'user_password' => Hash::make('tuning123'),
                'user_tag' => 'pro',
                'is_active' => true,
                'paddock_id' => 3,
            ],
            [
                'user_name' => 'Laura Martínez',
                'email_address' => 'laura@motorpress.com',
                'contact_email' => 'laura.martinez@motorpress.com',
                'address' => 'Torre Prensa 10, Madrid',
                'phone' => '+34600000005',
                'user_password' => Hash::make('press123'),
                'user_tag' => 'press',
                'is_active' => true,
                'paddock_id' => 8,
            ],
        ]);
    }
}
