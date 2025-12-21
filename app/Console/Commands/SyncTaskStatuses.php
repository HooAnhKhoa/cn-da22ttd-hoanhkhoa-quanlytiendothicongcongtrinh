<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin\Task;

class SyncTaskStatuses extends Command
{
    protected $signature = 'tasks:sync-status';
    protected $description = 'Đồng bộ trạng thái của tất cả tasks dựa trên tiến độ';

    public function handle()
    {
        $tasks = Task::all();
        $updatedCount = 0;
        
        foreach ($tasks as $task) {
            $progress = (int) $task->progress_percent;
            $newStatus = '';
            
            if ($progress === 0) {
                $newStatus = 'planned';
            } elseif ($progress > 0 && $progress < 100) {
                $newStatus = 'in_progress';
            } elseif ($progress === 100) {
                $newStatus = 'completed';
            }
            
            if ($task->status !== $newStatus) {
                $task->status = $newStatus;
                $task->save();
                $updatedCount++;
            }
        }
        
        $this->info("Đã cập nhật {$updatedCount} tasks.");
        
        return Command::SUCCESS;
    }
}