<?php

namespace Database\Seeders;

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
        $team1 = Team::where('name', 'FC Antigravity')->first();
        $team2 = Team::where('name', 'Galaxy Futsal')->first();

        if ($managementRole) {
            if ($team1) {
                User::create([
                    'name' => 'Hendra Manager',
                    'email' => 'manager1@futsal.com',
                    'password' => Hash::make('password'),
                    'role_id' => $managementRole->id,
                    'team_id' => $team1->id,
                ]);
            }

            if ($team2) {
                User::create([
                    'name' => 'Boni Manager',
                    'email' => 'manager2@futsal.com',
                    'password' => Hash::make('password'),
                    'role_id' => $managementRole->id,
                    'team_id' => $team2->id,
                ]);
            }
        }
    }
}
