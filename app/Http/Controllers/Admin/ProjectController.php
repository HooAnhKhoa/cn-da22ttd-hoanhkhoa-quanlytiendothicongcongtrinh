<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Http\Controllers\Controller;    

class ProjectController extends Controller
{   
    public function index(Request $request)
    {
        $query = Project::with(['owner', 'contractor', 'engineer'])
            ->withSum('contracts', 'contract_value'); // Thêm tổng hợp đồng

        // 🔍 Tìm kiếm theo tên dự án
        if ($request->filled('search')) {
            $query->where('project_name', 'like', '%' . $request->search . '%');
        }

        // 🏷️ Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ⭐ LUÔN ĐẨY DỰ ÁN ĐÃ HỦY XUỐNG CUỐI
        $query->orderByRaw("status = 'cancelled' ASC");

        // 🔃 Sắp xếp
        switch ($request->sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            
            case 'name':
                $query->orderBy('project_name', 'asc');
                break;
            
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }

        $projects = $query->paginate(12)->withQueryString();

        return view('admin.projects.index', compact('projects'));
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

        return view('admin.projects.create', compact('owners', 'contractors', 'engineers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255|unique:projects,project_name',
            'owner_id' => 'required|exists:users,id',
            'contractor_id' => 'required|exists:users,id',
            'engineer_id' => 'required|exists:users,id',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:draft,pending_contract,in_progress,completed,on_hold,cancelled'
        ]);

        // Đặt trạng thái mặc định là "draft" nếu không có
        if (!isset($validated['status'])) {
            $validated['status'] = 'draft';
        }

        // Tạo project
        $project = Project::create($validated);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Dự án "' . $project->project_name . '" đã được tạo thành công!');
    }

    public function show(Project $project)
    {
        $project->load([
            'owner',
            'contractor', 
            'engineer',
            'contracts' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'sites' => function($query) {
                // Load số lượng tasks và tổng vật liệu đã dùng
                $query->withCount(['tasks'])
                    ->with(['tasks' => function($taskQuery) {
                        $taskQuery->withCount(['materialUsages'])
                                ->withSum('materialUsages as total_material_quantity', 'quantity');
                    }]);
            },
        ]);
        
        $hasContracts = $project->contracts->count() > 0;

        return view('admin.projects.show', compact('project', 'hasContracts'));
    }

    public function edit(Project $project)
    {
        // Lấy danh sách users để chọn cho đội ngũ dự án
        $owners = User::where('user_type', 'owner')->get();
        $contractors = User::where('user_type', 'contractor')->get();
        $engineers = User::where('user_type', 'engineer')->get();
        
        return view('admin.projects.edit', compact('project', 'owners', 'contractors', 'engineers'));
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
            'description' => 'nullable|string',
            'status' => 'required|in:draft,pending_contract,in_progress,completed,on_hold,cancelled'
        ]);

        $project->update($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Dự án đã được cập nhật!');
    }

    public function destroy(Project $project)
    {
        $project->update([
            'status' => 'cancelled'
        ]);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Dự án đã được hủy!');
    }
}