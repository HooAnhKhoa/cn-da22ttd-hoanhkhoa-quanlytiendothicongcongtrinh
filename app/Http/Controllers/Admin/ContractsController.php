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
        $query = Contract::with(['project', 'contractor', 'owner']);

        // Lọc theo trạng thái
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

        // Lọc theo chủ đầu tư
        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('contract_number', 'like', "%{$search}%")
                  ->orWhere('contract_name', 'like', "%{$search}%")
                  ->orWhereHas('project', function($q) use ($search) {
                      $q->where('project_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('contractor', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('owner', function($q) use ($search) {
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
            case 'newest':
            default:
                $query->orderByDesc('created_at');
        }

        $contracts = $query->paginate(15);
        $projects = Project::all();
        $contractors = User::where('user_type', 'contractor')->get();
        $owners = User::where('user_type', 'owner')->get();

        // Tính toán thống kê
        $activeCount = Contract::where('status', 'active')->count();
        $pendingCount = Contract::where('status', 'draft')->orWhere('status', 'pending_signature')->count();
        $totalValue = Contract::sum('contract_value');
        $overdueCount = Contract::where('status', 'active')
            ->where('due_date', '<', now())
            ->count();

        return view('admin.contracts.index', compact(
            'contracts', 
            'projects', 
            'contractors',
            'owners',
            'activeCount',
            'pendingCount',
            'totalValue',
            'overdueCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $projects = Project::all();
        $contractors = User::where('user_type', 'contractor')->get();
        $owners = User::where('user_type', 'owner')->get();
        $statuses = self::getStatuses();
        $nextId = (\App\Models\Contract::max('id') ?? 0) + 1;
        $autoContractNumber = 'HD-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        $selectedProjectId = $request->query('project_id');
        $paymentStatuses = self::getPaymentStatuses();

        return view('admin.contracts.create', compact('projects', 'contractors', 'owners', 'statuses', 'paymentStatuses', 'selectedProjectId', 'autoContractNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'owner_id' => 'required|exists:users,id',
            'contractor_id' => 'required|exists:users,id',
            'contract_value' => 'required|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'signed_date' => 'required|date',
            'due_date' => 'required|date|after:signed_date',
            'status' => 'required|in:draft,pending_signature,active,completed,terminated,on_hold,expired',
            'payment_status' => 'required|in:unpaid,partially_paid,fully_paid,overdue,refunded',
            'contract_number' => 'nullable|string|unique:contracts,contract_number',
            'contract_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'terms' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Auto-calculate total_paid if advance_payment is provided
            if (isset($validated['advance_payment']) && $validated['advance_payment'] > 0) {
                $validated['total_paid'] = $validated['advance_payment'];
            } else {
                $validated['total_paid'] = 0;
            }

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
        $contract->load(['project', 'owner', 'contractor', 'payments']);
        
        $totalPaid = $contract->total_paid;
        $remaining = $contract->remaining_amount;
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
        $owners = User::where('user_type', 'owner')->get();
        $statuses = self::getStatuses();
        $paymentStatuses = self::getPaymentStatuses();

        return view('admin.contracts.edit', compact('contract', 'projects', 'contractors', 'owners', 'statuses', 'paymentStatuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'owner_id' => 'required|exists:users,id',
            'contractor_id' => 'required|exists:users,id',
            'contract_value' => 'required|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'signed_date' => 'required|date',
            'due_date' => 'required|date|after:signed_date',
            'status' => 'required|in:draft,pending_signature,active,completed,terminated,on_hold,expired',
            'payment_status' => 'required|in:unpaid,partially_paid,fully_paid,overdue,refunded',
            'contract_number' => 'nullable|string|unique:contracts,contract_number,' . $contract->id,
            'contract_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'terms' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update total_paid if advance_payment changes
            if (isset($validated['advance_payment']) && $validated['advance_payment'] != $contract->advance_payment) {
                $validated['total_paid'] = $validated['advance_payment'] + ($contract->total_paid - $contract->advance_payment);
            }

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
}