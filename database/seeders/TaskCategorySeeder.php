<?php

namespace Database\Seeders;

use App\Models\TaskCategory;
use Illuminate\Database\Seeder;

class TaskCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TaskCategory::create(['name' => 'Fisik']);
        TaskCategory::create(['name' => 'Teknik']);
        TaskCategory::create(['name' => 'Taktik']);
        TaskCategory::create(['name' => 'Gaya Hidup']);
        TaskCategory::create(['name' => 'Analisis']);
    }
}
