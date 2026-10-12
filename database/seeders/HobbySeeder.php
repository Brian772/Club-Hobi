<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HobbySeeder extends Seeder
{
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

        $rows = collect($hobbies)->map(fn ($name) => [
            'name' => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ])->all();
        DB::table('hobbies')->upsert($rows, ['name'], ['updated_at']);
    }
}
