<?php

namespace Database\Seeders;

use App\Models\ProgressUpdate;
use Illuminate\Database\Seeder;

class ProgressUpdateSeeder extends Seeder
{
    public function run(): void
    {
        ProgressUpdate::factory()->count(200)->create();
        $this->command->info('Progress updates seeded successfully!');
        $this->command->info('Total progress updates: ' . ProgressUpdate::count());
    }
}