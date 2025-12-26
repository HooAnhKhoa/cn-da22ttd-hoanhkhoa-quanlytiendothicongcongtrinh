<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin
        User::create([
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'phone' => '0123456789',
            'user_type' => 'admin',
            'status' => 'active',
            'password' => Hash::make('123456'),
            'email_verified_at' => now(),
        ]);

        // Create owners
        User::factory()->count(5)->owner()->create();

        // Create contractors
        User::factory()->count(8)->contractor()->create();

        // Create engineers
        User::factory()->count(12)->engineer()->create();

        $this->command->info('Users seeded successfully!');
        $this->command->info('Total users: ' . User::count());
    }
}