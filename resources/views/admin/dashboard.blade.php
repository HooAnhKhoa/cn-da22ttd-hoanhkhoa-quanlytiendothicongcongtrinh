@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Bảng điều khiển Admin</h1>
    <p class="text-gray-600">Hệ thống quản lý tiến độ và tài chính công trình</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    @php
        $cards = [
            ['label' => 'Tổng dự án', 'value' => $stats['total_projects'], 'icon' => 'fa-city', 'color' => 'blue'],
            ['label' => 'Công việc trễ', 'value' => $stats['overdue_tasks'], 'icon' => 'fa-clock', 'color' => 'orange'],
            ['label' => 'Dự án đang chạy', 'value' => $stats['active_projects'], 'icon' => 'fa-tasks', 'color' => 'green'],
            ['label' => 'Người dùng', 'value' => $stats['total_users'], 'icon' => 'fa-users', 'color' => 'purple'],
        ];
    @endphp

    @foreach($cards as $card)
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-{{ $card['color'] }}-50 text-{{ $card['color'] }}-600">
                <i class="fas {{ $card['icon'] }} text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">{{ $card['label'] }}</p>
                <p class="text-2xl font-bold text-gray-800">{{ $card['value'] }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Phần 1: Dự án mới cập nhật -->
    <div class="lg:col-span-1 bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800">Dự án mới cập nhật</h3>
            <a href="{{ route('admin.projects.index') }}" class="text-sm text-blue-600 hover:underline">Xem tất cả</a>
        </div>
        <div class="p-6 space-y-4">
            @foreach($recentProjects ?? [] as $project)
            <div class="flex items-start justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900 truncate mb-1">{{ $project->project_name }}</p>
                    <div class="space-y-1">
                        <p class="text-xs text-gray-600">
                            <i class="fas fa-money-bill-wave mr-1 text-gray-400"></i>
                            Ngân sách: {{ number_format($project->total_budget) }} VNĐ
                        </p>
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-clock mr-1 text-gray-400"></i>
                            Cập nhật: {{ $project->updated_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                <span class="text-xs px-2 py-1 rounded-full ml-2 flex-shrink-0 
                    {{ $project->status == 'completed' ? 'bg-green-100 text-green-800' : 
                      ($project->status == 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                      ($project->status == 'on_hold' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                    @if($project->status == 'in_progress')
                        ĐANG CHẠY
                    @elseif($project->status == 'completed')
                        HOÀN THÀNH
                    @elseif($project->status == 'on_hold')
                        TẠM DỪNG
                    @else
                        CHƯA BẮT ĐẦU
                    @endif
                </span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Phần 2: Tiến độ công việc gần đây -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800">Tiến độ công việc gần đây</h3>
            <a href="{{ route('admin.tasks.index') }}" class="text-sm text-blue-600 hover:underline">Xem tất cả</a>
        </div>
        <div class="p-0">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Công việc</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiến độ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày cập nhật</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentProgress as $progress)
                    @php
                        // Sử dụng progress_percent theo database
                        $percentage = $progress->progress_percent ?? 0;
                        $percentage = (int) $percentage;
                        $percentage = min($percentage, 100);
                        $percentage = max($percentage, 0);
                        
                        // Lấy tên người dùng - xử lý null-safe
                        $userName = isset($progress->user) ? 
                            ($progress->user->name ?? $progress->user->username ?? 'System') : 
                            'System';
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-start">
                                {{-- <div class="w-2 h-2 rounded-full mt-2 mr-2 flex-shrink-0
                                    {{ isset($progress->task->status) ? 
                                        ($progress->task->status == 'completed' ? 'bg-green-500' : 
                                        ($progress->task->status == 'in_progress' ? 'bg-blue-500' : 
                                        ($progress->task->status == 'pending' ? 'bg-yellow-500' : 'bg-gray-500'))) : 
                                        'bg-gray-500' }}">
                                </div> --}}
                                <div class="min-w-0">
                                    <div class="text-sm font-bold text-gray-900 truncate">
                                        {{ $progress->task->task_name ?? 'N/A' }}
                                    </div>
                                    <div class="mt-1 space-y-1">
                                        @if(isset($progress->task->site) && isset($progress->task->site->project))
                                        <p class="text-xs text-gray-600 truncate">
                                            <i class="fas fa-project-diagram mr-1 text-gray-400 text-xs"></i>
                                            {{ $progress->task->site->project->project_name }}
                                        </p>
                                        @endif
                                        <p class="text-xs text-gray-500 truncate">
                                            <i class="fas fa-user mr-1 text-gray-400 text-xs"></i>
                                            {{ $userName }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <!-- Thanh tiến độ giống sites.index -->
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $percentage }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <!-- Ngày cập nhật trên 1 hàng -->
                            <div class="text-sm text-gray-900">
                                {{ $progress->created_at->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $progress->created_at->format('H:i') }}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Phần 3: Biểu đồ thanh toán -->
    <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-chart-line mr-2 text-blue-600"></i>Dòng tiền thanh toán
        </h3>
        <div class="h-64">
            <canvas id="paymentChart"></canvas>
        </div>
    </div>

    <!-- Phần 4: Thanh toán mới nhất -->
    <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800">Thanh toán mới nhất</h3>
            <i class="fas fa-money-check-alt text-gray-500"></i>
        </div>
        <div class="p-0">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tiền</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phương thức</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentPayments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-emerald-600">{{ number_format($payment->amount) }} VNĐ</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->pay_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full bg-gray-100 text-xs text-gray-800">
                                {{ $payment->method }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                {{ $payment->status == 'paid' ? 'bg-green-100 text-green-800' : 
                                  ($payment->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                @if($payment->status == 'paid')
                                    ĐÃ THANH TOÁN
                                @elseif($payment->status == 'pending')
                                    CHỜ XỬ LÝ
                                @else
                                    ĐÃ HỦY
                                @endif
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Phần 5: Biểu đồ trạng thái dự án -->
    <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-tasks mr-2 text-indigo-600"></i>Phân bố trạng thái Dự án
        </h3>
        <div class="h-64">
            <canvas id="projectStatusChart"></canvas>
        </div>
    </div>

    <!-- Phần 6: Thống kê nhanh -->
    <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800">Thống kê nhanh</h3>
            <i class="fas fa-chart-bar text-gray-500"></i>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-blue-600 font-medium">Tổng công việc</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_tasks'] }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-sm text-green-600 font-medium">Dự án hoàn thành</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $projectStatusStats['completed'] ?? 0 }}
                    </p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <p class="text-sm text-yellow-600 font-medium">Dự án tạm dừng</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $projectStatusStats['on_hold'] ?? 0 }}
                    </p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <p class="text-sm text-purple-600 font-medium">Tổng thanh toán</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($recentPayments->sum('amount')) }} VNĐ
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Biểu đồ Thanh toán
    const ctxPayment = document.getElementById('paymentChart').getContext('2d');
    new Chart(ctxPayment, {
        type: 'line',
        data: {
            labels: {!! json_encode($paymentChartData->pluck('month')) !!},
            datasets: [{
                label: 'Tổng tiền (VNĐ)',
                data: {!! json_encode($paymentChartData->pluck('total')) !!},
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Tổng tiền: ' + new Intl.NumberFormat('vi-VN').format(context.raw) + ' VNĐ';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN').format(value) + ' VNĐ';
                        }
                    }
                }
            }
        }
    });

    // 2. Biểu đồ Trạng thái Dự án
    const ctxProjectStatus = document.getElementById('projectStatusChart').getContext('2d');
    new Chart(ctxProjectStatus, {
        type: 'pie',
        data: {
            labels: ['Đang chạy', 'Hoàn thành', 'Tạm dừng', 'Chưa bắt đầu'],
            datasets: [{
                data: [
                    {{ $projectStatusStats['in_progress'] ?? 0 }},
                    {{ $projectStatusStats['completed'] ?? 0 }},
                    {{ $projectStatusStats['on_hold'] ?? 0 }},
                    {{ $projectStatusStats['pending'] ?? 0 }}
                ],
                backgroundColor: [
                    '#3b82f6', // blue for in_progress
                    '#10b981', // green for completed
                    '#f59e0b', // yellow for on_hold
                    '#9ca3af'  // gray for pending
                ],
                borderWidth: 1,
                borderColor: '#fff'
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} dự án (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush