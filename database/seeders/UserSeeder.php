<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User (with email_verified_at for Laravel 10+)
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@tigaputra.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);

        // Create Manager User (with email_verified_at for Laravel 10+)
        User::create([
            'name' => 'Manager',
            'email' => 'manager@tigaputra.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'manager',
        ]);

        // Create Test User for easy login
        User::create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => Hash::make('123456'),
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);
    }
}
