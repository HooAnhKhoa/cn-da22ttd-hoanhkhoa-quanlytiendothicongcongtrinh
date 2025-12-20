<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    // Hiển thị danh sách công trường
    public function index(Request $request)
    {
        $query = Site::with('project'); // nếu công trường thuộc dự án

        // 🔍 Tìm kiếm theo tên công trường
        if ($request->filled('search')) {
            $query->where('site_name', 'like', '%' . $request->search . '%');
        }

        // 🏷️ Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🔃 Sắp xếp
        switch ($request->sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;

            case 'name':
                $query->orderBy('site_name', 'asc');
                break;

            case 'progress':
                $query->orderBy('progress', 'desc'); // % tiến độ
                break;

            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }

        $sites = $query->paginate(12)->withQueryString();

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

    private function getMaterialTypeColor($type)
    {
        $colors = [
            'building_materials' => 'bg-blue-100 text-blue-800',
            'electrical' => 'bg-yellow-100 text-yellow-800',
            'plumbing' => 'bg-green-100 text-green-800',
            'finishing' => 'bg-purple-100 text-purple-800',
            'tools' => 'bg-red-100 text-red-800',
            'safety' => 'bg-orange-100 text-orange-800',
            'other' => 'bg-gray-100 text-gray-800'
        ];
        return $colors[$type] ?? 'bg-gray-100 text-gray-800';
    }

    // Hiển thị chi tiết công trường
    // Hiển thị chi tiết công trường
    public function show(Site $site)
    {
        // Load project
        $site->load(['project']);
        
        // Lấy tất cả tasks của site này
        $tasks = \App\Models\Task::where('site_id', $site->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Lấy tổng hợp vật tư sử dụng trong site
        $materialSummary = \DB::table('material_usages')
            ->join('tasks', 'material_usages.task_id', '=', 'tasks.id')
            ->join('materials', 'material_usages.material_id', '=', 'materials.id')
            ->where('tasks.site_id', $site->id)
            ->select(
                'materials.id',
                'materials.materials_name',
                'materials.unit',
                'materials.type',
                'materials.supplier',
                \DB::raw('SUM(material_usages.quantity) as total_quantity'),
                \DB::raw('COUNT(material_usages.id) as usage_count'),
                \DB::raw('MAX(material_usages.usage_date) as last_usage_date')
            )
            ->groupBy('materials.id', 'materials.materials_name', 'materials.unit', 'materials.type', 'materials.supplier')
            ->orderBy('total_quantity', 'desc')
            ->get();
        
        // Lấy tổng hợp theo loại
        $typeSummary = \DB::table('material_usages')
            ->join('tasks', 'material_usages.task_id', '=', 'tasks.id')
            ->join('materials', 'material_usages.material_id', '=', 'materials.id')
            ->where('tasks.site_id', $site->id)
            ->select(
                'materials.type',
                \DB::raw('SUM(material_usages.quantity) as total_quantity')
            )
            ->groupBy('materials.type')
            ->orderBy('total_quantity', 'desc')
            ->get();
        
        // Helper function cho màu loại vật tư
        $getMaterialTypeColor = function($type) {
            $colors = [
                'building_materials' => 'bg-blue-100 text-blue-800',
                'electrical' => 'bg-yellow-100 text-yellow-800',
                'plumbing' => 'bg-green-100 text-green-800',
                'finishing' => 'bg-purple-100 text-purple-800',
                'tools' => 'bg-red-100 text-red-800',
                'safety' => 'bg-orange-100 text-orange-800',
                'other' => 'bg-gray-100 text-gray-800'
            ];
            return $colors[$type] ?? 'bg-gray-100 text-gray-800';
        };
        
        return view('sites.show', compact('site', 'tasks', 'materialSummary', 'typeSummary', 'getMaterialTypeColor'));
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