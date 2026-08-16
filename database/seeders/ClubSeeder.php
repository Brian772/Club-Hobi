<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Club;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ClubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment(['local', 'staging'])) {
            $users = User::all();

            if ($users->isEmpty()) {
                $users = User::factory(10)->create();
            }

            $randomClubs = Club::factory(5)->create();

            foreach ($randomClubs as $club) {
                $memberUsers = $users->random(rand(3, min(5, $users->count())));
                foreach ($memberUsers as $user) {
                    DB::table('club_members')->insert([
                        'id' => (string) Str::uuid(),
                        'club_id' => $club->id,
                        'user_id' => $user->id,
                        'joined_at' => fake()->dateTimeBetween('-1 year', 'now'),
                    ]);
                }
            }
        }
    }
}
