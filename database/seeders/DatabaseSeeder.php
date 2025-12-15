<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat admin user
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'SwiftRail',
            'email' => 'admin@swiftrail.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        // 2. Buat regular user
        User::create([
            'first_name' => 'Andi',
            'last_name' => 'Saputra',
            'email' => 'andi@swiftrail.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
        ]);

        // 3. Buat 10 user random dengan role 'user'
        User::factory(10)->create(['role' => 'user']);
    }
}