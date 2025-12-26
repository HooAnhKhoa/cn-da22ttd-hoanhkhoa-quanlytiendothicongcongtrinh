<?php

namespace App\Http\Controllers\Admin;

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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProgressUpdate::with(['task', 'user']);

        // 🔹 Lọc theo công việc
        if ($request->filled('task_id')) {
            $query->where('task_id', $request->task_id);
        }

        // 🔹 Lọc từ ngày
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // 🔹 Lọc đến ngày
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // 🔹 Sắp xếp mới nhất
        $progressUpdates = ProgressUpdate::with(['task', 'creator'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.progress_updates.index', compact('progressUpdates'));
    }

    /**
     * Show the form for creating a new resource.
     */
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
        
        return view('admin.progress_updates.create', compact('tasks', 'taskId'));
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

        return redirect()->route('admin.tasks.show', $request->task_id)
            ->with('success', 'Báo cáo tiến độ đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $progressUpdate = ProgressUpdate::with(['task.site.project', 'reporter'])
            ->findOrFail($id);
        
        return view('admin.progress_updates.show', compact('progressUpdate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $progressUpdate = ProgressUpdate::findOrFail($id);
        $tasks = Task::where('id', $progressUpdate->task_id)
            ->orWhere('status', '!=', 'completed')
            ->orderBy('task_name')
            ->get();
        
        return view('admin.progress_updates.edit', compact('progressUpdate', 'tasks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $progressUpdate = ProgressUpdate::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'task_id' => 'required|exists:tasks,id',
            'date' => 'required|date',
            'progress_percent' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string|max:2000',
            'attached_files.*' => 'nullable|file|max:10240',
            'remove_files' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // 1. Lấy danh sách file hiện tại (Model tự cast sang Array, KHÔNG dùng json_decode)
        $existingFiles = $progressUpdate->attached_files ?? [];
        
        // 2. Xóa file được chọn (nếu có)
        if ($request->has('remove_files')) {
            foreach ($request->remove_files as $fileToRemove) {
                if (($key = array_search($fileToRemove, $existingFiles)) !== false) {
                    Storage::disk('public')->delete($fileToRemove); // Xóa file vật lý
                    unset($existingFiles[$key]); // Xóa khỏi mảng
                }
            }
            $existingFiles = array_values($existingFiles); // Sắp xếp lại chỉ số mảng
        }

        // 3. Thêm file mới
        if ($request->hasFile('attached_files')) {
            foreach ($request->file('attached_files') as $file) {
                $path = $file->store('progress_updates', 'public');
                $existingFiles[] = $path;
            }
        }

        // 4. Lưu cập nhật (Truyền mảng trực tiếp, KHÔNG dùng json_encode)
        $progressUpdate->update([
            'task_id' => $request->task_id,
            'date' => $request->date,
            'progress_percent' => $request->progress_percent,
            'description' => $request->description,
            'attached_files' => !empty($existingFiles) ? $existingFiles : null,
        ]);

        // Cập nhật lại task nếu đây là báo cáo mới nhất
        $latestReport = ProgressUpdate::where('task_id', $request->task_id)
            ->orderBy('date', 'desc')->first();
        
        if ($latestReport && $latestReport->id == $id) {
            $task = Task::find($request->task_id);
            if ($task) {
                $task->progress_percent = $request->progress_percent;
                $task->save();
            }
        }

        return redirect()->route('admin.tasks.show', $request->task_id)
            ->with('success', 'Báo cáo tiến độ đã được cập nhật!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $progressUpdate = ProgressUpdate::findOrFail($id);
        $taskId = $progressUpdate->task_id;
        
        // 1. Xóa file vật lý (Model tự cast sang Array)
        if (!empty($progressUpdate->attached_files)) {
            foreach ($progressUpdate->attached_files as $file) {
                Storage::disk('public')->delete($file);
            }
        }
        
        $progressUpdate->delete();
        
        // Cập nhật lại task về trạng thái của báo cáo liền trước
        $latestReport = ProgressUpdate::where('task_id', $taskId)
            ->orderBy('date', 'desc')->first();
        
        $task = Task::find($taskId);
        if ($task) {
            $task->progress_percent = $latestReport ? $latestReport->progress_percent : 0;
            $task->save();
        }

        return redirect()->route('admin.tasks.show', $taskId)
            ->with('success', 'Báo cáo tiến độ đã xóa!');
    }

    /**
     * Get progress updates for a specific task (API endpoint).
     */
    public function getTaskProgressUpdates($taskId)
    {
        $progressUpdates = ProgressUpdate::where('task_id', $taskId)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($progressUpdates);
    }

    /**
     * Display progress chart for a task.
     */
    public function progressChart($taskId)
    {
        $task = Task::findOrFail($taskId);
        $progressUpdates = ProgressUpdate::where('task_id', $taskId)
            ->orderBy('date', 'asc')
            ->get();
        
        $chartData = [
            'labels' => [],
            'data' => [],
            'descriptions' => []
        ];
        
        foreach ($progressUpdates as $update) {
            $chartData['labels'][] = $update->date->format('d/m/Y');
            $chartData['data'][] = $update->progress_percent;
            $chartData['descriptions'][] = $update->description ?: '';
        }
        
        return view('progress_updates.chart', compact('task', 'chartData'));
    }

    /**
     * Download an attached file.
     */
    public function downloadFile($id, $filename)
    {
        $progressUpdate = ProgressUpdate::findOrFail($id);
        
        // Model tự cast sang Array
        $files = $progressUpdate->attached_files ?? [];
        
        // Kiểm tra xem file có trong danh sách database không (an toàn)
        // Lưu ý: $files lưu đường dẫn đầy đủ 'progress_updates/abc.jpg', còn $filename chỉ là 'abc.jpg'
        // Nên ta cần check basename hoặc đường dẫn
        $found = false;
        $fullPath = '';
        
        foreach($files as $f) {
            if (basename($f) == $filename) {
                $found = true;
                $fullPath = $f;
                break;
            }
        }
        
        if ($found && Storage::disk('public')->exists($fullPath)) {
            return Storage::disk('public')->download($fullPath);
        }
        
        return redirect()->back()->with('error', 'File không tồn tại.');
    }
}