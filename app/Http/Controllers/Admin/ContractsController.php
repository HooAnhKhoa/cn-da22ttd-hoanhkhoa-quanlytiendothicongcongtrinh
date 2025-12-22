<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractsController extends Controller
{
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_SUSPENDED = 'suspended';

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Chờ xử lý',
            self::STATUS_ACTIVE => 'Đang hoạt động',
            self::STATUS_COMPLETED => 'Đã hoàn thành',
            self::STATUS_CANCELLED => 'Đã hủy',
            self::STATUS_SUSPENDED => 'Tạm dừng'
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Contract::with(['project', 'contractor']);

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo dự án
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Lọc theo nhà thầu
        if ($request->filled('contractor_id')) {
            $query->where('contractor_id', $request->contractor_id);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('project', function($q) use ($search) {
                    $q->where('project_name', 'like', "%{$search}%");
                })->orWhereHas('contractor', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Sắp xếp
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('signed_date');
                break;
            case 'value_asc':
                $query->orderBy('contract_value');
                break;
            case 'value_desc':
                $query->orderByDesc('contract_value');
                break;
            case 'due_soon':
                $query->orderBy('due_date');
                break;
            default:
                $query->orderByDesc('signed_date');
        }

        $contracts = $query->paginate(15);
        $projects = Project::all();
        $contractors = User::where('user_type', 'contractor')->get();

        // Tính toán thống kê
        $activeCount = Contract::where('status', 'active')->count();
        $pendingCount = Contract::where('status', 'pending')->count();
        $totalValue = Contract::sum('contract_value');
        $overdueCount = Contract::where('status', 'active')
            ->where('due_date', '<', now())
            ->count();

        return view('admin.contracts.index', compact(
            'contracts', 
            'projects', 
            'contractors',
            'activeCount',
            'pendingCount',
            'totalValue',
            'overdueCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::where('status', 'in_progress')->get();
        $contractors = User::where('user_type', 'contractor')->get();
        $statuses = self::getStatuses();

        return view('admin.contracts.create', compact('projects', 'contractors', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'contractor_id' => 'required|exists:users,id',
            'contract_value' => 'required|numeric|min:0',
            'signed_date' => 'required|date',
            'due_date' => 'required|date|after:signed_date',
            'status' => 'required|in:pending,active,completed,cancelled,suspended',
            'description' => 'nullable|string|max:1000',
            'terms' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            Contract::create($validated);
            DB::commit();

            return redirect()->route('admin.contracts.index')
                ->with('success', 'Hợp đồng đã được tạo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contract $contract)
    {
        $contract->load(['project', 'contractor', 'payments']);
        
        $totalPaid = $contract->payments->sum('amount');
        $remaining = $contract->contract_value - $totalPaid;
        $progress = $contract->contract_value > 0 ? ($totalPaid / $contract->contract_value) * 100 : 0;

        return view('admin.contracts.show', compact('contract', 'totalPaid', 'remaining', 'progress'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contract $contract)
    {
        $projects = Project::where('status', 'in_progress')->get();
        $contractors = User::where('user_type', 'contractor')->get();
        $statuses = self::getStatuses();

        return view('admin.contracts.edit', compact('contract', 'projects', 'contractors', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'contractor_id' => 'required|exists:users,id',
            'contract_value' => 'required|numeric|min:0',
            'signed_date' => 'required|date',
            'due_date' => 'required|date|after:signed_date',
            'status' => 'required|in:pending,active,completed,cancelled,suspended',
            'description' => 'nullable|string|max:1000',
            'terms' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $contract->update($validated);
            DB::commit();

            return redirect()->route('admin.contracts.show', $contract)
                ->with('success', 'Hợp đồng đã được cập nhật thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contract $contract)
    {
        DB::beginTransaction();
        try {
            $contract->delete();
            DB::commit();

            return redirect()->route('admin.contracts.index')
                ->with('success', 'Hợp đồng đã được xóa thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Client view - Danh sách hợp đồng của client
     */
    public function clientIndex(Request $request)
    {
        $user = auth()->user();
        
        $query = Contract::with(['project', 'contractor'])
            ->where('contractor_id', $user->id);

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('project', function($q) use ($search) {
                    $q->where('project_name', 'like', "%{$search}%");
                });
            });
        }

        $contracts = $query->paginate(10);

        return view('client.contracts.index', compact('contracts'));
    }

    /**
     * Client view - Xem chi tiết hợp đồng
     */
    public function clientShow(Contract $contract)
    {
        // Kiểm tra quyền truy cập
        if (auth()->user()->id !== $contract->contractor_id) {
            abort(403, 'Unauthorized');
        }

        $contract->load(['project', 'contractor', 'payments']);
        
        $totalPaid = $contract->payments->sum('amount');
        $remaining = $contract->contract_value - $totalPaid;
        $progress = $contract->contract_value > 0 ? ($totalPaid / $contract->contract_value) * 100 : 0;

        return view('client.contracts.show', compact('contract', 'totalPaid', 'remaining', 'progress'));
    }
}