<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $sites = Site::all();
        $engineers = User::where('user_type', 'engineer')->get();
        
        if ($sites->isEmpty() || $engineers->isEmpty()) {
            $this->command->info('Không có site hoặc engineer nào để tạo công việc.');
            return;
        }

        foreach ($sites as $site) {
            // Mỗi site có 3-8 công việc
            $taskCount = rand(3, 8);
            
            for ($i = 0; $i < $taskCount; $i++) {
                $status = $this->getTaskStatusForSite($site);
                
                // Sử dụng Factory state tương ứng
                $taskFactory = Task::factory()->withValidDates();
                
                switch ($status) {
                    case 'planned':
                        $taskFactory = $taskFactory->planned();
                        break;
                    case 'in_progress':
                        $taskFactory = $taskFactory->inProgress();
                        break;
                    case 'pending_review':
                        $taskFactory = $taskFactory->pendingReview();
                        break;
                    case 'completed':
                        $taskFactory = $taskFactory->completed();
                        break;
                }
                
                $taskFactory->create([
                    'site_id' => $site->id,
                    'assigned_engineer_id' => $engineers->random()->id,
                ]);
            }
            
            // Cập nhật total_budget cho site
            $site->total_budget = $site->tasks()->sum('task_budget');
            $site->save();
        }

        // Tạo một số task có parent (subtasks)
        $mainTasks = Task::limit(10)->get();
        foreach ($mainTasks as $mainTask) {
            $subtaskCount = rand(1, 3);
            for ($i = 0; $i < $subtaskCount; $i++) {
                Task::factory()
                    ->withParent()
                    ->create([
                        'assigned_engineer_id' => $engineers->random()->id,
                    ]);
            }
        }

        $this->command->info('Đã tạo ' . Task::count() . ' công việc.');
    }

    private function getTaskStatusForSite($site): string
    {
        return match($site->status) {
            'in_progress' => $this->getRandomStatus(['planned', 'in_progress', 'pending_review', 'completed']),
            'completed' => 'completed',
            'cancelled' => 'cancelled',
            default => 'planned',
        };
    }
    
    private function getRandomStatus(array $statuses): string
    {
        $weights = [
            'planned' => 2,
            'in_progress' => 4,
            'pending_review' => 2,
            'completed' => 1
        ];
        
        $weightedStatuses = [];
        foreach ($statuses as $status) {
            $weight = $weights[$status] ?? 1;
            for ($i = 0; $i < $weight; $i++) {
                $weightedStatuses[] = $status;
            }
        }
        
        return $weightedStatuses[array_rand($weightedStatuses)];
    }
}