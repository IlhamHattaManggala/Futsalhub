<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CoachUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coachRole = Role::where('name', 'coach')->first();
        $team1 = Team::where('name', 'FC Antigravity')->first();
        $team2 = Team::where('name', 'Galaxy Futsal')->first();

        if ($coachRole) {
            if ($team1) {
                User::create([
                    'name' => 'Coach Ilham',
                    'email' => 'coach1@futsal.com',
                    'password' => Hash::make('password'),
                    'role_id' => $coachRole->id,
                    'team_id' => $team1->id,
                ]);
            }

            if ($team2) {
                User::create([
                    'name' => 'Coach Andi',
                    'email' => 'coach2@futsal.com',
                    'password' => Hash::make('password'),
                    'role_id' => $coachRole->id,
                    'team_id' => $team2->id,
                ]);
            }
        }
    }
}
