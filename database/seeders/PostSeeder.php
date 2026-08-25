<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = DB::table('users')->pluck('id');
        $clubs = DB::table('clubs')->pluck('id');

        if ($users->isEmpty() || $clubs->isEmpty()) {
            $this->command->warn(
                'Seeder posts dibatalkan. Pastikan tabel users dan clubs sudah memiliki data.'
            );

            return;
        }

        $posts = [
            [
                'title' => 'Selamat Datang di Klub!',
                'content' => 'Halo semuanya! Selamat datang di klub kita. Jangan ragu untuk memperkenalkan diri dan ikut berdiskusi bersama anggota lainnya.',
                'media_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee',
                'is_announcement' => false,
            ],
            [
                'title' => 'Jadwal Kegiatan Minggu Ini',
                'content' => 'Kegiatan klub minggu ini akan dilaksanakan pada hari Sabtu pukul 15.00. Jangan lupa membawa perlengkapan masing-masing.',
                'media_url' => null,
                'is_announcement' => false,
            ],
            [
                'title' => 'Ada yang Mau Ikut Latihan Bareng?',
                'content' => 'Besok sore ada yang mau latihan bareng? Kita bisa mulai sekitar pukul 16.00. Kalau tertarik, langsung komentar di bawah.',
                'media_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee',
                'is_announcement' => false,
            ],
            [
                'title' => 'Hasil Kegiatan Kemarin',
                'content' => 'Terima kasih untuk semua anggota yang sudah ikut kegiatan kemarin. Seru banget dan semoga kegiatan berikutnya bisa lebih ramai lagi!',
                'media_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee',
                'is_announcement' => false,
            ],
            [
                'title' => 'Tips untuk Pemula',
                'content' => 'Buat yang baru mulai belajar, jangan terlalu fokus mengejar hasil. Yang paling penting adalah konsisten latihan dan menikmati prosesnya.',
                'media_url' => null,
                'is_announcement' => false,
            ],
            [
                'title' => 'Polling: Kegiatan Berikutnya',
                'content' => 'Menurut kalian kegiatan berikutnya lebih seru kalau diadakan outdoor atau indoor? Tulis pilihan kalian di komentar.',
                'media_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee',
                'is_announcement' => false,
            ],
            [
                'title' => 'Dokumentasi Kegiatan',
                'content' => 'Beberapa foto dari kegiatan terakhir sudah kami kumpulkan. Jangan lupa cek bagian Gallery untuk melihat dokumentasinya.',
                'media_url' => null,
                'is_announcement' => false,
            ],
            [
                'title' => 'Pengumuman Penting untuk Semua Anggota',
                'content' => 'Mohon semua anggota membaca aturan klub sebelum mengikuti kegiatan berikutnya. Kita ingin menjaga klub tetap nyaman dan aman untuk semua.',
                'media_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee',
                'is_announcement' => false,
            ],
            [
                'title' => 'Rekomendasi Perlengkapan',
                'content' => 'Untuk anggota yang baru bergabung, berikut beberapa perlengkapan dasar yang bisa kalian siapkan sebelum mengikuti kegiatan klub.',
                'media_url' => null,
                'is_announcement' => false,
            ],
            [
                'title' => 'Perkenalan Anggota Baru',
                'content' => 'Selamat datang untuk anggota baru! Yuk, kenalan dengan anggota lainnya dan ceritakan sedikit tentang pengalaman kalian.',
                'media_url' => null,
                'is_announcement' => false,
            ],
            [
                'title' => 'Diskusi Mingguan',
                'content' => 'Topik diskusi minggu ini adalah pengalaman pertama kalian ketika mulai menekuni hobi ini. Ceritakan pengalaman kalian di komentar.',
                'media_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee',
                'is_announcement' => false,
            ],
            [
                'title' => 'Kegiatan Bulanan Klub',
                'content' => 'Kegiatan bulanan akan segera dilaksanakan. Informasi mengenai waktu dan tempat akan diumumkan lebih lanjut.',
                'media_url' => null,
                'is_announcement' => false,
            ],
            [
                'title' => 'Siapa yang Sudah Mencoba?',
                'content' => 'Minggu ini kita mencoba sesuatu yang baru. Ada yang sudah mencoba teknik tersebut? Bagikan pengalaman kalian!',
                'media_url' => null,
                'is_announcement' => false,
            ],
            [
                'title' => 'Terima Kasih untuk Semua Anggota',
                'content' => 'Terima kasih sudah membuat komunitas ini menjadi tempat yang menyenangkan untuk belajar dan berbagi.',
                'media_url' => null,
                'is_announcement' => false,
            ],
            [
                'title' => 'Reminder Kegiatan Besok',
                'content' => 'Jangan lupa, kegiatan klub akan dilaksanakan besok. Pastikan kalian sudah mempersiapkan perlengkapan yang dibutuhkan.',
                'media_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee',
                'is_announcement' => false,
            ],
            [
                'title' => 'Share Hasil Karya Kalian',
                'content' => 'Punya hasil karya atau dokumentasi terbaru? Upload dan bagikan di sini supaya anggota lain bisa melihat dan memberikan feedback.',
                'media_url' => null,
                'is_announcement' => false,
            ],
            [
                'title' => 'Mencari Teman untuk Latihan',
                'content' => 'Saya sedang mencari teman untuk latihan bersama minggu ini. Kalau ada yang tertarik, boleh langsung komentar.',
                'media_url' => null,
                'is_announcement' => false,
            ],
            [
                'title' => 'Aturan Baru Klub',
                'content' => 'Mulai minggu depan akan ada beberapa penyesuaian aturan klub. Silakan baca pengumuman lengkap dari admin sebelum mengikuti kegiatan.',
                'media_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee',
                'is_announcement' => false,
            ],
            [
                'title' => 'Apa Target Kalian Bulan Ini?',
                'content' => 'Yuk, tuliskan target kalian bulan ini. Bisa berupa skill baru, project, atau pencapaian lain yang ingin kalian capai.',
                'media_url' => null,
                'is_announcement' => false,
            ],
            [
                'title' => 'Feedback untuk Kegiatan Terakhir',
                'content' => 'Kami ingin mendengar pendapat kalian. Apa yang sudah bagus dari kegiatan terakhir dan apa yang perlu diperbaiki untuk kegiatan berikutnya?',
                'media_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee',
                'is_announcement' => false,
            ],
        ];

        foreach ($posts as $post) {
            DB::table('posts')->insert([
                'id' => (string) Str::uuid(),
                'club_id' => $clubs->random(),
                'user_id' => $users->random(),
                'title' => $post['title'],
                'content' => $post['content'],
                'media_url' => $post['media_url'],
                'is_announcement' => $post['is_announcement'],
                'created_at' => now()->subDays(rand(0, 30)),
                'updated_at' => now(),
            ]);
        }
    }
}