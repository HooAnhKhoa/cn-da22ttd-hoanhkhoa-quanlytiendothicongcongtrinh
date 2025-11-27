<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        Material::factory()->count(25)->create();
        $this->command->info('Materials seeded successfully!');
        $this->command->info('Total materials: ' . Material::count());
    }
}