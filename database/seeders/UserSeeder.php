<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@wisuda.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create scanner user
        User::create([
            'name' => 'Scanner User',
            'email' => 'scanner@wisuda.com',
            'password' => Hash::make('password'),
            'role' => 'scanner',
            'email_verified_at' => now(),
        ]);
    }
}
