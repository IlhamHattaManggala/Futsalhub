<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadminRole = Role::where('name', 'superadmin')->first();

        if ($superadminRole) {
            User::updateOrCreate(
                ['email' => 'superadmin@futsal.com'],
                [
                    'name' => 'Superadmin Global',
                    'password' => Hash::make('password'),
                    'role_id' => $superadminRole->id,
                    'team_id' => null,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
