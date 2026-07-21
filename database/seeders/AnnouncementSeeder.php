<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch Teams
        $team1 = Team::where('name', 'PH Futsal Academy')->first();
        $team2 = Team::where('name', 'Galaxy Futsal')->first();

        // Fetch Managers
        $manager1 = User::where('email', 'achmadsyafiudin@gmail.com')->first() ?: User::whereHas('role', function($q) { $q->where('name', 'management'); })->first();
        $manager2 = User::where('email', 'didejulioarando@gmail.com')->first() ?: User::whereHas('role', function($q) { $q->where('name', 'management'); })->first();

        // ==========================================
        // TEAM 1 ANNOUNCEMENT
        // ==========================================
        if ($team1 && $manager1) {
            Announcement::create([
                'team_id' => $team1->id,
                'user_id' => $manager1->id,
                'title' => 'Pengumuman Iuran Kas Bulan Mei',
                'content' => 'Halo rekan-rekan PH Futsal Academy, diharapkan untuk segera melunasi iuran bulanan kas tim sebesar Rp 50.000 paling lambat akhir minggu ini melalui Bendahara/Manager. Uang akan digunakan untuk pendaftaran turnamen lokal bulan depan. Terima kasih!',
            ]);
        }

        // ==========================================
        // TEAM 2 ANNOUNCEMENT
        // ==========================================
        if ($team2 && $manager2) {
            Announcement::create([
                'team_id' => $team2->id,
                'user_id' => $manager2->id,
                'title' => 'Jadwal Friendly Match Akhir Pekan',
                'content' => 'Teman-teman Galaxy Futsal, hari Sabtu nanti kita ada friendly match. Mohon konfirmasi kehadirannya di grup WA agar Coach Andi bisa merancang skema rotasi tim dengan matang. Tetap semangat!',
            ]);
        }
    }
}
