<?php

namespace App\Http\Controllers\Client;

use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Project::query();

        // 1. Phân quyền dữ liệu theo User Type
        // Thay vì chỉ where('owner_id'), ta check linh hoạt
        if ($user->user_type === 'owner') {
            $query->where('owner_id', $user->id);
        } elseif ($user->user_type === 'contractor') {
            $query->where('contractor_id', $user->id);
        } elseif ($user->user_type === 'engineer') {
            $query->where('engineer_id', $user->id);
        } else {
            // Trường hợp user có thể đóng nhiều vai trò hoặc admin
            $query->where(function($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhere('contractor_id', $user->id)
                  ->orWhere('engineer_id', $user->id);
            });
        }

        // Eager load tổng giá trị hợp đồng để hiển thị ngân sách
        $query->withSum('contracts', 'contract_value');

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
            case 'budget_desc':
                $query->orderByDesc('contracts_sum_contract_value');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }

        $projects = $query->paginate(12)->withQueryString();

        return view('client.projects.index', compact('projects'));
    }

    /** * Show the form for creating a new resource.
     */

    public function create()
    {
        // Lấy danh sách Owner để Nhà thầu chọn (Khách hàng)
        $owners = User::where('user_type', 'owner')->where('status', 'active')->get();
        
        // Lấy danh sách Engineer để Nhà thầu chọn (Tư vấn giám sát - nếu có)
        $engineers = User::where('user_type', 'engineer')->where('status', 'active')->get();

        return view('client.projects.create', compact('owners', 'engineers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'owner_id' => 'required|exists:users,id',     // Bắt buộc chọn chủ đầu tư
            'engineer_id' => 'nullable|exists:users,id',  // Kỹ sư có thể để trống
        ]);

        // Gán contractor_id là người đang đăng nhập
        $validated['contractor_id'] = auth()->id();
        $validated['status'] = 'draft'; // Mặc định là nháp

        // Tạo dự án
        Project::create($validated);

        return redirect()->route('client.projects.index')
            ->with('success', 'Dự án mới đã được tạo thành công!');
    }
    /**
     * Display the specified resource.
     */
    // ProjectController.php - cập nhật phương thức show()
    public function show(Project $project)
    {
        $user = Auth::user();

        // 1. Check quyền
        if ($project->owner_id !== $user->id && 
            $project->contractor_id !== $user->id && 
            $project->engineer_id !== $user->id) {
            abort(403, 'Bạn không có quyền truy cập vào dự án này.');
        }

        // 2. Nạp dữ liệu SÂU (Deep Eager Loading)
        $project->load([
            'owner', 'contractor', 'engineer',
            'contracts.payments',
            'documents',
            
            // Load Sites -> Tasks -> ProgressUpdates
            'sites' => function($q) {
                $q->orderBy('created_at', 'desc');
            },
            'sites.tasks' => function($q) {
                $q->orderBy('start_date', 'asc');
            },
            'sites.tasks.progressUpdates' => function($q) {
                $q->orderBy('date', 'desc');
            },
            // Chỉ cần load creator, KHÔNG load images vì ảnh nằm trong attached_files rồi
            'sites.tasks.progressUpdates.creator', 
        ]);

        // 3. Tính toán số liệu tổng quan
        $totalBudget = $project->contracts->sum('contract_value');
        $totalPaid = $project->contracts->sum(fn($c) => $c->payments->where('status', 'completed')->sum('amount'));

        return view('client.projects.show', compact('project', 'totalBudget', 'totalPaid'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}