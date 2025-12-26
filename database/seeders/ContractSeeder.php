<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();
        
        if ($projects->isEmpty()) {
            $this->command->info('Không có project nào để tạo hợp đồng.');
            return;
        }

        foreach ($projects as $project) {
            // Mỗi project có 1-2 hợp đồng
            $count = rand(1, 2);
            
            for ($i = 0; $i < $count; $i++) {
                Contract::factory()->create([
                    'project_id' => $project->id,
                    // Đã xóa owner_id, contractor_id vì chúng nằm trong Project
                    'status' => $this->getContractStatusForProject($project),
                ]);
            }
        }

        $this->command->info('Đã tạo ' . Contract::count() . ' hợp đồng.');
    }

    private function getContractStatusForProject($project): string
    {
        return match($project->status) {
            'pending_contract' => 'pending_signature',
            'in_progress' => 'active',
            'completed' => 'completed',
            'cancelled' => 'terminated',
            default => fake()->randomElement(['draft', 'pending_signature']),
        };
    }
}