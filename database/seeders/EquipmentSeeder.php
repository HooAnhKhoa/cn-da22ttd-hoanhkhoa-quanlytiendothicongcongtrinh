<?php

namespace Database\Seeders;

use App\Models\Equipment;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        Equipment::factory()->count(20)->create();
        $this->command->info('Equipment seeded successfully!');
        $this->command->info('Total equipment: ' . Equipment::count());
    }
}