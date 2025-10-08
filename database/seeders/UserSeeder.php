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
        // Create Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@tigaputra.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Manager User
        User::create([
            'name' => 'Manager',
            'email' => 'manager@tigaputra.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);
    }
}
