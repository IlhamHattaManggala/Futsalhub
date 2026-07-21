<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::updateOrCreate(
            ['name' => 'superadmin'],
            ['description' => 'Super Administrator Platform']
        );

        Role::updateOrCreate(
            ['name' => 'management'],
            ['description' => 'Manajemen Tim / Pemilik Klub']
        );

        Role::updateOrCreate(
            ['name' => 'coach'],
            ['description' => 'Pelatih / Staf Taktis']
        );

        Role::updateOrCreate(
            ['name' => 'player'],
            ['description' => 'Pemain Tim Futsal']
        );
    }
}
