<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Player;
use App\Models\Schedule;
use App\Models\Team;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch Teams
        $team1 = Team::where('name', 'FC Antigravity')->first();

        if ($team1) {
            // Schedules
            $schedule1 = Schedule::create([
                'team_id' => $team1->id,
                'title' => 'Latihan Rutin Fisik & Taktik',
                'type' => 'Latihan',
                'start_time' => now()->addDays(2)->setTime(19, 0),
                'location' => 'Futsal Champion Court A',
                'description' => 'Latihan persiapan tanding minggu depan. Wajib bawa jersey biru latihan.',
            ]);

            Schedule::create([
                'team_id' => $team1->id,
                'title' => 'Pertandingan Uji Coba vs Dev Team',
                'type' => 'Pertandingan',
                'start_time' => now()->addDays(5)->setTime(20, 0),
                'location' => 'Vidi Arena Futsal',
                'description' => 'Match persahabatan formal 2x20 menit bersih.',
                'opponent' => 'Dev Team FC',
            ]);

            // Attendances for schedule 1
            $team1Players = Player::where('team_id', $team1->id)->get();
            foreach ($team1Players as $p) {
                Attendance::create([
                    'schedule_id' => $schedule1->id,
                    'player_id' => $p->id,
                    'status' => $p->number == 11 ? 'Izin' : 'Hadir',
                    'notes' => $p->number == 11 ? 'Ada urusan keluarga' : 'Tepat waktu',
                ]);
            }
        }
    }
}
