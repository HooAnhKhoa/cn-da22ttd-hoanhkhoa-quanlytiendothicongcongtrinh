<?php

namespace Database\Seeders;

use App\Models\Drawing;
use Illuminate\Database\Seeder;

class DrawingSeeder extends Seeder
{
    public function run(): void
    {
        Drawing::factory()->count(40)->create();
        $this->command->info('Drawings seeded successfully!');
        $this->command->info('Total drawings: ' . Drawing::count());
    }
}