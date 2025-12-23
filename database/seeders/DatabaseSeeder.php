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
            MilestoneSeeder::class,
            DelaySeeder::class,
            MaterialSeeder::class,
            MaterialUsageSeeder::class,
            EquipmentSeeder::class,
            EquipmentUsageSeeder::class,
            InspectionSeeder::class,
            IssueSeeder::class,
            DocumentSeeder::class,
            DrawingSeeder::class,
            ContractSeeder::class,
            PaymentSeeder::class,
            ContractApprovalSeeder::class,
            TaskReviewSeeder::class,    
        ]);
    }
}