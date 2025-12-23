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

        return view('admin.projects.index', compact('projects'));
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
    public function show(string $id)
    {
        //
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
