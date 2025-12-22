<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;
use App\Models\Issue;
use App\Models\Payment;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\MaterialUsage;
use App\Models\ProgressUpdate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Trang chủ công cộng (chưa đăng nhập)
     */
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->user_type === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->user_type === 'client') {
                return redirect()->route('client.dashboard');
            }
        }

        // Nếu chưa đăng nhập, hiển thị trang chủ công cộng
        return view('welcome');
    }

    private function getRecentProgress()
    {
        return ProgressUpdate::with(['task.site.project', 'user'])
            ->select('*') // Lấy tất cả các trường
            ->latest()
            ->take(5)
            ->get();
    }

    /**
    * Dashboard cho Admin
    */
    public function adminDashboard()
    {
        // 1. Thống kê cơ bản
        $stats = [
            'total_projects' => Project::count(),
            'total_tasks' => Task::count(),
            'total_users' => User::count(),
            'active_projects' => Project::where('status', 'in_progress')->count(),
            'overdue_tasks' => Task::where('end_date', '<', now())
                                ->whereNotIn('status', ['completed', 'cancelled'])->count(),
        ];

        // 2. Dữ liệu biểu đồ Thanh toán
        $paymentChartData = Payment::select(
            DB::raw('DATE_FORMAT(pay_date, "%m/%Y") as month'),
            DB::raw('SUM(amount) as total')
        )
        ->groupBy('month')
        ->orderBy('pay_date', 'asc')
        ->get();

        // 3. Lấy danh sách gần đây
        $recentProjects = Project::orderBy('id', 'desc')->take(5)->get();
        
        $recentPayments = Payment::with('contract.project')
            ->orderBy('pay_date', 'desc')
            ->take(5)
            ->get();

        // 4. Lấy thông tin tiến độ công việc gần đây
        $recentProgress = $this->getRecentProgress();

        // 5. Thống kê trạng thái dự án cho biểu đồ
        $projectStatusStats = Project::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')->toArray();

        // QUAN TRỌNG: Truyền đầy đủ các biến vào compact()
        return view('admin.dashboard', compact(
            'stats',
            'paymentChartData',
            'recentProjects',
            'recentPayments',
            'recentProgress',
            'projectStatusStats'
        ));
    }

    /**
     * Dashboard cho Client
     */
    public function clientDashboard()
    {
        $userId = Auth::id();
        
        // Thống kê dự án của client
        $clientProjects = Project::where('owner_id', $userId)
            ->orWhere('contractor_id', $userId)
            ->orWhere('engineer_id', $userId)
            ->get();

        $stats = [
            'total_projects' => $clientProjects->count(),
            'active_projects' => $clientProjects->where('status', 'in_progress')->count(),
            'completed_projects' => $clientProjects->where('status', 'completed')->count(),
            'on_hold_projects' => $clientProjects->where('status', 'on_hold')->count(),
        ];

        // Lấy tất cả site của client
        $projectIds = $clientProjects->pluck('id');
        $clientSiteIds = \App\Models\Site::whereIn('project_id', $projectIds)->pluck('id');
        
        // Thống kê công việc
        $clientTasks = Task::whereIn('site_id', $clientSiteIds)->get();
        
        $taskStats = [
            'total_tasks' => $clientTasks->count(),
            'completed_tasks' => $clientTasks->where('status', 'completed')->count(),
            'in_progress_tasks' => $clientTasks->where('status', 'in_progress')->count(),
            'overdue_tasks' => $this->getClientOverdueTasksCount($clientSiteIds),
        ];

        // Dự án gần đây
        $recentProjects = $clientProjects->sortByDesc('created_at')->take(5);

        // Công việc gần đây
        $recentTasks = Task::whereIn('site_id', $clientSiteIds)
            ->with(['site.project'])
            ->latest()
            ->take(10)
            ->get();

        // Tiến độ gần đây
        $recentProgress = \App\Models\ProgressUpdate::whereIn('task_id', $clientTasks->pluck('id'))
            ->with(['task.site.project'])
            ->latest()
            ->take(5)
            ->get();

        // Vật liệu sử dụng cho dự án của client
        $materialStats = $this->getClientMaterialStats($projectIds, $clientSiteIds);

        // Thông tin thanh toán
        $paymentStats = $this->getClientPaymentStats($projectIds);

        return view('client.dashboard', compact(
            'stats',
            'taskStats',
            'recentProjects',
            'recentTasks',
            'recentProgress',
            'materialStats',
            'paymentStats'
        ));
    }

    /**
     * Lấy thống kê vật liệu cho Admin
     */
    private function getMaterialStats()
    {
        // Cache dữ liệu trong 10 phút (600 giây)
        return Cache::remember('admin_material_stats', 600, function () {
            $stats = [
                'total_quantity' => 0,
                'by_type' => [],
                'top_materials' => [],
                'by_project' => [],
                'monthly_usage' => [],
                'recent_usage' => [],
            ];

            try {
                // 1. Tính tổng vật liệu đã sử dụng
                $stats['total_quantity'] = MaterialUsage::sum('quantity');

                // 2. Phân bố theo loại
                $typeStats = MaterialUsage::selectRaw('materials.type, SUM(material_usages.quantity) as total_quantity')
                    ->join('materials', 'material_usages.material_id', '=', 'materials.id')
                    ->groupBy('materials.type')
                    ->get();

                foreach ($typeStats as $typeStat) {
                    $stats['by_type'][$typeStat->type] = (float) $typeStat->total_quantity;
                }

                // 3. Top 5 vật liệu sử dụng nhiều nhất
                $stats['top_materials'] = MaterialUsage::selectRaw('
                        materials.id,
                        materials.materials_name,
                        materials.type,
                        materials.supplier,
                        materials.unit,
                        SUM(material_usages.quantity) as total_quantity,
                        COUNT(material_usages.id) as usage_count
                    ')
                    ->join('materials', 'material_usages.material_id', '=', 'materials.id')
                    ->groupBy('materials.id', 'materials.materials_name', 'materials.type', 'materials.supplier', 'materials.unit')
                    ->orderByDesc('total_quantity')
                    ->limit(5)
                    ->get();

                // 4. Phân bố theo dự án
                $projectStats = MaterialUsage::selectRaw('
                        projects.project_name,
                        SUM(material_usages.quantity) as total_quantity
                    ')
                    ->join('tasks', 'material_usages.task_id', '=', 'tasks.id')
                    ->leftJoin('sites', 'tasks.site_id', '=', 'sites.id')
                    ->leftJoin('projects', 'sites.project_id', '=', 'projects.id')
                    ->whereNotNull('projects.id')
                    ->groupBy('projects.id', 'projects.project_name')
                    ->orderByDesc('total_quantity')
                    ->get();

                foreach ($projectStats as $projectStat) {
                    if ($projectStat->project_name) {
                        $stats['by_project'][$projectStat->project_name] = (float) $projectStat->total_quantity;
                    }
                }

                // 5. Thống kê vật liệu theo tháng (6 tháng gần đây)
                $monthlyStats = MaterialUsage::selectRaw('
                        DATE_FORMAT(material_usages.usage_date, "%Y-%m") as month,
                        SUM(material_usages.quantity) as total_quantity
                    ')
                    ->where('material_usages.usage_date', '>=', Carbon::now()->subMonths(6))
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();

                foreach ($monthlyStats as $monthlyStat) {
                    $stats['monthly_usage'][$monthlyStat->month] = (float) $monthlyStat->total_quantity;
                }

                // 6. Thống kê vật liệu gần đây (7 ngày)
                $stats['recent_usage'] = MaterialUsage::selectRaw('
                        materials.materials_name,
                        materials.type,
                        material_usages.quantity,
                        material_usages.usage_date,
                        tasks.task_name,
                        users.username as used_by
                    ')
                    ->join('materials', 'material_usages.material_id', '=', 'materials.id')
                    ->join('tasks', 'material_usages.task_id', '=', 'tasks.id')
                    ->leftJoin('users', 'material_usages.user_id', '=', 'users.id')
                    ->where('material_usages.usage_date', '>=', Carbon::now()->subDays(7))
                    ->orderBy('material_usages.usage_date', 'desc')
                    ->limit(10)
                    ->get();

            } catch (\Exception $e) {
                \Log::error('Error getting material stats with cache: ' . $e->getMessage());
            }

            return $stats;
        });
    }

    /**
     * Lấy thống kê vật liệu cho Client
     */
    private function getClientMaterialStats($projectIds, $siteIds)
    {
        $stats = [
            'total_quantity' => 0,
            'by_project' => [],
            'recent_usage' => [],
        ];

        try {
            // Tính tổng vật liệu đã sử dụng cho dự án của client
            $totalQuantity = MaterialUsage::whereIn('task_id', function($query) use ($siteIds) {
                $query->select('id')
                    ->from('tasks')
                    ->whereIn('site_id', $siteIds);
            })->sum('quantity');
            
            $stats['total_quantity'] = $totalQuantity;

            // Phân bố theo dự án
            $projectStats = MaterialUsage::selectRaw('
                    projects.project_name,
                    SUM(material_usages.quantity) as total_quantity
                ')
                ->join('tasks', 'material_usages.task_id', '=', 'tasks.id')
                ->leftJoin('sites', 'tasks.site_id', '=', 'sites.id')
                ->leftJoin('projects', 'sites.project_id', '=', 'projects.id')
                ->whereIn('projects.id', $projectIds)
                ->groupBy('projects.id', 'projects.project_name')
                ->orderByDesc('total_quantity')
                ->get();

            foreach ($projectStats as $projectStat) {
                if ($projectStat->project_name) {
                    $stats['by_project'][$projectStat->project_name] = (float) $projectStat->total_quantity;
                }
            }

            // Vật liệu gần đây
            $recentMaterialUsage = MaterialUsage::selectRaw('
                    materials.materials_name,
                    materials.type,
                    material_usages.quantity,
                    material_usages.usage_date,
                    tasks.task_name,
                    projects.project_name
                ')
                ->join('materials', 'material_usages.material_id', '=', 'materials.id')
                ->join('tasks', 'material_usages.task_id', '=', 'tasks.id')
                ->leftJoin('sites', 'tasks.site_id', '=', 'sites.id')
                ->leftJoin('projects', 'sites.project_id', '=', 'projects.id')
                ->whereIn('projects.id', $projectIds)
                ->where('material_usages.usage_date', '>=', Carbon::now()->subDays(7))
                ->orderBy('material_usages.usage_date', 'desc')
                ->limit(10)
                ->get();

            $stats['recent_usage'] = $recentMaterialUsage;

        } catch (\Exception $e) {
            \Log::error('Error getting client material stats: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Lấy thống kê thanh toán cho Client
     */
    private function getClientPaymentStats($projectIds)
    {
        $stats = [
            'total_paid' => 0,
            'total_due' => 0,
            'recent_payments' => [],
        ];

        try {
            // Kiểm tra nếu có model Payment
            if (class_exists('\App\Models\Payment')) {
                $payments = \App\Models\Payment::whereIn('project_id', $projectIds)->get();
                
                $stats['total_paid'] = $payments->where('status', 'paid')->sum('amount');
                $stats['total_due'] = $payments->where('status', 'pending')->sum('amount');
                
                $stats['recent_payments'] = \App\Models\Payment::whereIn('project_id', $projectIds)
                    ->latest()
                    ->take(5)
                    ->get();
            }

        } catch (\Exception $e) {
            \Log::error('Error getting payment stats: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Lấy số công việc trễ hạn (tất cả)
     */
    private function getOverdueTasksCount()
    {
        return Task::where('end_date', '<', Carbon::now())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();
    }

    /**
     * Lấy số công việc trễ hạn cho Client
     */
    private function getClientOverdueTasksCount($siteIds)
    {
        return Task::whereIn('site_id', $siteIds)
            ->where('end_date', '<', Carbon::now())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();
    }

    /**
     * Profile page (chung cho cả admin và client)
     */
    public function profile()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    /**
     * Cập nhật avatar
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = time() . '.' . $avatar->getClientOriginalExtension();
            $avatar->storeAs('avatars', $filename, 'public');
            
            $user->avatar = $filename;
            $user->save();
        }

        return back()->with('success', 'Avatar updated successfully.');
    }

    /**
     * Cập nhật profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user->update($request->only('name', 'email', 'phone', 'address'));

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = \Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }

    /**
     * API endpoint cho thống kê nhanh cho Admin
     */
    public function getAdminQuickStats()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'total_projects' => Project::count(),
                    'total_tasks' => Task::count(),
                    'total_clients' => User::where('role', 'client')->count(),
                    'active_projects' => Project::where('status', 'in_progress')->count(),
                    'overdue_tasks' => $this->getOverdueTasksCount(),
                    'total_material_used' => MaterialUsage::sum('quantity'),
                    'recent_tasks' => Task::where('created_at', '>=', Carbon::now()->subDays(7))->count(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching quick stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint cho thống kê nhanh cho Client
     */
    public function getClientQuickStats()
    {
        try {
            $userId = Auth::id();
            $clientProjects = Project::where('client_id', $userId)
                ->orWhere('owner_id', $userId)
                ->pluck('id');
                
            $siteIds = \App\Models\Site::whereIn('project_id', $clientProjects)->pluck('id');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_projects' => $clientProjects->count(),
                    'active_projects' => Project::whereIn('id', $clientProjects)
                        ->where('status', 'in_progress')
                        ->count(),
                    'completed_tasks' => Task::whereIn('site_id', $siteIds)
                        ->where('status', 'completed')
                        ->count(),
                    'overdue_tasks' => $this->getClientOverdueTasksCount($siteIds),
                    'total_material_used' => MaterialUsage::whereIn('task_id', function($query) use ($siteIds) {
                        $query->select('id')
                            ->from('tasks')
                            ->whereIn('site_id', $siteIds);
                    })->sum('quantity'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching client stats: ' . $e->getMessage()
            ], 500);
        }
    }
}