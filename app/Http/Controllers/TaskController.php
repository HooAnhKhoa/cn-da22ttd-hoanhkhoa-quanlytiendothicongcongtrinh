<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Site;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['site.project'])->latest()->get();
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $sites = Site::with('project')->get();
        $parentTasks = Task::whereNull('parent_id')->get();
        
        return view('tasks.create', compact('sites', 'parentTasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'parent_id' => 'nullable|exists:tasks,id',
            'task_name' => 'required',
            'description' => 'nullable',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'planned_duration' => 'nullable|integer|min:0',
            'progress_percent' => 'required|integer|min:0|max:100',
            'status' => 'required|in:planned,in_progress,completed,on_hold,cancelled'
        ]);

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Công việc đã được tạo thành công!');
    }

    public function show(Task $task)
    {
        $task->load(['site.project', 'parent', 'children', 'progressUpdates', 'delays', 'materialUsages.material']);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $sites = Site::with('project')->get();
        $parentTasks = Task::whereNull('parent_id')->where('id', '!=', $task->id)->get();
        
        return view('tasks.edit', compact('task', 'sites', 'parentTasks'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'parent_id' => 'nullable|exists:tasks,id',
            'task_name' => 'required',
            'description' => 'nullable',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'planned_duration' => 'nullable|integer|min:0',
            'actual_duration' => 'nullable|integer|min:0',
            'progress_percent' => 'required|integer|min:0|max:100',
            'status' => 'required|in:planned,in_progress,completed,on_hold,cancelled'
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Công việc đã được cập nhật!');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Công việc đã được xóa!');
    }
}