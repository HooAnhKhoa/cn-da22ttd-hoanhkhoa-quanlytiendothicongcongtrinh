<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Site;
use App\Models\Admin\Project;
use App\Models\Admin\Task;
use App\Models\Admin\MaterialUsage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiteController extends Controller
{
    // Hiển thị danh sách công trường
    public function index(Request $request)
    {
        $query = Site::with('project');
        
        // Tìm kiếm theo tên công trường
        if ($request->has('search') && $request->search != '') {
            $query->where('site_name', 'like', '%' . $request->search . '%');
        }
        
        // Lọc theo dự án
        if ($request->has('project') && $request->project != '') {
            $query->where('project_id', $request->project);
        }
        
        // Lọc theo trạng thái
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Sắp xếp
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'name':
                    $query->orderBy('site_name', 'asc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $sites = $query->paginate(10);
        $projects = Project::orderBy('project_name')->get(); // Lấy danh sách dự án
        
        return view('admin.sites.index', compact('sites', 'projects'));
    }

    // Hiển thị form tạo công trường
    public function create(Request $request)
    {
        $projectId = $request->get('project_id');

        $projects = Project::where('status', '!=', 'cancelled')->get();

        $selectedProject = null;
        if ($projectId) {
            $selectedProject = Project::find($projectId);
        }

        return view('admin.sites.create', compact('projects', 'selectedProject', 'projectId'));
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
    public function show(Site $site)
    {
        // Lấy tất cả công việc thuộc công trường
        $tasks = Task::where('site_id', $site->id)->get();
        
        // Tính toán tiến độ tổng thể
        $totalProgress = 0;
        $overallProgress = 0;
        
        if($tasks->count() > 0) {
            foreach($tasks as $task) {
                $totalProgress += $task->progress_percent ?? 0;
            }
            $overallProgress = round($totalProgress / $tasks->count(), 1);
        }
        
        // Tổng hợp vật tư
        $materialSummary = MaterialUsage::whereIn('task_id', $tasks->pluck('id'))
            ->join('materials', 'material_usages.material_id', '=', 'materials.id')
            ->select(
                'materials.id',
                'materials.materials_name',
                'materials.type',
                'materials.unit',
                'materials.supplier',
                DB::raw('SUM(material_usages.quantity) as total_quantity'),
                DB::raw('COUNT(material_usages.id) as usage_count'),
                DB::raw('MAX(material_usages.usage_date) as last_usage_date')
            )
            ->groupBy('materials.id', 'materials.materials_name', 'materials.type', 'materials.unit', 'materials.supplier')
            ->get();
            
        // Tổng hợp theo loại
        $typeSummary = MaterialUsage::whereIn('task_id', $tasks->pluck('id'))
            ->join('materials', 'material_usages.material_id', '=', 'materials.id')
            ->select(
                'materials.type',
                DB::raw('SUM(material_usages.quantity) as total_quantity'),
                DB::raw('COUNT(DISTINCT materials.id) as type_count')
            )
            ->groupBy('materials.type')
            ->get();
            
        return view('admin.sites.show', compact(
            'site', 
            'tasks', 
            'overallProgress',
            'materialSummary',
            'typeSummary'
        ));
    }

    // Hiển thị form chỉnh sửa
    public function edit(Site $site)
    {
        $projects = Project::all();
        // KHÔNG cần engineers, contractors
        return view('admin.sites.edit', compact('site', 'projects'));
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

        return redirect()->route('admin.sites.show', $site)
            ->with('success', 'Công trường đã được cập nhật thành công!');
    }

    // Xóa công trường
    public function destroy(Site $site)
    {
        $site->delete();
        
        return redirect()->route('admin.sites.index')
            ->with('success', 'Công trường đã được xóa thành công!');
    }
}