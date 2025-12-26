@extends('layouts.app')

@section('title', 'Bảng điều khiển Kỹ sư')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Bảng Điều Khiển Kỹ Sư</h1>
            <p class="text-gray-500 mt-2 font-medium">Chào mừng trở lại, {{ Auth::user()->username }}! Dưới đây là tổng quan công việc của bạn.</p>
        </div>
        <div class="flex items-center space-x-3">
            <span class="px-4 py-2 bg-blue-50 text-blue-700 rounded-full text-sm font-bold">
                <i class="fas fa-hard-hat mr-2"></i>Kỹ sư
            </span>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow">
            <div class="p-4 bg-blue-100 rounded-2xl text-blue-600 mr-4">
                <i class="fas fa-tasks text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Tổng công việc</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['total_tasks'] }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow">
            <div class="p-4 bg-yellow-100 rounded-2xl text-yellow-600 mr-4">
                <i class="fas fa-spinner text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Đang thực hiện</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['in_progress_tasks'] }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow">
            <div class="p-4 bg-green-100 rounded-2xl text-green-600 mr-4">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Hoàn thành</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['completed_tasks'] }}</h3>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['completion_rate'] }}% tổng số</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow">
            <div class="p-4 bg-red-100 rounded-2xl text-red-600 mr-4">
                <i class="fas fa-exclamation-triangle text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Trễ hạn</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['overdue_tasks'] }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow">
            <div class="p-4 bg-orange-100 rounded-2xl text-orange-600 mr-4">
                <i class="fas fa-clock text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Sắp đến hạn</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['upcoming_deadline_tasks'] }}</h3>
                <p class="text-xs text-gray-500 mt-1">7 ngày tới</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cột trái: Tiến độ và công việc -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Cập nhật tiến độ -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-line text-blue-500 mr-2"></i>
                        Cập nhật tiến độ gần đây
                    </h2>
                    @if(Route::has('client.progress.index'))
                    <a href="{{ route('client.progress.index') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800">
                        Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                    @endif
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            @forelse($recentProgress as $progress)
                            <li>
                                <div class="relative pb-8">
                                    @if (!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <i class="fas fa-hard-hat text-white text-xs"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-600">
                                                    @php
                                                        $description = $progress->description ?? 'Không có mô tả';
                                                        $limitedDescription = Str::limit($description, 100);
                                                    @endphp
                                                    {{ $limitedDescription }} 
                                                    <span class="font-bold text-gray-800">[{{ $progress->task->task_name ?? 'Task ẩn' }}]</span>
                                                </p>
                                                <div class="flex items-center mt-2">
                                                    <p class="text-xs text-gray-400">
                                                        <i class="fas fa-building mr-1"></i>
                                                        {{ $progress->task->site->project->project_name ?? 'N/A' }}
                                                    </p>
                                                    <span class="mx-2 text-gray-300">•</span>
                                                    <p class="text-xs text-gray-400">
                                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                                        {{ $progress->task->site->site_name ?? 'N/A' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap">
                                                <div class="text-gray-500">
                                                    <i class="far fa-clock mr-1"></i>
                                                    {{ $progress->created_at->diffForHumans() ?? 'N/A' }}
                                                </div>
                                                <div class="mt-2">
                                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-bold">
                                                        {{ $progress->progress_percent ?? 0 }}%
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @empty
                            <li class="py-8 text-center">
                                <div class="text-gray-400 mb-3">
                                    <i class="fas fa-chart-line text-3xl"></i>
                                </div>
                                <p class="text-gray-500 italic">Chưa có cập nhật tiến độ nào gần đây.</p>
                                @if(Route::has('client.progress.create'))
                                <a href="{{ route('client.progress.create') }}" class="mt-3 inline-block text-blue-600 hover:text-blue-800 text-sm font-bold">
                                    <i class="fas fa-plus mr-1"></i>Tạo cập nhật mới
                                </a>
                                @endif
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Công việc sắp đến hạn -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-calendar-alt text-orange-500 mr-2"></i>
                        Công việc sắp đến hạn
                    </h2>
                    @if(Route::has('client.tasks.index'))
                    <a href="{{ route('client.tasks.index') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800">
                        Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Công việc</th>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Dự án</th>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Hạn chót</th>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($upcomingTasks as $task)
                            @php
                                $endDate = $task->end_date ? \Carbon\Carbon::parse($task->end_date) : null;
                                $isOverdue = $endDate && $endDate < now();
                                $statusClass = match($task->status) {
                                    'in_progress' => 'bg-blue-50 text-blue-600',
                                    'completed' => 'bg-green-50 text-green-600',
                                    'pending_review' => 'bg-purple-50 text-purple-600',
                                    'planned' => 'bg-gray-50 text-gray-600',
                                    default => 'bg-gray-50 text-gray-600',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-4">
                                    <div class="font-bold text-gray-800">{{ $task->task_name ?? 'Không có tên' }}</div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        <i class="fas fa-map-marker-alt mr-1"></i>{{ $task->site->site_name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="text-sm font-medium text-gray-600">{{ $task->site->project->project_name ?? 'N/A' }}</div>
                                </td>
                                <td class="p-4">
                                    @if($endDate)
                                    <div class="text-sm font-medium {{ $isOverdue ? 'text-red-600' : 'text-gray-600' }}">
                                        {{ $endDate->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $endDate->diffForHumans() }}
                                    </div>
                                    @else
                                    <div class="text-sm text-gray-400">Không có</div>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $statusClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <div class="flex space-x-2">
                                        @if(Route::has('client.tasks.show'))
                                        <a href="{{ route('client.tasks.show', $task->id) }}" 
                                           class="text-blue-600 hover:text-blue-800 font-bold text-sm"
                                           title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endif
                                        @if(Route::has('client.progress.create'))
                                        <a href="{{ route('client.progress.create', ['task_id' => $task->id]) }}" 
                                           class="text-green-600 hover:text-green-800 font-bold text-sm"
                                           title="Cập nhật tiến độ">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center">
                                    <div class="text-gray-400 mb-3">
                                        <i class="fas fa-calendar-check text-3xl"></i>
                                    </div>
                                    <p class="text-gray-500">Không có công việc nào sắp đến hạn.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Cột phải: Thống kê và hiệu suất -->
        <div class="space-y-8">
            <!-- Biểu đồ trạng thái công việc -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-chart-pie text-blue-500 mr-2"></i>
                    Phân bố công việc
                </h2>
                <div class="space-y-6">
                    @php
                        $totalTasks = $stats['total_tasks'];
                        $statuses = [
                            'completed' => ['label' => 'Đã hoàn thành', 'color' => 'bg-green-500'],
                            'in_progress' => ['label' => 'Đang thực hiện', 'color' => 'bg-blue-500'],
                            'pending_review' => ['label' => 'Chờ duyệt', 'color' => 'bg-purple-500'],
                            'planned' => ['label' => 'Lên kế hoạch', 'color' => 'bg-gray-400'],
                        ];
                    @endphp

                    @foreach($statuses as $statusKey => $statusInfo)
                    @php
                        $statusData = $taskStats['by_status'][$statusKey] ?? ['count' => 0, 'percentage' => 0];
                        $count = is_array($statusData) ? ($statusData['count'] ?? 0) : $statusData;
                        $percentage = is_array($statusData) ? ($statusData['percentage'] ?? 0) : 
                                     ($totalTasks > 0 ? round(($count / $totalTasks) * 100) : 0);
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="font-semibold text-gray-700">{{ $statusInfo['label'] }}</span>
                            <span class="text-gray-500">{{ $count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3">
                            <div class="h-3 rounded-full {{ $statusInfo['color'] }}" 
                                 style="width: {{ $percentage }}%">
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    <!-- Tóm tắt -->
                    <div class="pt-4 border-t border-gray-100">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-blue-50 rounded-xl">
                                <p class="text-xs text-gray-600">Dự án đang tham gia</p>
                                <p class="text-xl font-bold text-blue-700">{{ $projects->count() ?? 0 }}</p>
                            </div>
                            <div class="text-center p-3 bg-green-50 rounded-xl">
                                <p class="text-xs text-gray-600">Hạng mục</p>
                                <p class="text-xl font-bold text-green-700">{{ $taskStats['total_sites'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hiệu suất công việc -->
            <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl shadow-xl p-6 text-white">
                <h2 class="text-lg font-bold mb-6 opacity-90 flex items-center">
                    <i class="fas fa-trophy mr-2"></i>
                    Hiệu suất công việc
                </h2>
                <div class="space-y-4">
                    <div class="bg-white/10 rounded-2xl p-4 backdrop-blur-sm border-l-4 border-blue-400">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-xs font-medium opacity-70 uppercase">Tỷ lệ hoàn thành</p>
                                <p class="text-xl font-bold">{{ $stats['completion_rate'] ?? 0 }}%</p>
                            </div>
                            <div class="text-2xl">
                                @php
                                    $completionRate = $stats['completion_rate'] ?? 0;
                                @endphp
                                @if($completionRate >= 80)
                                <i class="fas fa-fire text-yellow-300"></i>
                                @elseif($completionRate >= 50)
                                <i class="fas fa-chart-line"></i>
                                @else
                                <i class="fas fa-chart-bar"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white/10 rounded-2xl p-4 backdrop-blur-sm border-l-4 border-yellow-400">
                        <p class="text-xs font-medium opacity-70 uppercase">Cần xử lý</p>
                        @php
                            $inProgressCount = $taskStats['by_status']['in_progress']['count'] ?? 0;
                            $pendingReviewCount = $taskStats['by_status']['pending_review']['count'] ?? 0;
                            $needAttention = $inProgressCount + $pendingReviewCount;
                        @endphp
                        <p class="text-xl font-bold">{{ $needAttention }} Tasks</p>
                    </div>

                    <div class="bg-white/10 rounded-2xl p-4 backdrop-blur-sm border-l-4 border-green-400">
                        <p class="text-xs font-medium opacity-70 uppercase">Đúng tiến độ</p>
                        @php
                            $onTimeTasks = $stats['total_tasks'] - $stats['overdue_tasks'];
                            $onTimePercentage = $stats['total_tasks'] > 0 ? round(($onTimeTasks / $stats['total_tasks']) * 100) : 0;
                        @endphp
                        <p class="text-xl font-bold">{{ $onTimeTasks }} Tasks</p>
                        <p class="text-xs opacity-70 mt-1">{{ $onTimePercentage }}% tổng số</p>
                    </div>
                </div>
                
                <!-- CTA Buttons -->
                <div class="mt-6 space-y-3">
                    @if(Route::has('client.tasks.index'))
                    <a href="{{ route('client.tasks.index') }}" 
                       class="block w-full text-center py-3 bg-white text-indigo-700 rounded-xl font-bold hover:bg-indigo-50 transition-colors">
                        <i class="fas fa-list mr-2"></i>Xem danh sách công việc
                    </a>
                    @endif
                    @if(Route::has('client.progress.create'))
                    <a href="{{ route('client.progress.create') }}" 
                       class="block w-full text-center py-3 bg-white/20 text-white rounded-xl font-bold hover:bg-white/30 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Tạo cập nhật mới
                    </a>
                    @endif
                </div>
            </div>

            <!-- Dự án đang tham gia -->
            @if($projects && $projects->count() > 0)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-building text-gray-500 mr-2"></i>
                    Dự án đang tham gia
                </h2>
                <div class="space-y-4">
                    @foreach($projects->take(3) as $project)
                    @php
                        $statusClass = match($project->status) {
                            'in_progress' => 'bg-blue-50 text-blue-600',
                            'completed' => 'bg-green-50 text-green-600',
                            default => 'bg-gray-50 text-gray-600',
                        };
                    @endphp
                    <div class="p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-bold text-gray-800">{{ $project->project_name ?? 'Không có tên' }}</h4>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-user-hard-hat mr-1"></i>
                                    {{ $project->contractor->username ?? 'N/A' }}
                                </p>
                            </div>
                            <span class="px-3 py-1 text-xs font-bold rounded-full {{ $statusClass }}">
                                {{ $project->status ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="mt-3 flex items-center text-sm text-gray-500">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span>{{ $project->location ?? 'Không có địa chỉ' }}</span>
                        </div>
                    </div>
                    @endforeach
                    
                    @if($projects->count() > 3)
                    <div class="text-center pt-2">
                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-bold">
                            Xem thêm {{ $projects->count() - 3 }} dự án khác
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection