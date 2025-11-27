<?php

namespace Database\Seeders;

use App\Models\EquipmentUsage;
use Illuminate\Database\Seeder;

class EquipmentUsageSeeder extends Seeder
{
    public function run(): void
    {
        EquipmentUsage::factory()->count(80)->create();
        $this->command->info('Equipment usage seeded successfully!');
        $this->command->info('Total equipment usage records: ' . EquipmentUsage::count());
    }
}