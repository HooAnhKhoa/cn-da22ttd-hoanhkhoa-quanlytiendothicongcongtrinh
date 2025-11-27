<?php

namespace Database\Seeders;

use App\Models\Delay;
use Illuminate\Database\Seeder;

class DelaySeeder extends Seeder
{
    public function run(): void
    {
        Delay::factory()->count(40)->create();
        $this->command->info('Delays seeded successfully!');
        $this->command->info('Total delays: ' . Delay::count());
    }
}