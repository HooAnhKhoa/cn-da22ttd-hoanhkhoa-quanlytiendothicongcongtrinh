<?php

namespace App\Http\Controllers\Admin;

use App\Models\Task;
use App\Models\Site;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with('site');
        
        // Tìm kiếm theo tên công việc
        if ($request->has('search') && $request->search != '') {
            $query->where('task_name', 'like', '%' . $request->search . '%');
        }
        
        // Lọc theo công trường
        if ($request->has('site') && $request->site != '') {
            $query->where('site_id', $request->site);
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
        
        $tasks = $query->paginate(10);
        $sites = Site::orderBy('site_name')->get(); // Lấy danh sách công trường
        
        return view('admin.tasks.index', compact('tasks', 'sites'));
    }

    public function create(Request $request)
    {
        // Lấy site_id nếu tạo task từ site
        $siteId = $request->get('site_id');

        // Lấy danh sách site (chưa bị hủy)
        $sites = Site::where('status', '!=', 'cancelled')
                    ->orderBy('site_name')
                    ->get();

        // Task cha (nếu có site_id thì lọc theo site)
        $tasksQuery = Task::orderBy('task_name');

        if ($siteId) {
            $tasksQuery->where('site_id', $siteId);
        }

        $tasks = $tasksQuery->get();

        // Nhóm task theo site_id
        $tasksBySite = $tasks->groupBy('site_id');

        return view('admin.tasks.create', compact(
            'sites',
            'siteId',
            'tasksBySite'
        ));
    }

    // Hiển thị form tạo công việc từ site
    public function createFromSite(Site $site)
    {
        // Lấy tất cả sites để chọn (nếu cần)
        $sites = Site::all();
        
        // Lấy công việc cha của site này
        $parentTasks = Task::where('site_id', $site->id)
            ->whereNull('parent_id')
            ->get();
        
        return view('admin.tasks.create', [
            'site' => $site, // Truyền site vào view
            'sites' => $sites,
            'parentTasks' => $parentTasks,
            'selectedSiteId' => $site->id, // Đánh dấu site được chọn
        ]);
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

        return redirect()->route('admin.tasks.index')->with('success', 'Công việc đã được tạo thành công!');
    }

    public function show(Task $task)
    {
        // CHỈ load materialUsages với material, KHÔNG load materials (vì quan hệ không tồn tại)
        $task->load([
            'site.project',
            'parent',
            'children',
            'progressUpdates',
            'delays',
            'materialUsages.material', // Chỉ cần load này
        ]);

        // Tính tổng số lượng vật tư từ materialUsages
        $totalQuantity = $task->materialUsages->sum('quantity');
        
        // Chuẩn bị dữ liệu cho biểu đồ
        $chartColors = [
            '#4f46e5', '#10b981', '#f59e0b', '#ef4444', 
            '#8b5cf6', '#06b6d4', '#84cc16', '#f97316'
        ];

        return view('admin.tasks.show', compact('task', 'totalQuantity', 'chartColors'));
    }

    public function edit(Task $task)
    {
        // Lấy tất cả công trình
        $sites = Site::orderBy('site_name')->get();
        
        // Lấy các công việc có thể làm cha (cùng công trình, không phải chính nó, không phải con của nó)
        $parentTasks = Task::where('site_id', $task->site_id)
            ->where('id', '!=', $task->id)
            ->where(function($query) use ($task) {
                // Loại trừ các công việc con của task hiện tại
                $query->where('parent_id', '!=', $task->id)
                    ->orWhereNull('parent_id');
            })
            ->orderBy('task_name')
            ->get();
        
        return view('admin.tasks.edit', compact('task', 'sites', 'parentTasks'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'task_name' => 'required|string|max:255',
            'parent_id' => [
                'nullable',
                'exists:tasks,id',
                function ($attribute, $value, $fail) use ($task) {
                    // Không cho chọn chính nó làm cha
                    if ($value == $task->id) {
                        $fail('Không thể chọn chính công việc này làm công việc cha.');
                    }
                    
                    // Không cho chọn công việc con làm cha
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
        
        // Cập nhật task
        $task->update($validated);
        
        return redirect()->route('admin.tasks.show', $task)
            ->with('success', 'Công việc đã được cập nhật thành công!');
    }

    private function isChildOf(Task $parent, $childId)
    {
        $child = Task::find($childId);
        if (!$child) return false;
        
        // Kiểm tra đệ quy
        while ($child->parent_id) {
            if ($child->parent_id == $parent->id) {
                return true;
            }
            $child = Task::find($child->parent_id);
        }
        
        return false;
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('admin.tasks.index')->with('success', 'Công việc đã được xóa!');
    }
}