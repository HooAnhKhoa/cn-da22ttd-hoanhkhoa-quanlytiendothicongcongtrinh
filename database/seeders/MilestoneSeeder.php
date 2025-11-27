<?php

namespace Database\Seeders;

use App\Models\Milestone;
use Illuminate\Database\Seeder;

class MilestoneSeeder extends Seeder
{
    public function run(): void
    {
        Milestone::factory()->count(50)->create();
        $this->command->info('Milestones seeded successfully!');
        $this->command->info('Total milestones: ' . Milestone::count());
    }
}