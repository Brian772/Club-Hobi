<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Hobby;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            return;
        }

        $richard = User::where('email', 'richard@example.com')->first() ?? $users->first();
        $jhon = User::where('email', 'jhon@example.com')->first();
        $rangga = User::where('email', 'rangga@example.com')->first();
        $sinta = User::where('email', 'sinta@example.com')->first();

        $hobbies = Hobby::all();

        $findHobbyId = function ($name) use ($hobbies) {
            $hobby = $hobbies->firstWhere('name', $name);
            return $hobby ? $hobby->id : ($hobbies->first() ? $hobbies->first()->id : 1);
        };

        $sampleClubs = [
            [
                'name' => 'Komunitas Lensa & Fotografi',
                'hobby_id' => $findHobbyId('Fotografi'),
                'description' => 'Tempat berkumpulnya pecinta fotografi untuk sharing karya, tips hunting, dan jadwal photowalk bersama.',
                'cover_url' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=800&q=80',
                'creator_id' => $rangga ? $rangga->id : $richard->id,
            ],
            [
                'name' => 'Indo Gamers Guild',
                'hobby_id' => $findHobbyId('Gaming'),
                'description' => 'Klub mabar game PC, Console, dan Mobile. Diskusi seputar game terbaru dan turnamen komunitas.',
                'cover_url' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=800&q=80',
                'creator_id' => $sinta ? $sinta->id : $richard->id,
            ],
            [
                'name' => 'Harmoni Musik Akustik',
                'hobby_id' => $findHobbyId('Musik'),
                'description' => 'Komunitas pecinta musik instrumen akustik, jamming santai, cover lagu, dan sharing chord & aransemen.',
                'cover_url' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=800&q=80',
                'creator_id' => $richard->id,
            ],
            [
                'name' => 'Klub Badminton Smashing',
                'hobby_id' => $findHobbyId('Bulu Tangkis'),
                'description' => 'Jadwal latihan badminton rutin setiap minggu, sparing antar klub, dan olahraga bersama menjaga kebugaran.',
                'cover_url' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?auto=format&fit=crop&w=800&q=80',
                'creator_id' => $richard->id,
            ],
            [
                'name' => 'Dapur Kreatif & Kuliner',
                'hobby_id' => $findHobbyId('Memasak'),
                'description' => 'Eksplorasi resep masakan Nusantara dan dunia, tips baking pastry, dan review kuliner hits.',
                'cover_url' => 'https://images.unsplash.com/photo-1556910103-1c02745aae4d?auto=format&fit=crop&w=800&q=80',
                'creator_id' => $sinta ? $sinta->id : $users->last()->id,
            ],
            [
                'name' => 'Komunitas Coding & Tech',
                'hobby_id' => $findHobbyId('Coding'),
                'description' => 'Belajar bersama web development, AI, mobile app, dan kolaborasi open-source project.',
                'cover_url' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=800&q=80',
                'creator_id' => $richard->id,
            ],
        ];

        foreach ($sampleClubs as $clubData) {
            $clubId = (string) Str::uuid();
            $club = Club::create([
                'id' => $clubId,
                'name' => $clubData['name'],
                'hobby_id' => $clubData['hobby_id'],
                'description' => $clubData['description'],
                'created_by' => $clubData['creator_id'],
                'cover_url' => $clubData['cover_url'],
            ]);

            // Add creator as owner
            DB::table('club_members')->insert([
                'id' => (string) Str::uuid(),
                'club_id' => $club->id,
                'user_id' => $clubData['creator_id'],
                'role' => 'owner',
                'joined_at' => now()->subMonths(3),
            ]);

            // Add Richard and Jhon as members to popular clubs so "Club yang anda ikuti" has data
            foreach (array_filter([$richard, $jhon]) as $keyUser) {
                if ($keyUser->id !== $clubData['creator_id']) {
                    DB::table('club_members')->insertOrIgnore([
                        'id' => (string) Str::uuid(),
                        'club_id' => $club->id,
                        'user_id' => $keyUser->id,
                        'role' => 'member',
                        'joined_at' => now()->subMonths(1),
                    ]);
                }
            }

            // Add random users as members
            $randomUsers = $users->random(min(4, $users->count()));
            foreach ($randomUsers as $u) {
                DB::table('club_members')->insertOrIgnore([
                    'id' => (string) Str::uuid(),
                    'club_id' => $club->id,
                    'user_id' => $u->id,
                    'role' => 'member',
                    'joined_at' => now()->subDays(rand(5, 60)),
                ]);
            }
        }
    }
}
