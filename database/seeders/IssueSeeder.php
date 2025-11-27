<?php

namespace Database\Seeders;

use App\Models\Issue;
use Illuminate\Database\Seeder;

class IssueSeeder extends Seeder
{
    public function run(): void
    {
        Issue::factory()->count(45)->create();
        $this->command->info('Issues seeded successfully!');
        $this->command->info('Total issues: ' . Issue::count());
    }
}