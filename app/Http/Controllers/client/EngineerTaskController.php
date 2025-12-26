<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EngineerTaskController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Task::with(['site.project', 'parent']);
        
        // --- 1. THÊM PHÂN QUYỀN (Logic bắt buộc) ---
        if ($user->user_type === 'engineer') {
            // Kỹ sư: Thấy task được giao HOẶC task trong dự án mình giám sát
            $query->where(function($q) use ($user) {
                $q->where('assigned_engineer_id', $user->id)
                  ->orWhereHas('site.project', fn($p) => $p->where('engineer_id', $user->id));
            });
        } 
        elseif ($user->user_type === 'contractor') {
            // Nhà thầu: Thấy task trong dự án của mình
            $query->whereHas('site.project', fn($p) => $p->where('contractor_id', $user->id));
        }
        elseif ($user->user_type === 'owner') {
            $query->whereHas('site.project', fn($p) => $p->where('owner_id', $user->id));
        }

        // --- 2. GIỮ NGUYÊN LOGIC LỌC CỦA BẠN ---
        
        // Tìm kiếm theo tên công việc
        if ($request->filled('search')) {
            $query->where('task_name', 'like', '%' . $request->search . '%');
        }
        
        // Lọc theo công trường
        if ($request->filled('site')) {
            $query->where('site_id', $request->site);
        }
        
        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Sắp xếp
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'name':
                    $query->orderBy('task_name', 'asc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $tasks = $query->paginate(10)->withQueryString();
        
        // Lấy danh sách site để filter (Cần filter theo user để không lộ site khác)
        $sites = $this->getSitesForUser($user);
        
        return view('client.tasks.index', compact('tasks', 'sites'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        // Check quyền
        if (!in_array($user->user_type, ['contractor', 'engineer'])) {
            abort(403, 'Bạn không có quyền tạo công việc.');
        }

        // Lấy site_id nếu tạo task từ site
        $siteId = $request->get('site_id');

        // Lấy danh sách site (Đã lọc theo quyền user)
        $sites = $this->getSitesForUser($user);

        // Task cha (nếu có site_id thì lọc theo site)
        $tasksQuery = Task::orderBy('task_name');

        if ($siteId) {
            $tasksQuery->where('site_id', $siteId);
        }

        $tasks = $tasksQuery->get();

        // Nhóm task theo site_id
        $tasksBySite = $tasks->groupBy('site_id');

        return view('client.tasks.create', compact(
            'sites',
            'siteId',
            'tasksBySite'
        ));
    }

    public function store(Request $request)
    {
        // Cho phép cả Contractor và Engineer
        $user = Auth::user();
        if (!in_array($user->user_type, ['contractor', 'engineer'])) {
            abort(403, 'Unauthorized');
        }

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

        // Check quyền sở hữu Site
        $site = Site::with('project')->findOrFail($request->site_id);
        $hasRight = false;
        if ($user->user_type === 'contractor' && $site->project->contractor_id === $user->id) $hasRight = true;
        if ($user->user_type === 'engineer' && $site->project->engineer_id === $user->id) $hasRight = true;

        if (!$hasRight) abort(403, 'Không có quyền trên công trường này.');

        Task::create($validated);

        return redirect()->route('client.tasks.index')->with('success', 'Công việc đã được tạo thành công!');
    }

    public function show(Task $task)
    {
        $this->authorizeTaskAccess($task);

        // Giữ nguyên load quan hệ như bạn muốn
        $task->load([
            'site.project',
            'parent',       
            'children',
            'progressUpdates',
            'materialUsages.material',
        ]);

        // Giữ nguyên biến hiển thị của bạn
        $totalQuantity = $task->materialUsages->sum('quantity');
        
        $chartColors = [
            '#4f46e5', '#10b981', '#f59e0b', '#ef4444', 
            '#8b5cf6', '#06b6d4', '#84cc16', '#f97316'
        ];

        return view('client.tasks.show', compact('task', 'totalQuantity', 'chartColors'));
    }

    public function edit(Task $task)
    {
        $user = Auth::user();
        if (!in_array($user->user_type, ['contractor', 'engineer'])) abort(403);
        $this->authorizeTaskAccess($task);

        // Lấy tất cả công trình (theo quyền)
        $sites = $this->getSitesForUser($user);
        
        // Lấy các công việc có thể làm cha
        $parentTasks = Task::where('site_id', $task->site_id)
            ->where('id', '!=', $task->id)
            ->where(function($query) use ($task) {
                $query->where('parent_id', '!=', $task->id)
                    ->orWhereNull('parent_id');
            })
            ->orderBy('task_name')
            ->get();
        
        return view('client.tasks.edit', compact('task', 'sites', 'parentTasks'));
    }

    public function update(Request $request, Task $task)
    {
        $user = Auth::user();
        if (!in_array($user->user_type, ['contractor', 'engineer'])) abort(403);
        $this->authorizeTaskAccess($task);

        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'task_name' => 'required|string|max:255',
            'parent_id' => [
                'nullable',
                'exists:tasks,id',
                function ($attribute, $value, $fail) use ($task) {
                    if ($value == $task->id) {
                        $fail('Không thể chọn chính công việc này làm công việc cha.');
                    }
                    if ($value && $this->isChildOf($task, $value)) {
                        $fail('Không thể chọn công việc con làm công việc cha.');
                    }
                }
            ],
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'planned_duration' => 'nullable|integer|min:0',
            'progress_percent' => 'nullable|integer|min:0|max:100',
            'status' => 'required|in:planned,in_progress,completed,on_hold,cancelled',
        ]);
        
        $task->update($validated);
        
        return redirect()->route('client.tasks.show', $task)
            ->with('success', 'Công việc đã được cập nhật thành công!');
    }

    public function destroy(Task $task)
    {
        $user = Auth::user();
        if (!in_array($user->user_type, ['contractor', 'engineer'])) abort(403);
        $this->authorizeTaskAccess($task);

        $task->delete();
        return redirect()->route('client.tasks.index')->with('success', 'Công việc đã được xóa!');
    }

    // --- Helper ---

    private function isChildOf(Task $parent, $childId)
    {
        $child = Task::find($childId);
        if (!$child) return false;
        while ($child->parent_id) {
            if ($child->parent_id == $parent->id) return true;
            $child = Task::find($child->parent_id);
        }
        return false;
    }

    private function getSitesForUser($user)
    {
        $query = Site::query();
        if ($user->user_type === 'contractor') {
            $query->whereHas('project', fn($q) => $q->where('contractor_id', $user->id));
        } elseif ($user->user_type === 'engineer') {
            $query->whereHas('project', fn($q) => $q->where('engineer_id', $user->id));
        } elseif ($user->user_type === 'owner') {
            $query->whereHas('project', fn($q) => $q->where('owner_id', $user->id));
        }
        return $query->orderBy('site_name')->get();
    }

    private function authorizeTaskAccess($task)
    {
        $user = Auth::user();
        $project = $task->site->project;
        $hasAccess = false;

        if ($user->user_type === 'contractor' && $project->contractor_id === $user->id) $hasAccess = true;
        elseif ($user->user_type === 'engineer' && ($project->engineer_id === $user->id || $task->assigned_engineer_id === $user->id)) $hasAccess = true;
        elseif ($user->user_type === 'owner' && $project->owner_id === $user->id) $hasAccess = true;

        if (!$hasAccess) abort(403, 'Bạn không có quyền truy cập công việc này.');
    }
}