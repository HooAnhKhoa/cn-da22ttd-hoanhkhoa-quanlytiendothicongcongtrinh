<?php

namespace App\Http\Controllers\Client;

use App\Models\Site;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\MaterialUsage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EngineerSiteController extends Controller
{
    /**
     * Danh sách công trường (Index)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // 1. Query chính lấy danh sách Sites
        $query = Site::with('project');

        // 2. Query phụ lấy danh sách Projects (để đổ vào dropdown filter)
        $projectQuery = Project::query();

        // --- PHÂN QUYỀN ---
        if ($user->user_type === 'contractor') {
            // Nhà thầu: Thấy site & project của mình
            $query->whereHas('project', fn($q) => $q->where('contractor_id', $user->id));
            $projectQuery->where('contractor_id', $user->id);
        } 
        elseif ($user->user_type === 'engineer') {
            // Kỹ sư: Thấy site & project mình giám sát
            $query->whereHas('project', fn($q) => $q->where('engineer_id', $user->id));
            $projectQuery->where('engineer_id', $user->id);
        }
        elseif ($user->user_type === 'owner') {
            // Chủ đầu tư: Thấy site & project của mình
            $query->whereHas('project', fn($q) => $q->where('owner_id', $user->id));
            $projectQuery->where('owner_id', $user->id);
        }

        // --- BỘ LỌC ---
        // 1. Lọc theo từ khóa
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('site_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // 2. Lọc theo Dự án (Nếu user chọn từ dropdown)
        if ($request->filled('project')) {
            $query->where('project_id', $request->project);
        }

        // 3. Lọc theo Trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sites = $query->orderBy('created_at', 'desc')
                       ->paginate(10)
                       ->withQueryString();

        // Lấy danh sách dự án để truyền sang View
        $projects = $projectQuery->get();

        return view('client.sites.index', compact('sites', 'projects'));
    }

    /**
     * Form tạo mới (Create) - Cho phép Contractor và Engineer
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        // Check quyền
        if (!in_array($user->user_type, ['contractor', 'engineer'])) {
            abort(403, 'Bạn không có quyền tạo công trường.');
        }

        // Lấy danh sách dự án phù hợp với vai trò
        $projectsQuery = Project::query();
        
        if ($user->user_type === 'contractor') {
            $projectsQuery->where('contractor_id', $user->id);
        } elseif ($user->user_type === 'engineer') {
            $projectsQuery->where('engineer_id', $user->id);
        }
        
        $projects = $projectsQuery->get();

        // Hỗ trợ chọn sẵn project nếu truyền từ URL
        $selectedProject = null;
        if ($request->has('project_id')) {
            $selectedProject = $projects->find($request->project_id);
        }

        return view('client.sites.create', compact('projects', 'selectedProject'));
    }

    /**
     * Lưu công trường (Store)
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->user_type, ['contractor', 'engineer'])) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'site_name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'progress_percent' => 'required|integer|min:0|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:planned,in_progress,completed,on_hold,cancelled',
        ]);

        // Kiểm tra Project thuộc về Contractor HOẶC Engineer này
        $project = Project::where('id', $request->project_id)
            ->where(function($q) use ($user) {
                $q->where('contractor_id', $user->id)
                  ->orWhere('engineer_id', $user->id);
            })
            ->firstOrFail();

        Site::create($request->all());

        return redirect()->route('client.sites.index')
                         ->with('success', 'Tạo công trường thành công.');
    }

    /**
     * Xem chi tiết (Show) - ĐÃ SỬA CỘT MATERIALS_NAME
     */
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
            
        return view('client.sites.show', compact(
            'site', 
            'tasks', 
            'overallProgress',
            'materialSummary',
            'typeSummary'
        ));
    }

    /**
     * Form chỉnh sửa (Edit)
     */
    public function edit(Site $site)
    {
        $user = Auth::user();

        if (!in_array($user->user_type, ['contractor', 'engineer'])) {
            abort(403, 'Bạn không có quyền chỉnh sửa công trường.');
        }

        $this->authorizeSiteAccess($site);

        // Lấy danh sách dự án
        $projects = Project::query();
        if ($user->user_type === 'contractor') {
            $projects->where('contractor_id', $user->id);
        } elseif ($user->user_type === 'engineer') {
            $projects->where('engineer_id', $user->id);
        }
        $projects = $projects->get();

        return view('client.sites.edit', compact('site', 'projects'));
    }

    /**
     * Cập nhật (Update)
     */
    public function update(Request $request, Site $site)
    {
        $user = Auth::user();

        if (!in_array($user->user_type, ['contractor', 'engineer'])) {
            abort(403, 'Unauthorized');
        }

        $this->authorizeSiteAccess($site);

        $request->validate([
            'site_name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'location' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'progress_percent' => 'required|integer|min:0|max:100',
            'description' => 'nullable|string',
            'status' => 'required',
        ]);

        // Kiểm tra project mới (nếu đổi) có thuộc về user không
        if ($request->project_id != $site->project_id) {
            Project::where('id', $request->project_id)
                ->where(function($q) use ($user) {
                    $q->where('contractor_id', $user->id)
                      ->orWhere('engineer_id', $user->id);
                })
                ->firstOrFail();
        }

        $site->update($request->all());

        return redirect()->route('client.sites.index')
                         ->with('success', 'Cập nhật công trường thành công.');
    }

    /**
     * Xóa (Destroy)
     */
    public function destroy(Site $site)
    {
        $user = Auth::user();

        if (!in_array($user->user_type, ['contractor', 'engineer'])) {
            abort(403, 'Bạn không được phép xóa công trường.');
        }

        $this->authorizeSiteAccess($site);

        if ($site->tasks()->count() > 0) {
            return back()->with('error', 'Không thể xóa công trường đã có công việc.');
        }

        $site->delete();

        return redirect()->route('client.sites.index')
                         ->with('success', 'Đã xóa công trường.');
    }

    // --- Helper Functions ---

    /**
     * Hàm check quyền: User có được xem/sửa Site này không?
     */
    private function authorizeSiteAccess(Site $site)
    {
        $user = Auth::user();
        $project = $site->project;

        $hasAccess = false;

        if ($user->user_type === 'contractor' && $project->contractor_id === $user->id) {
            $hasAccess = true;
        } 
        elseif ($user->user_type === 'engineer' && $project->engineer_id === $user->id) {
            $hasAccess = true;
        }
        elseif ($user->user_type === 'owner' && $project->owner_id === $user->id) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            abort(403, 'Bạn không có quyền truy cập công trường này.');
        }
    }
}