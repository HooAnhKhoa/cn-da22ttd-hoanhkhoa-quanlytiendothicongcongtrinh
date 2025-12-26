<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProjectSeeder::class,
            SiteSeeder::class,
            TaskSeeder::class,
            ProgressUpdateSeeder::class,
            MaterialSeeder::class,
            MaterialUsageSeeder::class,
            DocumentSeeder::class,
            ContractSeeder::class,
            PaymentSeeder::class,
            ContractApprovalSeeder::class,
            TaskReviewSeeder::class,    
        ]);
    }
}