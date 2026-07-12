<?php

namespace Database\Seeders;

use App\Models\PremiumPayment;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class PremiumPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch Teams
        $team1 = Team::where('name', 'FC Antigravity')->first();
        $team2 = Team::where('name', 'Galaxy Futsal')->first();

        // Fetch Managers
        $manager1 = User::where('email', 'manager1@futsal.com')->first();
        $manager2 = User::where('email', 'manager2@futsal.com')->first();

        // Transaction 1: Success / Paid (Team 1)
        if ($team1 && $manager1) {
            PremiumPayment::create([
                'team_id' => $team1->id,
                'user_id' => $manager1->id,
                'amount' => 100000.00,
                'status' => 'approved',
                'payment_status' => 'paid',
                'reference' => 'T1234567890',
                'merchant_ref' => 'TRX-123456789',
                'payment_method' => 'QRIS2',
                'pay_code' => 'https://sandbox.tripay.co.id/qr/T1234567890',
                'qr_url' => 'https://sandbox.tripay.co.id/qr/T1234567890',
                'payment_url' => 'https://tripay.co.id/checkout/T1234567890',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ]);
        }

        // Transaction 2: Pending (Team 2)
        if ($team2 && $manager2) {
            PremiumPayment::create([
                'team_id' => $team2->id,
                'user_id' => $manager2->id,
                'amount' => 100000.00,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'reference' => 'T1234567891',
                'merchant_ref' => 'TRX-123456790',
                'payment_method' => 'BCAVA',
                'pay_code' => '98765432101234',
                'payment_url' => 'https://tripay.co.id/checkout/T1234567891',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ]);
        }
    }
}
