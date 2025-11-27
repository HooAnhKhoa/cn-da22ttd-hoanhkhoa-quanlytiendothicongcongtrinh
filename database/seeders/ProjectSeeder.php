<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        Project::factory()->count(15)->create();
        $this->command->info('Projects seeded successfully!');
        $this->command->info('Total projects: ' . Project::count());
    }
}