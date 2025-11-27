<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        Payment::factory()->count(50)->create();
        $this->command->info('Payments seeded successfully!');
        $this->command->info('Total payments: ' . Payment::count());
    }
}