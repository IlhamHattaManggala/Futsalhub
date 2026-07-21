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
        $team = Team::where('name', 'PH Futsal Academy')->first() ?: Team::first();

        if ($playerRole && $team) {
            $players = [
                'Bintang Fajar Satria Muda',
                'Muhammad Labib Arkaan',
                'Mikyal Kautsar Aprilio',
                'Irfan Qoshidul Haq',
                'Ibnu Arsa Mutawa',
                'M. Nezfan Maulana Nady',
                'Revli Yandi Arslan',
                'Revano Farras Kurniawan',
                'Muhammad Rafa Maulana',
                'Bangkit Indra Kusuma',
                'Muhammad Tanjung Mahira',
                'Muhammad Syafiq',
                'Caesar Shofa Arhan Maulana',
                'Ivander Wahyu Pratama',
                'Haidar Rahman',
                'Muhammad Daffa Ardeansah',
                'Afgan Dwi Pangestu',
                'Candra Abrar Nugraha',
                'Faza Narendra Kuswanto',
                'Nayzar Daffa Maulana',
            ];

            foreach ($players as $name) {
                $email = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name)) . '@gmail.com';

                User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'password' => Hash::make('password'),
                        'role_id' => $playerRole->id,
                        'team_id' => $team->id,
                    ]
                );
            }
        }
    }
}
