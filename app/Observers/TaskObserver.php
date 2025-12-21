<?php

namespace App\Observers;

use App\Models\Admin\Site;
use App\Models\Admin\Task;
use Carbon\Carbon;

class TaskObserver
{
    /**
     * Handle the Task "creating" event.
     * Chạy trước khi task được tạo
     */
    public function creating(Task $task): void
    {
        $this->updateTaskStatusBasedOnProgress($task);
    }

    /**
     * Handle the Task "updating" event.
     * Chạy trước khi task được cập nhật
     */
    public function updating(Task $task): void
    {
        $this->updateTaskStatusBasedOnProgress($task);
        
        // Nếu đã hoàn thành, cập nhật end_date nếu chưa có
        if ($task->progress_percent == 100 && !$task->end_date) {
            $task->end_date = Carbon::now();
        }
    }

    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        $this->updateSiteProgress($task->site);
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        $this->updateSiteProgress($task->site);
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        $this->updateSiteProgress($task->site);
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        $this->updateSiteProgress($task->site);
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        $this->updateSiteProgress($task->site);
    }

    /**
     * Cập nhật trạng thái task dựa trên tiến độ
     */
    protected function updateTaskStatusBasedOnProgress(Task $task)
    {
        // Chỉ xử lý khi progress_percent thay đổi
        if ($task->isDirty('progress_percent')) {
            $progress = (int) $task->progress_percent;
            
            if ($progress === 0) {
                $task->status = 'planned';
            } elseif ($progress > 0 && $progress < 100) {
                $task->status = 'in_progress';
            } elseif ($progress === 100) {
                $task->status = 'completed';
            }
        }
    }

    /**
     * Cập nhật tiến độ tổng của site
     */
    protected function updateSiteProgress(Site $site)
    {
        // Lấy tất cả task thuộc site
        $tasks = Task::where('site_id', $site->id)->get();

        if ($tasks->count() > 0) {
            $totalProgress = 0;

            foreach ($tasks as $task) {
                $totalProgress += $task->progress_percent ?? 0;
            }

            // Tiến độ trung bình của site
            $overallProgress = round($totalProgress / $tasks->count(), 1);

            // Cập nhật tiến độ site
            $site->progress_percent = $overallProgress;

            // Cập nhật trạng thái site theo tiến độ
            if ($overallProgress == 100) {
                $site->status = 'completed';
            } elseif ($overallProgress > 0) {
                $site->status = 'in_progress';
            } else {
                $site->status = 'planned';
            }

            $site->save();
        }
    }

}