<?php

namespace App\Http\Controllers\Client;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Project::where('owner_id', $user->id)
            ->withSum('contracts', 'contract_value'); // Thêm tổng hợp đồng

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
            
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }

        $projects = $query->paginate(12)->withQueryString();

        return view('client.projects.index', compact('projects'));
    }

    /** 
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $user = Auth::user();

        // 1. Kiểm tra Quyền hạn (Authorization)
        // Chỉ cho phép xem nếu User là Chủ đầu tư, Nhà thầu, hoặc Kỹ sư của dự án đó
        if ($project->owner_id !== $user->id && 
            $project->contractor_id !== $user->id && 
            $project->engineer_id !== $user->id) {
            abort(403, 'Bạn không có quyền truy cập vào dự án này.');
        }

        // 2. Eager Loading (Nạp sẵn dữ liệu quan hệ)
        // Dựa trên các biến được gọi trong View show.blade.php
        $project->load([
            'owner',                // Để hiển thị thông tin Chủ đầu tư
            'contractor',           // Để hiển thị thông tin Nhà thầu
            'engineer',             // Để hiển thị thông tin Kỹ sư
            'sites' => function($query) {
                // Sắp xếp công trường (tuỳ chọn)
                $query->orderBy('created_at', 'desc');
            },
            'sites.tasks',          // Cần load tasks trong site để tính toán Progress bar
            'milestones',           // Để đếm số lượng mốc quan trọng
            'contracts.contractor', // Để hiển thị tên nhà thầu trong tab Hợp đồng
            'documents',            // Để hiển thị danh sách tài liệu
        ]);

        // 3. Trả về View
        // Lưu ý: View bạn gửi nằm ở folder 'client.projects.show' (dựa theo logic folder index)
        return view('client.projects.show', compact('project'));
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
