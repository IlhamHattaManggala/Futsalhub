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
    }
}
