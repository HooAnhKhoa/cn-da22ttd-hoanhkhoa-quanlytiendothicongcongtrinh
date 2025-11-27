<?php

namespace Database\Seeders;

use App\Models\Contract;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        Contract::factory()->count(20)->create();
        $this->command->info('Contracts seeded successfully!');
        $this->command->info('Total contracts: ' . Contract::count());
    }
}