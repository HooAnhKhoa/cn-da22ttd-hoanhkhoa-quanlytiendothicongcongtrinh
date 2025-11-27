<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        Document::factory()->count(75)->create();
        $this->command->info('Documents seeded successfully!');
        $this->command->info('Total documents: ' . Document::count());
    }
}