<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        Task::factory()->count(100)->create();
        $this->command->info('Tasks seeded successfully!');
        $this->command->info('Total tasks: ' . Task::count());
    }
}