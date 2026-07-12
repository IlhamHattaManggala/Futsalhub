<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch Teams
        $team1 = Team::where('name', 'FC Antigravity')->first();
        $team2 = Team::where('name', 'Galaxy Futsal')->first();

        // ==========================================
        // TEAM 1 PLAYERS
        // ==========================================
        $playersData1 = [
            ['name' => 'Rian Maulana', 'number' => 7, 'position' => 'Flank', 'email' => 'player1@futsal.com'],
            ['name' => 'Fikri Hidayat', 'number' => 4, 'position' => 'Anchor', 'email' => 'player2@futsal.com'],
            ['name' => 'Aris Budiman', 'number' => 9, 'position' => 'Pivot', 'email' => 'player3@futsal.com'],
            ['name' => 'Doni Siregar', 'number' => 1, 'position' => 'Goalkeeper', 'email' => 'player4@futsal.com'],
            ['name' => 'Taufik Hidayat', 'number' => 11, 'position' => 'Flank', 'email' => 'player5@futsal.com'],
        ];

        foreach ($playersData1 as $p) {
            $user = User::where('email', $p['email'])->first();

            Player::create([
                'team_id' => $team1->id,
                'user_id' => $user ? $user->id : null,
                'name' => $p['name'],
                'number' => $p['number'],
                'position' => $p['position'],
                'phone' => '081234567890',
                'birth_date' => '2002-04-12',
                'height' => 175,
                'weight' => 68,
            ]);
        }

        // Add 2 non-user players to Team 1
        Player::create([
            'team_id' => $team1->id,
            'user_id' => null,
            'name' => 'Reza Pahlevi',
            'number' => 14,
            'position' => 'Pivot',
            'phone' => '082345678901',
            'birth_date' => '2001-08-20',
            'height' => 180,
            'weight' => 74,
        ]);

        Player::create([
            'team_id' => $team1->id,
            'user_id' => null,
            'name' => 'Budi Prasetyo',
            'number' => 10,
            'position' => 'Flank',
            'phone' => '083456789012',
            'birth_date' => '2003-01-15',
            'height' => 170,
            'weight' => 62,
        ]);

        // ==========================================
        // TEAM 2 PLAYERS
        // ==========================================
        $playersData2 = [
            ['name' => 'Rendi Saputra', 'number' => 10, 'position' => 'Flank', 'email' => 'player2_1@futsal.com'],
            ['name' => 'Yoga Pratama', 'number' => 5, 'position' => 'Anchor', 'email' => 'player2_2@futsal.com'],
            ['name' => 'Galih Permana', 'number' => 8, 'position' => 'Pivot', 'email' => 'player2_3@futsal.com'],
            ['name' => 'Sandi Yudha', 'number' => 99, 'position' => 'Goalkeeper', 'email' => 'player2_4@futsal.com'],
            ['name' => 'Imron Rosadi', 'number' => 21, 'position' => 'Flank', 'email' => 'player2_5@futsal.com'],
        ];

        foreach ($playersData2 as $p) {
            $user = User::where('email', $p['email'])->first();

            Player::create([
                'team_id' => $team2->id,
                'user_id' => $user ? $user->id : null,
                'name' => $p['name'],
                'number' => $p['number'],
                'position' => $p['position'],
                'phone' => '089876543210',
                'birth_date' => '2004-11-05',
                'height' => 172,
                'weight' => 64,
            ]);
        }
    }
}
