<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    // Hiển thị danh sách công trường
    public function index()
    {
        $sites = Site::with('project')->latest()->paginate(10);
        return view('sites.index', compact('sites'));
    }

    // Hiển thị form tạo công trường
    public function create()
    {
        $projects = Project::all();
        // KHÔNG cần engineers, contractors vì bảng không có các cột này
        return view('sites.create', compact('projects'));
    }

    // Lưu công trường mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'progress_percent' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:planned,in_progress,completed,on_hold,cancelled',
        ]);

        Site::create($validated);

        return redirect()->route('sites.index')
            ->with('success', 'Công trường đã được tạo thành công!');
    }

    // Hiển thị chi tiết công trường
    public function show(Site $site)
    {
        // CHỈ load project, không load engineer, contractor
        $site->load(['project']);
        return view('sites.show', compact('site'));
    }

    // Hiển thị form chỉnh sửa
    public function edit(Site $site)
    {
        $projects = Project::all();
        // KHÔNG cần engineers, contractors
        return view('sites.edit', compact('site', 'projects'));
    }

    // Cập nhật công trường
    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'progress_percent' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:planned,in_progress,completed,on_hold,cancelled',
            // KHÔNG có engineer_id, contractor_id
        ]);

        $site->update($validated);

        return redirect()->route('sites.show', $site)
            ->with('success', 'Công trường đã được cập nhật thành công!');
    }

    // Xóa công trường
    public function destroy(Site $site)
    {
        $site->delete();
        
        return redirect()->route('sites.index')
            ->with('success', 'Công trường đã được xóa thành công!');
    }
}