<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Task;
use App\Models\ProgressUpdate;
use App\Observers\TaskObserver;
use App\Observers\ProgressUpdateObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Task::observe(TaskObserver::class);
        ProgressUpdate::observe(ProgressUpdateObserver::class);
    }
}