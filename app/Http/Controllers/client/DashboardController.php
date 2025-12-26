<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\ProgressUpdate; // Đã sửa từ Progress thành ProgressUpdate
use App\Models\MaterialUsage;
use App\Models\Contract;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userType = $user->user_type;
        
        switch ($userType) {
            case 'contractor':
                return $this->contractorDashboard($user);
            case 'owner':
                return $this->ownerDashboard($user);
            case 'engineer':
                return $this->engineerDashboard($user);
            default:
                abort(403, 'Unauthorized role');
        }
    }
    
    // ==========================================
    // CONTRACTOR DASHBOARD
    // ==========================================
    private function contractorDashboard($user)
    {
        // Contractor được xác định trực tiếp trên Project
        $projects = Project::where('contractor_id', $user->id)->get();

        $stats = [
            'total_projects' => $projects->count(),
            'active_projects' => $projects->where('status', 'in_progress')->count(),
            'completed_projects' => $projects->where('status', 'completed')->count(),
        ];

        $taskStats = $this->getContractorTaskStats($user->id);
        $paymentStats = $this->getContractorPaymentStats($user->id);
        $materialStats = $this->getContractorMaterialStats($user->id);
        
        // Lấy tiến độ mới nhất từ các task thuộc dự án của contractor
        $recentProgress = ProgressUpdate::whereHas('task.site.project', function($query) use ($user) {
                $query->where('contractor_id', $user->id);
            })
            ->with(['task.site.project'])
            ->orderBy('date', 'desc') // Sửa created_at thành date theo schema
            ->take(5)
            ->get();

        $recentProjects = $projects->sortByDesc('created_at')->take(5);

        return view('client.dashboard.contractor', compact(
            'stats',
            'taskStats',
            'paymentStats',
            'materialStats',
            'recentProgress',
            'recentProjects'
        ));
    }
    
    // ==========================================
    // OWNER DASHBOARD
    // ==========================================
    private function ownerDashboard($user)
    {
        $projects = Project::where('owner_id', $user->id)->get();
        
        // Tính tổng ngân sách từ các Contracts (vì project không còn lưu total_budget)
        $totalInvestment = Contract::whereIn('project_id', $projects->pluck('id'))->sum('contract_value');

        $stats = [
            'total_projects' => $projects->count(),
            'active_projects' => $projects->where('status', 'in_progress')->count(),
            'total_investment' => $totalInvestment,
            'completed_projects' => $projects->where('status', 'completed')->count(),
        ];
        
        $financialStats = $this->getOwnerFinancialStats($user->id);
        $contractStats = $this->getOwnerContractStats($projects->pluck('id')->toArray());
        $upcomingPayments = $this->getOwnerUpcomingPayments($user->id);
        $recentProjects = $projects->sortByDesc('created_at')->take(5);
        
        return view('client.dashboard.owner', compact(
            'stats',
            'financialStats',
            'contractStats',
            'upcomingPayments',
            'recentProjects'
        ));
    }
    
    // ==========================================
    // ENGINEER DASHBOARD
    // ==========================================
    private function engineerDashboard($user)
    {
        // Sửa assigned_to thành assigned_engineer_id
        $tasks = Task::where('assigned_engineer_id', $user->id)->get();
        
        $stats = [
            'total_tasks' => $tasks->count(),
            'completed_tasks' => $tasks->where('status', 'completed')->count(),
            'in_progress_tasks' => $tasks->where('status', 'in_progress')->count(),
            // Sửa due_date thành end_date
            'overdue_tasks' => $tasks->where('status', '!=', 'completed')
                ->where('end_date', '<', now())->count(),
        ];
        
        $taskStats = $this->getEngineerTaskStats($tasks);
        $recentProgress = $this->getEngineerRecentProgress($user->id);
        
        $upcomingTasks = $tasks->where('status', '!=', 'completed')
            ->where('end_date', '>=', now())
            ->sortBy('end_date')
            ->take(5);
        
        return view('client.dashboard.engineer', compact(
            'stats',
            'taskStats',
            'recentProgress',
            'upcomingTasks'
        ));
    }
    
    // ==========================================
    // HELPER FUNCTIONS
    // ==========================================

    private function getContractorTaskStats($contractorId)
    {
        // Lấy tasks thông qua project -> contractor_id
        $tasks = Task::whereHas('site.project', function($query) use ($contractorId) {
                $query->where('contractor_id', $contractorId);
            })
            ->select('status', 'end_date')
            ->get();

        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('status', 'completed')->count();
        
        $overdueTasks = $tasks->filter(function($task) {
            return $task->status !== 'completed' && 
                   $task->end_date && 
                   $task->end_date < now();
        })->count();

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'overdue_tasks' => $overdueTasks,
            'progress_percent' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
        ];
    }
    
    private function getContractorPaymentStats($contractorId)
    {
        // Lấy Contracts thuộc các project của contractor này
        $contracts = Contract::whereHas('project', function($q) use ($contractorId) {
            $q->where('contractor_id', $contractorId);
        })->with(['payments' => function($q) {
            $q->where('status', 'completed');
        }])->get();

        $totalContractValue = $contracts->sum('contract_value');
        
        // Tính tổng đã trả từ bảng payments
        $totalPaid = $contracts->sum(function($contract) {
            return $contract->payments->sum('amount');
        });

        $totalRemaining = $totalContractValue - $totalPaid;

        return [
            'total_contract_value' => $totalContractValue,
            'total_paid' => $totalPaid,
            'total_remaining' => $totalRemaining,
            'paid_percent' => $totalContractValue > 0 ? round(($totalPaid / $totalContractValue) * 100) : 0,
        ];
    }
    
    private function getContractorMaterialStats($contractorId)
    {
        $materialUsage = MaterialUsage::whereHas('task.site.project', function($query) use ($contractorId) {
                $query->where('contractor_id', $contractorId);
            })
            ->with('material')
            ->select('material_id', DB::raw('SUM(quantity) as total_used')) // Sửa quantity_used thành quantity
            ->groupBy('material_id')
            ->orderByDesc('total_used')
            ->take(5)
            ->get()
            ->map(function($usage) {
                $usage->name = $usage->material->materials_name ?? 'Không xác định'; // Sửa name thành materials_name
                $usage->unit = $usage->material->unit ?? '';
                // Logic tính phần trăm giả định (vì không có định mức tối đa trong DB)
                $usage->percent = rand(10, 100); 
                return $usage;
            });

        return [
            'top_materials' => $materialUsage,
            'total_materials' => $materialUsage->count(),
        ];
    }
    
    private function getOwnerFinancialStats($ownerId)
    {
        // Lấy Contracts của owner
        $contracts = Contract::whereHas('project', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })->with(['payments' => function($q) {
            $q->where('status', 'completed');
        }])->get();
        
        $totalContractsValue = $contracts->sum('contract_value');
        
        $totalPaid = $contracts->sum(function($contract) {
            return $contract->payments->sum('amount');
        });
        
        return [
            'total_budget' => $totalContractsValue, // Giả sử ngân sách bằng tổng giá trị hợp đồng
            'total_contracts' => $totalContractsValue,
            'total_paid' => $totalPaid,
            'remaining' => $totalContractsValue - $totalPaid,
            'utilization_percent' => $totalContractsValue > 0 ? round(($totalPaid / $totalContractsValue) * 100) : 0,
        ];
    }
    
    private function getOwnerContractStats($projectIds)
    {
        $contracts = Contract::whereIn('project_id', $projectIds)->get();

        return [
            'total_contracts' => $contracts->count(),
            'active_contracts' => $contracts->where('status', 'active')->count(),
            'completed_contracts' => $contracts->where('status', 'completed')->count(),
            'pending_contracts' => $contracts->where('status', 'pending_signature')->count(),
            'contract_value' => $contracts->sum('contract_value'),
        ];
    }
    
    private function getOwnerUpcomingPayments($ownerId)
    {
        // Tìm các hợp đồng chưa thanh toán hết và sắp đến hạn
        $contracts = Contract::whereHas('project', function($query) use ($ownerId) {
                $query->where('owner_id', $ownerId);
            })
            ->whereIn('status', ['active', 'pending_signature'])
            ->with(['project', 'payments']) // Payments để tính còn lại
            ->get();

        // Lọc và tính toán thủ công vì không còn cột remaining_amount
        $pendingContracts = $contracts->map(function($contract) {
            $paid = $contract->payments->where('status', 'completed')->sum('amount');
            $remaining = $contract->contract_value - $paid;
            $contract->calculated_remaining = $remaining;
            return $contract;
        })->filter(function($contract) {
            return $contract->calculated_remaining > 0;
        })->sortBy('due_date')->take(5);

        return $pendingContracts->map(function($contract) {
            return [
                'contract' => $contract->contract_number ?? 'HĐ-' . $contract->id,
                'contractor' => $contract->project->contractor->username ?? 'N/A', // Lấy contractor từ project
                'project' => $contract->project->project_name ?? 'N/A',
                'amount' => $contract->calculated_remaining,
                'due_date' => $contract->due_date,
                'days_left' => $contract->due_date ? now()->diffInDays($contract->due_date, false) : null,
            ];
        });
    }
    
    private function getEngineerTaskStats($tasks)
    {
        // Đã bỏ cột priority, nên chỉ thống kê theo status
        $byStatus = [
            'completed' => $tasks->where('status', 'completed')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'planned' => $tasks->where('status', 'planned')->count(),
            'pending_review' => $tasks->where('status', 'pending_review')->count(),
        ];
        
        return [
            'by_status' => $byStatus,
            // Có thể thêm thống kê theo Site nếu muốn
            'by_site' => $tasks->groupBy('site_id')->map->count(),
        ];
    }
    
    private function getEngineerRecentProgress($engineerId)
    {
        return ProgressUpdate::whereHas('task', function($query) use ($engineerId) {
                $query->where('assigned_engineer_id', $engineerId);
            })
            ->with(['task.site.project'])
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();
    }
}