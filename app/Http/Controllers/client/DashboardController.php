<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\ProgressUpdate;
use App\Models\MaterialUsage;
use App\Models\Contract;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            ->orderBy('date', 'desc')
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
        
        // Tính tổng ngân sách từ các Contracts
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
    // Phương án 1: Tìm tasks qua assigned_engineer_id (cột có nhưng null)
    $tasks = Task::where('assigned_engineer_id', $user->id)->get();
    
    // Nếu không có tasks qua assigned_engineer_id, thử tìm qua project
    if ($tasks->count() === 0) {
        // Phương án 2: Tìm qua project's engineer_id
        $tasks = Task::whereHas('site.project', function($query) use ($user) {
                $query->where('engineer_id', $user->id);
            })
            ->get();
            
        \Log::info('Engineer tasks via project:', [
            'count' => $tasks->count(),
            'user_id' => $user->id,
            'username' => $user->username
        ]);
    }
    
    // Debug thông tin
    \Log::info('Engineer Dashboard Data:', [
        'user_id' => $user->id,
        'user_type' => $user->user_type,
        'total_tasks_direct' => Task::where('assigned_engineer_id', $user->id)->count(),
        'total_tasks_via_project' => Task::whereHas('site.project', function($q) use ($user) {
            $q->where('engineer_id', $user->id);
        })->count(),
        'projects_as_engineer' => Project::where('engineer_id', $user->id)->count()
    ]);
    
    // Tính toán các chỉ số chi tiết
    $totalTasks = $tasks->count();
    $completedTasks = $tasks->where('status', 'completed')->count();
    $inProgressTasks = $tasks->where('status', 'in_progress')->count();
    
    // Tính tasks trễ hạn
    $overdueTasks = $tasks->filter(function($task) {
        return $task->status !== 'completed' && 
               $task->end_date && 
               $task->end_date < now();
    })->count();
    
    // Tính tasks sắp đến hạn (trong 7 ngày tới)
    $upcomingDeadlineTasks = $tasks->filter(function($task) {
        return $task->status !== 'completed' && 
               $task->end_date && 
               $task->end_date >= now() && 
               $task->end_date <= now()->addDays(7);
    })->count();
    
    $stats = [
        'total_tasks' => $totalTasks,
        'completed_tasks' => $completedTasks,
        'in_progress_tasks' => $inProgressTasks,
        'overdue_tasks' => $overdueTasks,
        'upcoming_deadline_tasks' => $upcomingDeadlineTasks,
        'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
    ];
    
    $taskStats = $this->getEngineerTaskStats($tasks);
    $recentProgress = $this->getEngineerRecentProgress($user->id);
    
    // Lấy tasks sắp đến hạn (5 tasks)
    $upcomingTasks = $tasks->where('status', '!=', 'completed')
        ->where('end_date', '>=', now())
        ->sortBy('end_date')
        ->take(5);
    
    // Lấy các project mà engineer đang làm việc
    $projects = Project::where('engineer_id', $user->id)->get();
    
    return view('client.dashboard.engineer', compact(
        'stats',
        'taskStats',
        'recentProgress',
        'upcomingTasks',
        'projects'
    ));
}
    
    // ==========================================
    // HELPER FUNCTIONS
    // ==========================================

    private function getContractorTaskStats($contractorId)
    {
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
        $contracts = Contract::whereHas('project', function($q) use ($contractorId) {
            $q->where('contractor_id', $contractorId);
        })->with(['payments' => function($q) {
            $q->where('status', 'completed');
        }])->get();

        $totalContractValue = $contracts->sum('contract_value');
        
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
            ->select('material_id', DB::raw('SUM(quantity) as total_used'))
            ->groupBy('material_id')
            ->orderByDesc('total_used')
            ->take(5)
            ->get()
            ->map(function($usage) {
                $usage->name = $usage->material->materials_name ?? 'Không xác định';
                $usage->unit = $usage->material->unit ?? '';
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
            'total_budget' => $totalContractsValue,
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
        $contracts = Contract::whereHas('project', function($query) use ($ownerId) {
                $query->where('owner_id', $ownerId);
            })
            ->whereIn('status', ['active', 'pending_signature'])
            ->with(['project', 'payments'])
            ->get();

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
                'contractor' => $contract->project->contractor->username ?? 'N/A',
                'project' => $contract->project->project_name ?? 'N/A',
                'amount' => $contract->calculated_remaining,
                'due_date' => $contract->due_date,
                'days_left' => $contract->due_date ? now()->diffInDays($contract->due_date, false) : null,
            ];
        });
    }
    
    private function getEngineerTaskStats($tasks)
    {
        $totalTasks = $tasks->count();
        
        $byStatus = [
            'completed' => [
                'count' => $tasks->where('status', 'completed')->count(),
                'percentage' => $totalTasks > 0 ? round(($tasks->where('status', 'completed')->count() / $totalTasks) * 100) : 0,
                'color' => 'green'
            ],
            'in_progress' => [
                'count' => $tasks->where('status', 'in_progress')->count(),
                'percentage' => $totalTasks > 0 ? round(($tasks->where('status', 'in_progress')->count() / $totalTasks) * 100) : 0,
                'color' => 'blue'
            ],
            'planned' => [
                'count' => $tasks->where('status', 'planned')->count(),
                'percentage' => $totalTasks > 0 ? round(($tasks->where('status', 'planned')->count() / $totalTasks) * 100) : 0,
                'color' => 'gray'
            ],
            'pending_review' => [
                'count' => $tasks->where('status', 'pending_review')->count(),
                'percentage' => $totalTasks > 0 ? round(($tasks->where('status', 'pending_review')->count() / $totalTasks) * 100) : 0,
                'color' => 'purple'
            ],
        ];
        
        // Thống kê theo site/project
        $bySite = $tasks->groupBy('site_id')->map(function($siteTasks, $siteId) {
            return [
                'count' => $siteTasks->count(),
                'site_name' => $siteTasks->first()->site->site_name ?? 'N/A',
                'project_name' => $siteTasks->first()->site->project->project_name ?? 'N/A',
            ];
        })->sortByDesc('count')->take(5);
        
        return [
            'by_status' => $byStatus,
            'by_site' => $bySite,
            'total_sites' => $bySite->count(),
        ];
    }
    
    // THÊM METHOD BỊ THIẾU
    private function getEngineerRecentProgress($engineerId)
{
    // Thử cả 2 cách để lấy progress
    $progress = ProgressUpdate::whereHas('task', function($query) use ($engineerId) {
            $query->where('assigned_engineer_id', $engineerId);
        })
        ->with(['task.site.project'])
        ->orderBy('date', 'desc')
        ->take(5)
        ->get();
    
    // Nếu không có progress qua assigned_engineer_id, thử qua project
    if ($progress->count() === 0) {
        $progress = ProgressUpdate::whereHas('task.site.project', function($query) use ($engineerId) {
                $query->where('engineer_id', $engineerId);
            })
            ->with(['task.site.project'])
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();
    }
    
    return $progress;
}
}