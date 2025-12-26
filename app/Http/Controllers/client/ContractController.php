<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_SIGNATURE = 'pending_signature';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_TERMINATED = 'terminated';
    const STATUS_ON_HOLD = 'on_hold';
    const STATUS_EXPIRED = 'expired';

    // Note: Payment status is now calculated dynamically, but we keep constants for UI
    const PAYMENT_STATUS_UNPAID = 'unpaid';
    const PAYMENT_STATUS_PARTIALLY_PAID = 'partially_paid';
    const PAYMENT_STATUS_FULLY_PAID = 'fully_paid';
    const PAYMENT_STATUS_OVERDUE = 'overdue';

    public static function getStatuses()
    {
        return [
            self::STATUS_DRAFT => 'Bản nháp',
            self::STATUS_PENDING_SIGNATURE => 'Chờ ký',
            self::STATUS_ACTIVE => 'Đang hoạt động',
            self::STATUS_COMPLETED => 'Đã hoàn thành',
            self::STATUS_TERMINATED => 'Chấm dứt',
            self::STATUS_ON_HOLD => 'Tạm ngưng',
            self::STATUS_EXPIRED => 'Hết hạn',
        ];
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. QUERY CƠ BẢN: Lọc hợp đồng thuộc về Dự án mà user là Owner hoặc Contractor
        // Vì bảng contracts không còn owner_id/contractor_id, ta phải query qua relation 'project'
        $query = Contract::with(['project', 'project.contractor']) // Eager load để tránh N+1
            ->whereHas('project', function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhere('contractor_id', $user->id);
            });

        // 2. CÁC BỘ LỌC

        // Lọc theo trạng thái hợp đồng
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo Project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Lọc theo Nhà thầu (Query qua relationship project)
        if ($request->filled('contractor_id')) {
            $query->whereHas('project', function ($q) use ($request) {
                $q->where('contractor_id', $request->contractor_id);
            });
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('contract_number', 'like', "%{$search}%")
                  ->orWhere('contract_name', 'like', "%{$search}%")
                  ->orWhereHas('project', function ($pq) use ($search) {
                      $pq->where('project_name', 'like', "%{$search}%");
                  })
                  // Tìm theo tên nhà thầu (thông qua project -> contractor)
                  ->orWhereHas('project.contractor', function ($cq) use ($search) {
                      $cq->where('username', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Sắp xếp
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest': $query->orderBy('signed_date'); break;
            case 'value_asc': $query->orderBy('contract_value'); break;
            case 'value_desc': $query->orderByDesc('contract_value'); break;
            case 'due_soon': $query->orderBy('due_date'); break;
            default: $query->orderByDesc('created_at');
        }

        $contracts = $query->paginate(15)->withQueryString();

        // 3. DỮ LIỆU CHO BỘ LỌC UI
        // Lấy danh sách dự án của user
        $projects = Project::where(function($q) use ($user) {
            $q->where('owner_id', $user->id)
              ->orWhere('contractor_id', $user->id);
        })->get();

        // Lấy danh sách nhà thầu (nếu user là owner)
        $contractors = [];
        if ($user->user_type === 'owner') {
             $contractors = User::where('user_type', 'contractor')
                ->whereHas('contractedProjects', function ($q) use ($user) {
                    $q->where('owner_id', $user->id);
                })->get();
        }

        // 4. THỐNG KÊ (Sử dụng query builder để tối ưu)
        // Tạo base query thống kê
        $baseStatsQuery = Contract::whereHas('project', function ($q) use ($user) {
            $q->where('owner_id', $user->id)->orWhere('contractor_id', $user->id);
        });

        $activeCount = (clone $baseStatsQuery)->where('status', 'active')->count();
        $pendingCount = (clone $baseStatsQuery)->whereIn('status', ['draft', 'pending_signature'])->count();
        $totalValue = (clone $baseStatsQuery)->sum('contract_value');
        
        $overdueCount = (clone $baseStatsQuery)
            ->where('status', 'active')
            ->where('due_date', '<', now())
            ->count();

        return view('client.contracts.index', compact(
            'contracts',
            'projects',
            'contractors',
            'activeCount',
            'pendingCount',
            'totalValue',
            'overdueCount'
        ));
    }

    public function show(Contract $contract)
    {
        // Load quan hệ cần thiết
        // Lưu ý: Contract không còn trực tiếp owner/contractor, phải load qua project
        $contract->load(['project.owner', 'project.contractor', 'payments', 'approvals.approver']);

        $project = $contract->project;

        // BẢO MẬT: Kiểm tra quyền xem thông qua Project
        // User phải là Owner hoặc Contractor của Project đó
        if (auth()->id() !== $project->owner_id && auth()->id() !== $project->contractor_id) {
            abort(403, 'Bạn không có quyền truy cập hợp đồng này.');
        }

        // TÍNH TOÁN SỐ LIỆU (Sử dụng Accessor trong Model Contract)
        // total_paid và remaining_amount được tính động trong Model
        $totalPaid = $contract->total_paid; 
        $remaining = $contract->remaining_amount;
        
        $progress = 0;
        if ($contract->contract_value > 0) {
            $progress = ($totalPaid / $contract->contract_value) * 100;
        }

        return view('client.contracts.show', compact('contract', 'project', 'totalPaid', 'remaining', 'progress'));
    }
}