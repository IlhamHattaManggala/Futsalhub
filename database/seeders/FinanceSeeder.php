<?php

namespace Database\Seeders;

use App\Models\Finance;
use App\Models\Team;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch Teams
        $team1 = Team::where('name', 'FC Antigravity')->first();
        $team2 = Team::where('name', 'Galaxy Futsal')->first();

        // ==========================================
        // TEAM 1 FINANCES
        // ==========================================
        if ($team1) {
            Finance::create([
                'team_id' => $team1->id,
                'type' => 'Pemasukan',
                'amount' => 500000.00,
                'date' => now()->subDays(5),
                'description' => 'Iuran bulanan kas tim dari patungan pemain',
                'category' => 'Iuran Pemain',
            ]);

            Finance::create([
                'team_id' => $team1->id,
                'type' => 'Pengeluaran',
                'amount' => 250000.00,
                'date' => now()->subDays(4),
                'description' => 'Sewa Lapangan Futsal Champion 2 Jam',
                'category' => 'Sewa Lapangan',
            ]);

            Finance::create([
                'team_id' => $team1->id,
                'type' => 'Pemasukan',
                'amount' => 1000000.00,
                'date' => now()->subDays(2),
                'description' => 'Dana Sponsor Apparel dari Distro Lokal',
                'category' => 'Sponsor',
            ]);
        }

        // ==========================================
        // TEAM 2 FINANCES
        // ==========================================
        if ($team2) {
            Finance::create([
                'team_id' => $team2->id,
                'type' => 'Pemasukan',
                'amount' => 400000.00,
                'date' => now()->subDays(6),
                'description' => 'Uang Kas Awal Patungan Pengurus',
                'category' => 'Iuran Pemain',
            ]);

            Finance::create([
                'team_id' => $team2->id,
                'type' => 'Pengeluaran',
                'amount' => 150000.00,
                'date' => now()->subDays(3),
                'description' => 'Pembelian Bola Futsal Mitre 1 Pcs',
                'category' => 'Peralatan',
            ]);
        }
    }
}
