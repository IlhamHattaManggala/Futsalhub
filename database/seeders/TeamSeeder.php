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
        Team::create([
            'name' => 'FC Antigravity',
            'logo' => null,
            'description' => 'Tim Futsal Utama Antigravity dengan taktik modern.',
            'plan' => 'premium'
        ]);

        Team::create([
            'name' => 'Galaxy Futsal',
            'logo' => null,
            'description' => 'Klub futsal penantang dengan rotasi taktik cepat.',
            'plan' => 'free'
        ]);
    }
}
