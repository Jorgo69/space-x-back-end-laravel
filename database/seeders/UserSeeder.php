<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création d’un utilisateur standard
        User::create([
            'name' => 'User Simple',
            'email' => 'user@example.com',
            'password' => Hash::make('password'), // mot de passe simple pour le test
            'role' => 'user',
        ]);

        // Création d’un administrateur
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        \Log::info('Seeder UserSeeder exécuté : 1 user + 1 admin créés.');
    }
}
