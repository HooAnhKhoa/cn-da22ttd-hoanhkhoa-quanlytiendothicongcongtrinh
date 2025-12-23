<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\Progress;
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
        
        // Dashboard khác nhau cho từng role
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
    
    private function contractorDashboard($user)
    {
        // Logic dashboard cho contractor
        $projects = Project::whereHas('contracts', function($query) use ($user) {
            $query->where('contractor_id', $user->id);
        })->get();

        $stats = [
            'total_projects' => $projects->count(),
            'active_projects' => $projects->where('status', 'in_progress')->count(),
            'completed_projects' => $projects->where('status', 'completed')->count(),
        ];

        $taskStats = $this->getContractorTaskStats($user->id);
        $paymentStats = $this->getContractorPaymentStats($user->id);
        $materialStats = $this->getContractorMaterialStats($user->id);
        
        $recentProgress = Progress::whereHas('task', function($query) use ($user) {
                $query->whereHas('site.project.contracts', function($subQuery) use ($user) {
                    $subQuery->where('contractor_id', $user->id);
                });
            })
            ->with(['task.site.project'])
            ->orderBy('created_at', 'desc')
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
    
    private function ownerDashboard($user)
    {
        // Logic dashboard cho owner
        $projects = Project::where('owner_id', $user->id)->get();
        
        $stats = [
            'total_projects' => $projects->count(),
            'active_projects' => $projects->where('status', 'in_progress')->count(),
            'total_investment' => $projects->sum('total_budget'),
            'completed_projects' => $projects->where('status', 'completed')->count(),
        ];
        
        $financialStats = $this->getOwnerFinancialStats($user->id);
        $contractStats = $this->getOwnerContractStats($user->id);
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
    
    private function engineerDashboard($user)
    {
        // Logic dashboard cho engineer
        $tasks = Task::where('assigned_to', $user->id)->get();
        
        $stats = [
            'total_tasks' => $tasks->count(),
            'completed_tasks' => $tasks->where('status', 'completed')->count(),
            'in_progress_tasks' => $tasks->where('status', 'in_progress')->count(),
            'overdue_tasks' => $tasks->where('status', '!=', 'completed')
                ->where('due_date', '<', now())->count(),
        ];
        
        $taskStats = $this->getEngineerTaskStats($user->id);
        $recentProgress = $this->getEngineerRecentProgress($user->id);
        $upcomingTasks = $tasks->where('status', '!=', 'completed')
            ->where('due_date', '>=', now())
            ->sortBy('due_date')
            ->take(5);
        
        return view('client.dashboard.engineer', compact(
            'stats',
            'taskStats',
            'recentProgress',
            'upcomingTasks'
        ));
    }
    
    private function getContractorTaskStats($contractorId)
    {
        // Logic cho contractor task stats
        $tasks = Task::whereHas('site.project.contracts', function($query) use ($contractorId) {
                $query->where('contractor_id', $contractorId);
            })
            ->select('status', 'due_date')
            ->get();

        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('status', 'completed')->count();
        
        $overdueTasks = $tasks->filter(function($task) {
            return $task->status !== 'completed' && 
                   $task->due_date && 
                   $task->due_date->isPast();
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
        $contracts = Contract::where('contractor_id', $contractorId)
            ->select('contract_value', 'total_paid', 'remaining_amount')
            ->get();

        $totalContractValue = $contracts->sum('contract_value');
        $totalPaid = $contracts->sum('total_paid');
        $totalRemaining = $contracts->sum('remaining_amount');

        return [
            'total_contract_value' => $totalContractValue,
            'total_paid' => $totalPaid,
            'total_remaining' => $totalRemaining,
            'paid_percent' => $totalContractValue > 0 ? round(($totalPaid / $totalContractValue) * 100) : 0,
        ];
    }
    
    private function getContractorMaterialStats($contractorId)
    {
        $materialUsage = MaterialUsage::whereHas('task.site.project.contracts', function($query) use ($contractorId) {
                $query->where('contractor_id', $contractorId);
            })
            ->with('material')
            ->select('material_id', DB::raw('SUM(quantity_used) as total_used'))
            ->groupBy('material_id')
            ->orderByDesc('total_used')
            ->take(5)
            ->get()
            ->map(function($usage) {
                $usage->name = $usage->material->name ?? 'Không xác định';
                $usage->unit = $usage->material->unit ?? 'đơn vị';
                $usage->percent = min(100, round(($usage->total_used / 1000) * 100)); // Giả định 1000 là max
                return $usage;
            });

        return [
            'top_materials' => $materialUsage,
            'total_materials' => $materialUsage->count(),
        ];
    }
    
    private function getOwnerFinancialStats($ownerId)
    {
        $projects = Project::where('owner_id', $ownerId)->with('contracts')->get();
        
        $totalBudget = $projects->sum('total_budget');
        $totalContracts = $projects->sum(function($project) {
            return $project->contracts->sum('contract_value');
        });
        $totalPaid = $projects->sum(function($project) {
            return $project->contracts->sum('total_paid');
        });
        
        return [
            'total_budget' => $totalBudget,
            'total_contracts' => $totalContracts,
            'total_paid' => $totalPaid,
            'remaining' => $totalContracts - $totalPaid,
            'utilization_percent' => $totalBudget > 0 ? round(($totalContracts / $totalBudget) * 100) : 0,
        ];
    }
    
    private function getOwnerContractStats($ownerId)
    {
        $contracts = Contract::whereHas('project', function($query) use ($ownerId) {
            $query->where('owner_id', $ownerId);
        })->get();

        $totalContracts = $contracts->count();
        $activeContracts = $contracts->where('status', 'active')->count();
        $completedContracts = $contracts->where('status', 'completed')->count();
        $pendingContracts = $contracts->where('status', 'pending_signature')->count();

        return [
            'total_contracts' => $totalContracts,
            'active_contracts' => $activeContracts,
            'completed_contracts' => $completedContracts,
            'pending_contracts' => $pendingContracts,
            'contract_value' => $contracts->sum('contract_value'),
        ];
    }
    
    private function getOwnerUpcomingPayments($ownerId)
    {
        // Lấy các hợp đồng còn nợ
        $pendingPayments = Contract::whereHas('project', function($query) use ($ownerId) {
                $query->where('owner_id', $ownerId);
            })
            ->where('remaining_amount', '>', 0)
            ->whereIn('status', ['active', 'pending_signature'])
            ->with(['contractor', 'project'])
            ->orderBy('due_date')
            ->take(5)
            ->get()
            ->map(function($contract) {
                return [
                    'contract' => $contract->contract_number ?? 'HĐ-' . $contract->id,
                    'contractor' => $contract->contractor->name ?? 'N/A',
                    'project' => $contract->project->project_name ?? 'N/A',
                    'amount' => $contract->remaining_amount,
                    'due_date' => $contract->due_date,
                    'days_left' => $contract->due_date ? now()->diffInDays($contract->due_date, false) : null,
                ];
            });

        return $pendingPayments;
    }
    
    private function getEngineerTaskStats($engineerId)
    {
        $tasks = Task::where('assigned_to', $engineerId)->get();
        
        $byPriority = [
            'high' => $tasks->where('priority', 'high')->count(),
            'medium' => $tasks->where('priority', 'medium')->count(),
            'low' => $tasks->where('priority', 'low')->count(),
        ];
        
        $byStatus = [
            'completed' => $tasks->where('status', 'completed')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'pending' => $tasks->where('status', 'pending')->count(),
        ];
        
        return [
            'by_priority' => $byPriority,
            'by_status' => $byStatus,
        ];
    }
    
    private function getEngineerRecentProgress($engineerId)
    {
        return Progress::whereHas('task', function($query) use ($engineerId) {
                $query->where('assigned_to', $engineerId);
            })
            ->with(['task.site.project'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }
}