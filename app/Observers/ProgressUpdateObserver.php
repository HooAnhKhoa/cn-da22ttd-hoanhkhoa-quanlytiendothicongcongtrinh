<?php

namespace App\Observers;

use App\Models\ProgressUpdate;
use App\Models\Admin\Task;
use Carbon\Carbon;

class ProgressUpdateObserver
{
    /**
     * Handle the ProgressUpdate "created" event.
     */
    public function created(ProgressUpdate $progressUpdate): void
    {
        // Cập nhật tiến độ task khi có báo cáo mới
        $task = $progressUpdate->task;
        if ($task) {
            $task->progress_percent = $progressUpdate->progress_percent;
            
            // Tự động cập nhật trạng thái task dựa trên tiến độ
            $progress = (int) $progressUpdate->progress_percent;
            
            if ($progress === 0) {
                $task->status = 'planned';
            } elseif ($progress > 0 && $progress < 100) {
                $task->status = 'in_progress';
            } elseif ($progress === 100) {
                $task->status = 'completed';
                
                // Nếu đã hoàn thành, cập nhật end_date nếu chưa có
                if (!$task->end_date) {
                    $task->end_date = Carbon::now();
                }
            }
            
            $task->save();
        }
    }

    /**
     * Handle the ProgressUpdate "updated" event.
     */
    public function updated(ProgressUpdate $progressUpdate): void
    {
        // Cập nhật tiến độ task khi báo cáo được chỉnh sửa
        $task = $progressUpdate->task;
        if ($task && $progressUpdate->isDirty('progress_percent')) {
            $task->progress_percent = $progressUpdate->progress_percent;
            
            // Tự động cập nhật trạng thái task dựa trên tiến độ
            $progress = (int) $progressUpdate->progress_percent;
            
            if ($progress === 0) {
                $task->status = 'planned';
            } elseif ($progress > 0 && $progress < 100) {
                $task->status = 'in_progress';
            } elseif ($progress === 100) {
                $task->status = 'completed';
                
                // Nếu đã hoàn thành, cập nhật end_date nếu chưa có
                if (!$task->end_date) {
                    $task->end_date = Carbon::now();
                }
            }
            
            $task->save();
        }
    }

    /**
     * Handle the ProgressUpdate "deleted" event.
     */
    public function deleted(ProgressUpdate $progressUpdate): void
    {
        // Cập nhật task về tiến độ của báo cáo cuối cùng
        $task = $progressUpdate->task;
        if ($task) {
            $latestReport = ProgressUpdate::where('task_id', $task->id)
                ->latest('date')
                ->first();
            
            if ($latestReport) {
                $task->progress_percent = $latestReport->progress_percent;
            } else {
                $task->progress_percent = 0;
            }
            
            // Cập nhật trạng thái dựa trên tiến độ mới
            $progress = (int) $task->progress_percent;
            
            if ($progress === 0) {
                $task->status = 'planned';
            } elseif ($progress > 0 && $progress < 100) {
                $task->status = 'in_progress';
            } elseif ($progress === 100) {
                $task->status = 'completed';
            }
            
            $task->save();
        }
    }
}