<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ProgressUpdate;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProgressUpdateController extends Controller
{
    /**
     * Hiển thị danh sách tiến độ
     * Có thể lọc theo task_id nếu được truyền vào
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = ProgressUpdate::with(['task.site.project', 'creator']);

        // 1. Lọc theo Task cụ thể (nếu có request)
        if ($request->filled('task_id')) {
            $query->where('task_id', $request->task_id);
        }

        // 2. PHÂN QUYỀN: Chỉ lấy các báo cáo thuộc dự án mà user có quyền xem
        $query->whereHas('task.site.project', function($q) use ($user) {
            if ($user->user_type === 'owner') {
                $q->where('owner_id', $user->id);
            } elseif ($user->user_type === 'contractor') {
                $q->where('contractor_id', $user->id);
            } elseif ($user->user_type === 'engineer') {
                $q->where('engineer_id', $user->id);
            }
            // Admin xem được hết
        });

        // 3. Sắp xếp: Mới nhất lên đầu
        $updates = $query->orderBy('date', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate(15)
                         ->withQueryString();

        // Lấy thông tin Task để hiển thị tiêu đề nếu đang lọc
        $currentTask = null;
        if ($request->filled('task_id')) {
            $currentTask = Task::find($request->task_id);
        }

        return view('client.progress_updates.index', compact('updates', 'currentTask'));
    }

    /**
     * Hiển thị chi tiết một báo cáo
     */
    public function show(ProgressUpdate $progressUpdate)
    {
        // Check quyền (Policy hoặc logic đơn giản)
        // ...
        
        $progressUpdate->load(['task.site.project', 'creator']);
        return view('client.progress_updates.show', compact('progressUpdate'));
    }

    public function create(Request $request)
    {
        $taskId = $request->input('task_id');
        
        if ($taskId) {
            $task = Task::findOrFail($taskId);
            $tasks = collect([$task]);
        } else {
            $tasks = Task::where('status', '!=', 'completed')
                ->orderBy('task_name')
                ->get();
        }
        
        return view('client.progress_updates.create', compact('tasks', 'taskId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required|exists:tasks,id',
            'date' => 'required|date',
            'progress_percent' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string|max:2000',
            'attached_files.*' => 'nullable|file|max:10240', // 10MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // 1. Xử lý upload file
        $filePaths = [];
        if ($request->hasFile('attached_files')) {
            foreach ($request->file('attached_files') as $file) {
                // Lưu vào folder 'public/progress_updates'
                $path = $file->store('progress_updates', 'public');
                $filePaths[] = $path;
            }
        }

        // 2. Tạo bản ghi (Model tự ép kiểu mảng sang JSON)
        $progressUpdate = ProgressUpdate::create([
            'task_id' => $request->task_id,
            'date' => $request->date,
            'progress_percent' => $request->progress_percent,
            'description' => $request->description,
            'attached_files' => !empty($filePaths) ? $filePaths : null, // Truyền mảng trực tiếp
            'created_by' => Auth::id(),
        ]);

        // 3. Cập nhật tiến độ Task
        $task = Task::find($request->task_id);
        if ($task) {
            $task->progress_percent = $request->progress_percent;
            // Tự động chuyển trạng thái nếu đạt 100%
            if ($request->progress_percent == 100) {
                $task->status = 'completed';
                $task->end_date = now();
            } elseif ($task->status == 'planned' && $request->progress_percent > 0) {
                $task->status = 'in_progress';
                $task->start_date = now();
            }
            $task->save();
        }

        return redirect()->route('client.tasks.show', $request->task_id)
            ->with('success', 'Báo cáo tiến độ đã được tạo thành công!');
    }
}