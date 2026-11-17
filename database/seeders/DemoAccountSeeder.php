<?php

namespace Database\Seeders;

use App\Models\Hobby;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $account = [
            ['name' => 'Demo Admin', 'email' => 'admin@orbii.web.id', 'role' => 'admin'],
            ['name' => 'Demo Member', 'email' => 'member@orbii.web.id', 'role' => 'member'],
        ];

        foreach ($account as $acc) {
            User::updateOrCreate(
                [ 'email' => $acc['email'] ],
                [
                    'name' => $acc['name'],
                    'interests' => Hobby::inRandomOrder()->take(3)->pluck('name')->implode(','),
                    'role_global' => $acc['role'],
                    'password' => bcrypt('PasswordDemo123'),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
