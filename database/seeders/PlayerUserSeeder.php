<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PlayerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $playerRole = Role::where('name', 'player')->first();
        $team1 = Team::where('name', 'FC Antigravity')->first();
        $team2 = Team::where('name', 'Galaxy Futsal')->first();

        if ($playerRole) {
            // ==========================================
            // TEAM 1 PLAYER USERS
            // ==========================================
            if ($team1) {
                $playersData1 = [
                    ['name' => 'Rian Maulana', 'email' => 'player1@futsal.com'],
                    ['name' => 'Fikri Hidayat', 'email' => 'player2@futsal.com'],
                    ['name' => 'Aris Budiman', 'email' => 'player3@futsal.com'],
                    ['name' => 'Doni Siregar', 'email' => 'player4@futsal.com'],
                    ['name' => 'Taufik Hidayat', 'email' => 'player5@futsal.com'],
                ];

                foreach ($playersData1 as $p) {
                    User::create([
                        'name' => $p['name'],
                        'email' => $p['email'],
                        'password' => Hash::make('password'),
                        'role_id' => $playerRole->id,
                        'team_id' => $team1->id,
                    ]);
                }
            }

            // ==========================================
            // TEAM 2 PLAYER USERS
            // ==========================================
            if ($team2) {
                $playersData2 = [
                    ['name' => 'Rendi Saputra', 'email' => 'player2_1@futsal.com'],
                    ['name' => 'Yoga Pratama', 'email' => 'player2_2@futsal.com'],
                    ['name' => 'Galih Permana', 'email' => 'player2_3@futsal.com'],
                    ['name' => 'Sandi Yudha', 'email' => 'player2_4@futsal.com'],
                    ['name' => 'Imron Rosadi', 'email' => 'player2_5@futsal.com'],
                ];

                foreach ($playersData2 as $p) {
                    User::create([
                        'name' => $p['name'],
                        'email' => $p['email'],
                        'password' => Hash::make('password'),
                        'role_id' => $playerRole->id,
                        'team_id' => $team2->id,
                    ]);
                }
            }
        }
    }
}
