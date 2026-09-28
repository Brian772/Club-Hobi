<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HobbySeeder extends Seeder
{
    public function run(): void
    {
        $hobbies = [
            'Fotografi', 'Olahraga', 'Musik', 'Gaming', 'Memasak',
            'Membaca', 'Melukis', 'Menulis', 'Traveling', 'Coding',
            'Desain Grafis', 'Film & Sinema', 'Berkebun', 'Kerajinan Tangan',
            'Yoga & Meditasi', 'Hiking', 'Bulu Tangkis', 'Basket', 'Sepak Bola',
            'Berenang', 'Tari', 'Teater', 'Animasi', 'Robotika',
        ];

        foreach ($hobbies as $hobby) {
            DB::table('hobbies')->insertOrIgnore([
                'name'       => $hobby,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}