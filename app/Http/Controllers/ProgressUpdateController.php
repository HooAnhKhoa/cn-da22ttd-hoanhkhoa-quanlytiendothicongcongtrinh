<?php

namespace App\Http\Controllers;

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
        
        return view('progress_updates.index', compact('progressUpdates'));
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
        
        return view('progress_updates.create', compact('tasks', 'taskId'));
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
            'attached_files.*' => 'nullable|file|max:10240', // 10MB max per file
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle file uploads
        $filePaths = [];
        if ($request->hasFile('attached_files')) {
            foreach ($request->file('attached_files') as $file) {
                $path = $file->store('progress_updates', 'public');
                $filePaths[] = $path;
            }
        }

        // Create progress update
        $progressUpdate = ProgressUpdate::create([
            'task_id' => $request->task_id,
            'date' => $request->date,
            'progress_percent' => $request->progress_percent,
            'description' => $request->description,
            'attached_files' => !empty($filePaths) ? json_encode($filePaths) : null,
            'created_by' => Auth::user()->name ?? Auth::user()->email,
        ]);

        // Update task progress
        $task = Task::find($request->task_id);
        if ($task) {
            $task->progress_percent = $request->progress_percent;
            $task->save();
        }

        return redirect()->route('tasks.show', $request->task_id)
            ->with('success', 'Báo cáo tiến độ đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $progressUpdate = ProgressUpdate::with(['task', 'creator'])
            ->findOrFail($id);
        
        return view('progress_updates.show', compact('progressUpdate'));
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
        
        return view('progress_updates.edit', compact('progressUpdate', 'tasks'));
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle file management
        $existingFiles = json_decode($progressUpdate->attached_files, true) ?? [];
        
        // Remove selected files
        if ($request->has('remove_files')) {
            foreach ($request->remove_files as $fileToRemove) {
                if (($key = array_search($fileToRemove, $existingFiles)) !== false) {
                    Storage::disk('public')->delete($fileToRemove);
                    unset($existingFiles[$key]);
                }
            }
            $existingFiles = array_values($existingFiles); // Reindex array
        }

        // Add new files
        if ($request->hasFile('attached_files')) {
            foreach ($request->file('attached_files') as $file) {
                $path = $file->store('progress_updates', 'public');
                $existingFiles[] = $path;
            }
        }

        // Update progress update
        $progressUpdate->update([
            'task_id' => $request->task_id,
            'date' => $request->date,
            'progress_percent' => $request->progress_percent,
            'description' => $request->description,
            'attached_files' => !empty($existingFiles) ? json_encode($existingFiles) : null,
        ]);

        // Update task progress if this is the latest report
        $latestReport = ProgressUpdate::where('task_id', $request->task_id)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($latestReport && $latestReport->id == $id) {
            $task = Task::find($request->task_id);
            if ($task) {
                $task->progress_percent = $request->progress_percent;
                $task->save();
            }
        }

        return redirect()->route('tasks.show', $request->task_id)
            ->with('success', 'Báo cáo tiến độ đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $progressUpdate = ProgressUpdate::findOrFail($id);
        $taskId = $progressUpdate->task_id;
        
        // Delete attached files
        if ($progressUpdate->attached_files) {
            $files = json_decode($progressUpdate->attached_files, true);
            foreach ($files as $file) {
                Storage::disk('public')->delete($file);
            }
        }
        
        $progressUpdate->delete();
        
        // Update task progress to latest report
        $latestReport = ProgressUpdate::where('task_id', $taskId)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
        
        $task = Task::find($taskId);
        if ($task) {
            $task->progress_percent = $latestReport ? $latestReport->progress_percent : 0;
            $task->save();
        }

        return redirect()->route('tasks.show', $taskId)
            ->with('success', 'Báo cáo tiến độ đã được xóa thành công!');
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
        $files = json_decode($progressUpdate->attached_files, true) ?? [];
        
        if (in_array($filename, $files)) {
            $filePath = storage_path('app/public/' . $filename);
            
            if (file_exists($filePath)) {
                return response()->download($filePath, basename($filename));
            }
        }
        
        return redirect()->back()
            ->with('error', 'File không tồn tại hoặc đã bị xóa.');
    }
}