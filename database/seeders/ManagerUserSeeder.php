<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ManagerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $managementRole = Role::where('name', 'management')->first();
        $team = Team::where('name', 'PH Futsal Academy')->first() ?: Team::first();

        if ($managementRole && $team) {
            $managersData = [
                ['name' => 'Achmad Syafiudin', 'position' => 'Manajer', 'number' => 90],
                ['name' => 'Dide Julio Arando', 'position' => 'Official', 'number' => 91],
                ['name' => 'Yuda Indra Saputra', 'position' => 'Official', 'number' => 92],
            ];

            foreach ($managersData as $m) {
                $email = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $m['name'])) . '@gmail.com';

                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $m['name'],
                        'password' => Hash::make('password'),
                        'role_id' => $managementRole->id,
                        'team_id' => $team->id,
                        'email_verified_at' => now(),
                    ]
                );

                Player::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'team_id' => $team->id,
                        'name' => $m['name'],
                        'number' => $m['number'],
                        'position' => $m['position'],
                    ]
                );
            }
        }

        // Seed the new school managers
        if ($managementRole) {
            $schoolManagers = [
                [
                    'team_name' => 'SMK N 1 Adiwerna',
                    'name' => 'Manager SMK N 1 Adiwerna',
                    'email' => 'manager@smkn1adiwerna.ac.id',
                ],
                [
                    'team_name' => 'SMA N 6 Kota Tegal',
                    'name' => 'Manager SMA N 6 Kota Tegal',
                    'email' => 'manager@sman6tegal.ac.id',
                ],
                [
                    'team_name' => 'SMP 3 Pangkah',
                    'name' => 'Manager SMP 3 Pangkah',
                    'email' => 'manager@smp3pangkah.ac.id',
                ],
            ];

            foreach ($schoolManagers as $sm) {
                $t = Team::where('name', $sm['team_name'])->first();
                if ($t) {
                    $user = User::updateOrCreate(
                        ['email' => $sm['email']],
                        [
                            'name' => $sm['name'],
                            'password' => Hash::make('password'),
                            'role_id' => $managementRole->id,
                            'team_id' => $t->id,
                            'email_verified_at' => now(),
                        ]
                    );

                    Player::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'team_id' => $t->id,
                            'name' => $sm['name'],
                            'number' => 1,
                            'position' => 'Manajer',
                        ]
                    );
                }
            }
        }
    }
}
