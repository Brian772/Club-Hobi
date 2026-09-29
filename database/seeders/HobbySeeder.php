<?php

namespace Database\Seeders;

use App\Models\Hobby;
use Illuminate\Database\Seeder;

class HobbySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hobbies = [
            'Photography',
            'Music',
            'Gaming',
            'Traveling',
            'Cooking',
            'Reading',
            'Drawing',
            'Sports',
            'Fitness',
            'Coding',
            'Gardening',
            'Movies',
            'Writing',
            'Cycling',
            'Hiking',
        ];

        foreach ($hobbies as $name) {
            Hobby::firstOrCreate(['name' => $name]);
        }
    }
}
