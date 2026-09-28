<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $richard = User::where('email', 'richard@example.com')->first();
        $jhon = User::where('email', 'jhon@example.com')->first();
        $rangga = User::where('email', 'rangga@example.com')->first();
        $sinta = User::where('email', 'sinta@example.com')->first();

        // Seed messages for both Richard and Jhon so whichever the user logs in as, it matches Figma!
        $targetUsers = array_filter([$richard, $jhon]);

        foreach ($targetUsers as $targetUser) {
            if ($rangga) {
                Message::create([
                    'id' => (string) Str::uuid(),
                    'sender_id' => $rangga->id,
                    'receiver_id' => $targetUser->id,
                    'content' => 'Mau ikut photoshoot?',
                    'is_read' => true,
                    'send_at' => Carbon::now()->subDays(2),
                ]);

                Message::create([
                    'id' => (string) Str::uuid(),
                    'sender_id' => $targetUser->id,
                    'receiver_id' => $rangga->id,
                    'content' => 'Posisi dimana?',
                    'is_read' => true,
                    'send_at' => Carbon::now()->subDays(2)->addMinutes(15),
                ]);

                Message::create([
                    'id' => (string) Str::uuid(),
                    'sender_id' => $rangga->id,
                    'receiver_id' => $targetUser->id,
                    'content' => 'Di Kota Lama, kumpul jam 9 pagi ya.',
                    'is_read' => true,
                    'send_at' => Carbon::now()->subDays(1),
                ]);
            }

            if ($sinta) {
                Message::create([
                    'id' => (string) Str::uuid(),
                    'sender_id' => $targetUser->id,
                    'receiver_id' => $sinta->id,
                    'content' => 'Bawa tripod ga nanti?',
                    'is_read' => true,
                    'send_at' => Carbon::now()->subDays(2)->subHours(3),
                ]);

                Message::create([
                    'id' => (string) Str::uuid(),
                    'sender_id' => $sinta->id,
                    'receiver_id' => $targetUser->id,
                    'content' => 'wkwkwk',
                    'is_read' => false,
                    'send_at' => Carbon::now()->subDays(2),
                ]);
            }
        }
    }
}
