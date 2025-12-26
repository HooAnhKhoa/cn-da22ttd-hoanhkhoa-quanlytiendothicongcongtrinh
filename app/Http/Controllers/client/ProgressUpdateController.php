<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ProgressUpdate;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}