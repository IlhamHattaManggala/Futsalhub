<?php

namespace Database\Seeders;

use App\Models\Tactic;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class TacticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch Team 1
        $team1 = Team::where('name', 'PH Futsal Academy')->first();

        // Fetch Coach
        $coach1 = User::where('email', 'nicoprayogo@gmail.com')->first() ?: User::whereHas('role', function($q) { $q->where('name', 'coach'); })->first();

        if ($team1 && $coach1) {
            Tactic::create([
                'team_id' => $team1->id,
                'coach_id' => $coach1->id,
                'title' => 'Strategi Penyerangan Diamond 1-2-1',
                'description' => 'Taktik menyerang menggunakan formasi berlian. Flank bergerak lebar untuk menciptakan ruang bagi pivot.',
                'formation' => '1-2-1',
                'canvas_data' => [
                    'players' => [
                        ['id' => 'p1', 'role' => 'our', 'number' => 1, 'name' => 'GK', 'x' => 400, 'y' => 540],
                        ['id' => 'p2', 'role' => 'our', 'number' => 4, 'name' => 'ANC', 'x' => 400, 'y' => 420],
                        ['id' => 'p3', 'role' => 'our', 'number' => 7, 'name' => 'FL1', 'x' => 250, 'y' => 320],
                        ['id' => 'p4', 'role' => 'our', 'number' => 11, 'name' => 'FL2', 'x' => 550, 'y' => 320],
                        ['id' => 'p5', 'role' => 'our', 'number' => 9, 'name' => 'PIV', 'x' => 400, 'y' => 180],
                    ],
                    'opponents' => [
                        ['id' => 'o1', 'role' => 'enemy', 'number' => 99, 'name' => 'GK', 'x' => 400, 'y' => 60],
                        ['id' => 'o2', 'role' => 'enemy', 'number' => 5, 'name' => 'DEF', 'x' => 400, 'y' => 130],
                        ['id' => 'o3', 'role' => 'enemy', 'number' => 10, 'name' => 'MID', 'x' => 330, 'y' => 240],
                        ['id' => 'o4', 'role' => 'enemy', 'number' => 8, 'name' => 'MID', 'x' => 470, 'y' => 240],
                        ['id' => 'o5', 'role' => 'enemy', 'number' => 7, 'name' => 'FWD', 'x' => 400, 'y' => 350],
                    ],
                    'ball' => ['x' => 400, 'y' => 380],
                    'drawings' => [
                        ['type' => 'arrow', 'from' => ['x' => 400, 'y' => 420], 'to' => ['x' => 250, 'y' => 320], 'color' => '#facc15'],
                        ['type' => 'arrow', 'from' => ['x' => 250, 'y' => 320], 'to' => ['x' => 400, 'y' => 180], 'color' => '#ffffff'],
                        ['type' => 'line', 'points' => [['x' => 550, 'y' => 320], ['x' => 400, 'y' => 180]], 'color' => '#ef4444']
                    ]
                ]
            ]);
        }
    }
}
