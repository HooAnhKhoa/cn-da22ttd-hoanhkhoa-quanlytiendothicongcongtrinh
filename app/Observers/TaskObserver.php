<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\Site;

class TaskObserver
{
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
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }

    protected function updateSiteProgress(Site $site)
    {
        $tasks = Task::where('site_id', $site->id)->get();
        
        if ($tasks->count() > 0) {
            $totalProgress = 0;
            foreach ($tasks as $task) {
                $totalProgress += $task->progress_percent ?? 0;
            }
            $overallProgress = round($totalProgress / $tasks->count(), 1);
            
            $site->update(['progress_percent' => $overallProgress]);
        }
    }
}
