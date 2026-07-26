<?php

namespace Database\Seeders;

use App\Models\Player;
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
        $team = Team::where('name', 'PH Futsal Academy')->first() ?: Team::first();

        if ($coachRole && $team) {
            $coachesData = [
                ['name' => 'Nico Prayogo', 'position' => 'Pelatih Kepala', 'number' => 93],
                ['name' => 'Putra Kowiyyuz Septiaztu', 'position' => 'Asisten Pelatih', 'number' => 94],
            ];

            foreach ($coachesData as $c) {
                $email = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $c['name'])) . '@gmail.com';

                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $c['name'],
                        'password' => Hash::make('password'),
                        'role_id' => $coachRole->id,
                        'team_id' => $team->id,
                        'email_verified_at' => now(),
                    ]
                );

                Player::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'team_id' => $team->id,
                        'name' => $c['name'],
                        'number' => $c['number'],
                        'position' => $c['position'],
                    ]
                );
            }
        }
    }
}
