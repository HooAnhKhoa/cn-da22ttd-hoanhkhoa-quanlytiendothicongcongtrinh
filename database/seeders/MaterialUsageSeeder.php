<?php

namespace Database\Seeders;

use App\Models\MaterialUsage;
use Illuminate\Database\Seeder;

class MaterialUsageSeeder extends Seeder
{
    public function run(): void
    {
        MaterialUsage::factory()->count(150)->create();
        $this->command->info('Material usage seeded successfully!');
        $this->command->info('Total material usage records: ' . MaterialUsage::count());
    }
}