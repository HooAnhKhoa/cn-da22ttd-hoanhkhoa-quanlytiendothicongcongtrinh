<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'total_projects' => Project::count(),
            'total_tasks' => Task::count(),
            'total_users' => User::count(),
            'active_projects' => Project::where('status', 'in_progress')->count(),
        ];

        $recentProjects = Project::latest()->take(5)->get();
        $recentTasks = Task::with('site.project')->latest()->take(10)->get();

        return view('home', compact('stats', 'recentProjects', 'recentTasks'));
    }

    public function dashboard()
    {
        return $this->index();
    }
}