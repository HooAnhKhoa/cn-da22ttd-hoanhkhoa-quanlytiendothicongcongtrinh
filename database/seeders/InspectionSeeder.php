<?php

namespace Database\Seeders;

use App\Models\Inspection;
use Illuminate\Database\Seeder;

class InspectionSeeder extends Seeder
{
    public function run(): void
    {
        Inspection::factory()->count(60)->create();
        $this->command->info('Inspections seeded successfully!');
        $this->command->info('Total inspections: ' . Inspection::count());
    }
}