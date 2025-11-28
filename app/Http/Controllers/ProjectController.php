<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{   
    public function index(Request $request)
    {
        $projects = Project::with(['owner', 'contractor', 'engineer'])
            ->latest()
            ->paginate(12);

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        // Lấy chủ đầu tư (owner)
        $owners = User::where('user_type', 'owner')
                    ->orderBy('username')
                    ->get();

        // Lấy nhà thầu (contractor)
        $contractors = User::where('user_type', 'contractor')
                          ->orderBy('username')
                          ->get();

        // Lấy kỹ sư (engineer)
        $engineers = User::where('user_type', 'engineer')
                        ->orderBy('username')
                        ->get();

        return view('projects.create', compact('owners', 'contractors', 'engineers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'owner_id' => 'required|exists:users,id',
            'contractor_id' => 'required|exists:users,id',
            'engineer_id' => 'required|exists:users,id',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'total_budget' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:planned,in_progress,completed,on_hold,cancelled'
        ]);

        // Thêm user_id của người tạo
        $validated['user_id'] = auth()->id();

        // Tạo project
        $project = Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Dự án "' . $project->project_name . '" đã được tạo thành công!');
    }

    public function show(Project $project)
    {
        $project->load(['owner', 'contractor', 'engineer', 'sites', 'milestones', 'contracts']);
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        // Lấy danh sách users để chọn cho đội ngũ dự án
        $users = User::where('user_type', 'owner')->get();
        $contractors = User::where('user_type', 'contractor')->get();
        $engineers = User::where('user_type', 'engineer')->get();
        
        return view('projects.edit', compact('project', 'users', 'contractors', 'engineers'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255|unique:projects,project_name,' . $project->id,
            'owner_id' => 'required|exists:users,id',
            'contractor_id' => 'required|exists:users,id',
            'engineer_id' => 'required|exists:users,id',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'total_budget' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:planned,in_progress,completed,on_hold,cancelled'
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')->with('success', 'Dự án đã được cập nhật!');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Dự án đã được xóa!');
    }
}