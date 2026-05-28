<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Superadmin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@erp.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'last_seen' => now(),
        ]);

        // 10 regular users
        User::factory(10)->create();
    }
}
