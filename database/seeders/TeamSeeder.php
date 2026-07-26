<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Team::updateOrCreate(
            ['name' => 'PH Futsal Academy'],
            [
                'logo' => null,
                'description' => 'Akademi Futsal Utama PH dengan taktik modern.',
                'plan' => 'premium'
            ]
        );

        Team::updateOrCreate(
            ['name' => 'Galaxy Futsal'],
            [
                'logo' => null,
                'description' => 'Klub futsal penantang dengan rotasi taktik cepat.',
                'plan' => 'free'
            ]
        );

        Team::updateOrCreate(
            ['name' => 'SMK N 1 Adiwerna'],
            [
                'logo' => null,
                'description' => 'Tim Futsal SMK N 1 Adiwerna.',
                'plan' => 'premium'
            ]
        );

        Team::updateOrCreate(
            ['name' => 'SMA N 6 Kota Tegal'],
            [
                'logo' => null,
                'description' => 'Tim Futsal SMA N 6 Kota Tegal.',
                'plan' => 'premium'
            ]
        );

        Team::updateOrCreate(
            ['name' => 'SMP 3 Pangkah'],
            [
                'logo' => null,
                'description' => 'Tim Futsal SMP 3 Pangkah.',
                'plan' => 'premium'
            ]
        );
    }
}
