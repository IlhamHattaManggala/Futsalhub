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
        Role::create([
            'name' => 'superadmin',
            'description' => 'Super Administrator Platform'
        ]);

        Role::create([
            'name' => 'management',
            'description' => 'Manajemen Tim / Pemilik Klub'
        ]);

        Role::create([
            'name' => 'coach',
            'description' => 'Pelatih / Staf Taktis'
        ]);

        Role::create([
            'name' => 'player',
            'description' => 'Pemain Tim Futsal'
        ]);
    }
}
