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
        User::factory()->isAdmin()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('Hey.Admin.1234!'),
        ]);

        User::factory()->create([
            'name' => 'Normal User',
            'email' => 'normal@test.com',
            'password' => Hash::make('Hey.1234!'),
        ]);

        User::factory()->createMany(50);
    }
}
