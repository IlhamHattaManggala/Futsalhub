<?php

namespace Database\Seeders;

use App\Models\MatchGame;
use App\Models\Player;
use App\Models\Statistic;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class MatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch Team 1
        $team1 = Team::where('name', 'PH Futsal Academy')->first();

        if ($team1) {
            // Create finished match
            $match1 = MatchGame::create([
                'team_id' => $team1->id,
                'opponent' => 'Rajawali FC',
                'date' => now()->subDays(3),
                'location' => 'Champion Futsal Court B',
                'status' => 'Selesai',
                'score_team' => 4,
                'score_opponent' => 2,
                'notes' => 'Menang berkat taktik pivot diamond yang efektif di babak kedua.',
            ]);

            // Fetch players by email from their users relationship
            $player1 = Player::whereHas('user', function ($q) {
                $q->where('email', 'bintangfajarsatriamuda@gmail.com');
            })->first() ?: Player::skip(0)->first();

            $player2 = Player::whereHas('user', function ($q) {
                $q->where('email', 'muhammadlabibarkaan@gmail.com');
            })->first() ?: Player::skip(1)->first();

            $player3 = Player::whereHas('user', function ($q) {
                $q->where('email', 'mikyalkautsaraprilio@gmail.com');
            })->first() ?: Player::skip(2)->first();

            $player4 = Player::whereHas('user', function ($q) {
                $q->where('email', 'irfanqoshidulhaq@gmail.com');
            })->first() ?: Player::skip(3)->first();

            // Stats for Match 1
            if ($player1) {
                Statistic::create([
                    'match_id' => $match1->id,
                    'player_id' => $player1->id,
                    'goals' => 2,
                    'assists' => 1,
                    'yellow_cards' => 0,
                    'red_cards' => 0,
                    'minutes_played' => 35,
                ]);
            }

            if ($player3) {
                Statistic::create([
                    'match_id' => $match1->id,
                    'player_id' => $player3->id,
                    'goals' => 1,
                    'assists' => 2,
                    'yellow_cards' => 0,
                    'red_cards' => 0,
                    'minutes_played' => 30,
                ]);
            }

            if ($player2) {
                Statistic::create([
                    'match_id' => $match1->id,
                    'player_id' => $player2->id,
                    'goals' => 1,
                    'assists' => 0,
                    'yellow_cards' => 1,
                    'red_cards' => 0,
                    'minutes_played' => 40,
                ]);
            }

            if ($player4) {
                Statistic::create([
                    'match_id' => $match1->id,
                    'player_id' => $player4->id,
                    'goals' => 0,
                    'assists' => 0,
                    'yellow_cards' => 0,
                    'red_cards' => 0,
                    'minutes_played' => 40,
                ]);
            }
        }
    }
}
