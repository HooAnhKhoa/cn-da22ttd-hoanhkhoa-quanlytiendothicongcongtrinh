<?php

namespace App\Http\Controllers\Client;

use App\Models\User;
use App\Models\Project;
use App\Models\Contract;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

    const PAYMENT_STATUS_UNPAID = 'unpaid';
    const PAYMENT_STATUS_PARTIALLY_PAID = 'partially_paid';
    const PAYMENT_STATUS_FULLY_PAID = 'fully_paid';
    const PAYMENT_STATUS_OVERDUE = 'overdue';
    const PAYMENT_STATUS_REFUNDED = 'refunded';

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

    public static function getPaymentStatuses()
    {
        return [
            self::PAYMENT_STATUS_UNPAID => 'Chưa thanh toán',
            self::PAYMENT_STATUS_PARTIALLY_PAID => 'Thanh toán một phần',
            self::PAYMENT_STATUS_FULLY_PAID => 'Đã thanh toán đủ',
            self::PAYMENT_STATUS_OVERDUE => 'Quá hạn thanh toán',
            self::PAYMENT_STATUS_REFUNDED => 'Đã hoàn tiền',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {   
        $user = Auth::user();
        
        // 1. Sửa từ Project:: sang Contract:: và lọc theo owner_id của Hợp đồng
        $query = Contract::where(function($q) use ($user) {
            $q->where('owner_id', $user->id)
            ->orWhere('contractor_id', $user->id);
        });

        // Lọc theo trạng thái hợp đồng
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo trạng thái thanh toán
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Lọc theo dự án
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Lọc theo nhà thầu
        if ($request->filled('contractor_id')) {
            $query->where('contractor_id', $request->contractor_id);
        }

        // Tìm kiếm (giữ nguyên logic của bạn nhưng áp dụng trên $query của Contract)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('contract_number', 'like', "%{$search}%")
                ->orWhere('contract_name', 'like', "%{$search}%")
                ->orWhereHas('project', function($pq) use ($search) {
                    $pq->where('project_name', 'like', "%{$search}%");
                })
                ->orWhereHas('contractor', function($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%");
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

        // 2. Chỉ lấy danh sách dự án/đối tác liên quan đến Chủ đầu tư này để hiển thị trong bộ lọc
        $projects = Project::where('owner_id', $user->id)->get();
        $contractors = User::where('user_type', 'contractor')
            ->whereHas('contractedProjects', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            })->get();

        // 3. Tính toán thống kê - CẦN LỌC THEO OWNER_ID
        $statsQuery = Contract::where('owner_id', $user->id);
        
        $activeCount = (clone $statsQuery)->where('status', 'active')->count();
        $pendingCount = (clone $statsQuery)->whereIn('status', ['draft', 'pending_signature'])->count();
        $totalValue = (clone $statsQuery)->sum('contract_value');
        $overdueCount = (clone $statsQuery)->where('status', 'active')
            ->where('due_date', '<', now())
            ->count();

        // Lưu ý: Nếu dành cho Client, hãy đổi path view thành 'client.contracts.index'
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
        // Bảo mật: Nếu không phải chủ hợp đồng thì không cho xem
        if (auth()->id() !== $contract->contractor_id && auth()->id() !== $contract->owner_id) {
            abort(403);
        }

        $contract->load(['project', 'owner', 'payments', 'approvals.approver']);
        
        $totalPaid = $contract->total_paid;
        $remaining = $contract->remaining_amount;
        $progress = $contract->contract_value > 0 ? ($totalPaid / $contract->contract_value) * 100 : 0;

        return view('client.contracts.show', compact('contract', 'totalPaid', 'remaining', 'progress'));
    }
}