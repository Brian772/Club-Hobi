<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@orbii.com'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Admin Orbii',
                'password_hash' => Hash::make('password'),
                'role_global' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'bio' => 'Administrator Orbii Platform',
            ]
        );

        User::firstOrCreate(
            ['email' => 'richard@example.com'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Richard',
                'password_hash' => Hash::make('password'),
                'role_global' => 'member',
                'status' => 'active',
                'email_verified_at' => now(),
                'interests' => 'Fotografi, Gaming, Olahraga',
                'bio' => 'Antusias fotografi dan olahraga luar ruangan.',
            ]
        );

        User::firstOrCreate(
            ['email' => 'jhon@example.com'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Jhon',
                'password_hash' => Hash::make('password'),
                'role_global' => 'member',
                'status' => 'active',
                'email_verified_at' => now(),
                'interests' => 'Fotografi, Musik, Traveling',
                'bio' => 'Hobi fotografi dan eksplorasi tempat baru.',
            ]
        );

        User::firstOrCreate(
            ['email' => 'rangga@example.com'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Rangga P.',
                'password_hash' => Hash::make('password'),
                'role_global' => 'member',
                'status' => 'active',
                'email_verified_at' => now(),
                'interests' => 'Fotografi, Desain Grafis',
                'bio' => 'Fotografer freelance & street photography lover.',
            ]
        );

        User::firstOrCreate(
            ['email' => 'sinta@example.com'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Sinta W.',
                'password_hash' => Hash::make('password'),
                'role_global' => 'member',
                'status' => 'active',
                'email_verified_at' => now(),
                'interests' => 'Musik, Gaming, Memasak',
                'bio' => 'Pecinta musik indie dan game santai.',
            ]
        );

        // Additional users
        if (app()->environment(['local', 'staging'])) {
            User::factory(10)->create();
        }
    }
}