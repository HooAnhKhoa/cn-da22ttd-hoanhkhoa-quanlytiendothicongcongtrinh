<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    public function run(): void
    {
        Site::factory()->count(30)->create();
        $this->command->info('Sites seeded successfully!');
        $this->command->info('Total sites: ' . Site::count());
    }
}