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
        $team = Team::where('name', 'PH Futsal Academy')->first() ?: Team::first();

        if ($team) {
            $playersData = [
                ['name' => 'Bintang Fajar Satria Muda', 'position' => 'Keeper', 'number' => 1],
                ['name' => 'Muhammad Labib Arkaan', 'position' => 'Keeper', 'number' => 2],
                ['name' => 'Mikyal Kautsar Aprilio', 'position' => 'Pivot', 'number' => 3],
                ['name' => 'Irfan Qoshidul Haq', 'position' => 'Anchor', 'number' => 4],
                ['name' => 'Ibnu Arsa Mutawa', 'position' => 'Flank', 'number' => 5],
                ['name' => 'M. Nezfan Maulana Nady', 'position' => 'Anchor', 'number' => 6],
                ['name' => 'Revli Yandi Arslan', 'position' => 'Flank', 'number' => 7],
                ['name' => 'Revano Farras Kurniawan', 'position' => 'Pivot', 'number' => 8],
                ['name' => 'Muhammad Rafa Maulana', 'position' => 'Flank', 'number' => 9],
                ['name' => 'Bangkit Indra Kusuma', 'position' => 'Flank', 'number' => 10],
                ['name' => 'Muhammad Tanjung Mahira', 'position' => 'Flank', 'number' => 11],
                ['name' => 'Muhammad Syafiq', 'position' => 'Anchor', 'number' => 12],
                ['name' => 'Caesar Shofa Arhan Maulana', 'position' => 'Anchor', 'number' => 13],
                ['name' => 'Ivander Wahyu Pratama', 'position' => 'Flank', 'number' => 14],
                ['name' => 'Haidar Rahman', 'position' => 'Anchor', 'number' => 15],
                ['name' => 'Muhammad Daffa Ardeansah', 'position' => 'Flank', 'number' => 16],
                ['name' => 'Afgan Dwi Pangestu', 'position' => 'Flank', 'number' => 17],
                ['name' => 'Candra Abrar Nugraha', 'position' => 'Flank', 'number' => 18],
                ['name' => 'Faza Narendra Kuswanto', 'position' => 'Flank', 'number' => 19],
                ['name' => 'Nayzar Daffa Maulana', 'position' => 'Keeper', 'number' => 20],
            ];

            foreach ($playersData as $p) {
                $email = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $p['name'])) . '@gmail.com';
                $user = User::where('email', $email)->first();

                Player::updateOrCreate(
                    ['team_id' => $team->id, 'name' => $p['name']],
                    [
                        'user_id' => $user ? $user->id : null,
                        'number' => $p['number'],
                        'position' => $p['position'],
                        'phone' => '081234567890',
                        'birth_date' => '2002-04-12',
                        'height' => 175,
                        'weight' => 68,
                    ]
                );
            }
        }
    }
}
