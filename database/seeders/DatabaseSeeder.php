<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            TaskCategorySeeder::class,
            TeamSeeder::class,
            SuperadminUserSeeder::class,
            ManagerUserSeeder::class,
            CoachUserSeeder::class,
            PlayerUserSeeder::class,
            PlayerSeeder::class,
            ScheduleSeeder::class,
            FinanceSeeder::class,
            AnnouncementSeeder::class,
            MatchSeeder::class,
            TacticSeeder::class,
            PremiumPaymentSeeder::class,
        ]);
    }
}
