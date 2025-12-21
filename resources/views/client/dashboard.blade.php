{{-- @extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-gray-600">Tổng quan về hệ thống quản lý xây dựng</p>
</div>

<!-- Thống kê tổng quan -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-project-diagram text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Tổng dự án</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_projects'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-tasks text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Tổng công việc</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_tasks'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-users text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Người dùng</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_users'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                <i class="fas fa-hard-hat text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Vật liệu tổng</p>
                <p class="text-2xl font-semibold text-gray-900">
                    @php
                        $totalMaterial = isset($materialStats['total_quantity']) ? 
                            number_format($materialStats['total_quantity'], 1) : '0';
                    @endphp
                    {{ $totalMaterial }}
                </p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Dự án gần đây -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">Dự án gần đây</h2>
            <a href="{{ route('projects.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Xem tất cả
            </a>
        </div>
        <div class="p-6">
            @if($recentProjects->count() > 0)
                <div class="space-y-4">
                    @foreach($recentProjects->take(5) as $project)
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800">{{ $project->project_name }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $project->location }}</p>
                            <div class="flex items-center mt-2 space-x-2">
                                <span class="inline-block px-2 py-1 text-xs rounded-full 
                                    @if($project->status == 'in_progress') bg-green-100 text-green-800
                                    @elseif($project->status == 'completed') bg-blue-100 text-blue-800
                                    @elseif($project->status == 'on_hold') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ \App\Models\Project::getStatuses()[$project->status] ?? $project->status }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $project->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('projects.show', $project) }}" class="ml-4 p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
                @if($recentProjects->count() > 5)
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-500">Đang hiển thị 5/{{ $recentProjects->count() }} dự án</p>
                </div>
                @endif
            @else
                <div class="text-center py-8">
                    <i class="fas fa-project-diagram text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Chưa có dự án nào</p>
                    <a href="{{ route('projects.create') }}" class="inline-block mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Tạo dự án đầu tiên
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Công việc gần đây -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">Công việc gần đây</h2>
            <a href="{{ route('tasks.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Xem tất cả
            </a>
        </div>
        <div class="p-6">
            @if($recentTasks->count() > 0)
                <div class="space-y-4">
                    @foreach($recentTasks->take(5) as $task)
                    <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-semibold text-gray-800">{{ $task->task_name }}</h3>
                            <span class="inline-block px-2 py-1 text-xs rounded-full 
                                @if($task->status == 'completed') bg-green-100 text-green-800
                                @elseif($task->status == 'in_progress') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ \App\Models\Project::getStatuses()[$task->status] ?? $task->status }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">Dự án: {{ $task->site->project->project_name }}</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $task->progress_percent }}%"></div>
                                </div>
                                <span class="text-sm text-gray-500">{{ $task->progress_percent }}%</span>
                            </div>
                            <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Xem chi tiết
                            </a>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            Cập nhật: {{ $task->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-tasks text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Chưa có công việc nào</p>
                    <a href="{{ route('tasks.create') }}" class="inline-block mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Tạo công việc đầu tiên
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<br>

<!-- Thống kê vật liệu -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Biểu đồ phân bố vật liệu theo loại -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-chart-pie mr-2"></i>Phân bố vật liệu theo loại
            </h2>
        </div>
        <div class="p-6">
            @if(isset($materialStats['by_type']) && count($materialStats['by_type']) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <canvas id="materialTypeChart" height="250"></canvas>
                    </div>
                    <div>
                        <div class="space-y-3">
                            @php
                                $chartColors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];
                                $colorIndex = 0;
                                $totalQuantity = $materialStats['total_quantity'] ?? 0;
                            @endphp
                            @foreach($materialStats['by_type'] as $type => $quantity)
                                @php
                                    $percentage = $totalQuantity > 0 ? ($quantity / $totalQuantity * 100) : 0;
                                    $color = $chartColors[$colorIndex % count($chartColors)];
                                    $colorIndex++;
                                    $typeName = \App\Models\Material::getTypes()[$type] ?? $type;
                                @endphp
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $color }}"></div>
                                        <span class="text-sm font-medium text-gray-700">{{ $typeName }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-bold text-gray-900">{{ number_format($quantity, 1) }}</span>
                                        <div class="text-xs text-gray-500">
                                            {{ round($percentage, 1) }}%
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-boxes text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Chưa có dữ liệu vật liệu</h3>
                    <p class="text-gray-500 mb-6">Hệ thống chưa ghi nhận vật liệu sử dụng</p>
                    <a href="{{ route('material_usage.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-white hover:bg-green-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Thêm vật liệu sử dụng
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Top 5 vật liệu sử dụng nhiều nhất -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-chart-bar mr-2"></i>Top 5 vật liệu sử dụng nhiều nhất
            </h2>
            <a href="{{ route('materials.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Xem tất cả
            </a>
        </div>
        <div class="p-6">
            @if(isset($materialStats['top_materials']) && count($materialStats['top_materials']) > 0)
                <div class="space-y-4">
                    @foreach($materialStats['top_materials'] as $index => $material)
                    <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-800 font-medium mr-3">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <h3 class="font-semibold text-gray-800">{{ $material->materials_name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $material->supplier ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-lg text-gray-900">
                                    {{ number_format($material->total_quantity, 1) }}
                                </span>
                                <p class="text-xs text-gray-500">{{ $material->unit }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span>Loại: {{ \App\Models\Material::getTypes()[$material->type] ?? $material->type }}</span>
                            <span>{{ $material->usage_count }} lần sử dụng</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-chart-bar text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Chưa có dữ liệu vật liệu</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Phân bố vật liệu theo dự án -->
@if(isset($materialStats['by_project']) && count($materialStats['by_project']) > 0)
<div class="bg-white rounded-lg shadow mb-8">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-chart-line mr-2"></i>Phân bố vật liệu theo dự án
        </h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <canvas id="materialByProjectChart" height="250"></canvas>
            </div>
            <div>
                <div class="space-y-3">
                    @foreach($materialStats['by_project'] as $projectName => $quantity)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-2 bg-blue-500"></div>
                            <span class="text-sm font-medium text-gray-700 truncate" title="{{ $projectName }}">
                                {{ Str::limit($projectName, 30) }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-gray-900">{{ number_format($quantity, 1) }}</span>
                            <div class="text-xs text-gray-500">
                                @php
                                    $percentage = $totalQuantity > 0 ? ($quantity / $totalQuantity * 100) : 0;
                                @endphp
                                {{ round($percentage, 1) }}%
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Thống kê nâng cao -->
<div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <i class="fas fa-exclamation-triangle text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Công việc trễ hạn</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['overdue_tasks'] ?? 0 }}</p>
            </div>
        </div>
        <p class="text-xs text-gray-500">Cần xử lý ngay</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i class="fas fa-clock text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Công việc đang thực hiện</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['in_progress_tasks'] ?? 0 }}</p>
            </div>
        </div>
        <p class="text-xs text-gray-500">Đang được xử lý</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                <i class="fas fa-box-open text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Loại vật liệu sử dụng</p>
                <p class="text-2xl font-semibold text-gray-900">
                    {{ isset($materialStats['by_type']) ? count($materialStats['by_type']) : 0 }}
                </p>
            </div>
        </div>
        <p class="text-xs text-gray-500">Phân loại vật liệu</p>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Biểu đồ vật liệu theo loại (pie chart)
    @if(isset($materialStats['by_type']) && count($materialStats['by_type']) > 0)
        const materialTypeChart = document.getElementById('materialTypeChart');
        if (materialTypeChart) {
            @php
                $typeLabels = [];
                $typeQuantities = [];
                $typeColors = [];
                $typeNames = [];
                
                $colorIndex = 0;
                foreach($materialStats['by_type'] as $type => $quantity) {
                    $typeLabels[] = \App\Models\Material::getTypes()[$type] ?? $type;
                    $typeQuantities[] = $quantity;
                    $typeColors[] = $chartColors[$colorIndex % count($chartColors)];
                    $colorIndex++;
                }
            @endphp
            
            const typeLabels = @json($typeLabels);
            const typeQuantities = @json($typeQuantities);
            const typeColors = @json($typeColors);
            const totalQuantity = {{ $totalQuantity }};
            
            new Chart(materialTypeChart, {
                type: 'doughnut',
                data: {
                    labels: typeLabels,
                    datasets: [{
                        data: typeQuantities,
                        backgroundColor: typeColors,
                        borderColor: typeColors.map(color => color.replace('0.8', '1')),
                        borderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    const quantity = context.parsed;
                                    const percentage = (quantity / totalQuantity) * 100;
                                    label += quantity.toFixed(1) + ' đơn vị';
                                    label += ' (' + percentage.toFixed(1) + '%)';
                                    return label;
                                }
                            }
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true,
                        duration: 1000
                    }
                }
            });
        }
    @endif

    // Biểu đồ vật liệu theo dự án (bar chart)
    @if(isset($materialStats['by_project']) && count($materialStats['by_project']) > 0)
        const materialByProjectChart = document.getElementById('materialByProjectChart');
        if (materialByProjectChart) {
            @php
                $projectLabels = [];
                $projectQuantities = [];
                
                foreach($materialStats['by_project'] as $projectName => $quantity) {
                    $projectLabels[] = Str::limit($projectName, 20);
                    $projectQuantities[] = $quantity;
                }
            @endphp
            
            const projectLabels = @json($projectLabels);
            const projectQuantities = @json($projectQuantities);
            
            new Chart(materialByProjectChart, {
                type: 'bar',
                data: {
                    labels: projectLabels,
                    datasets: [{
                        label: 'Số lượng vật liệu',
                        data: projectQuantities,
                        backgroundColor: 'rgba(79, 70, 229, 0.7)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    const quantity = context.parsed.y;
                                    const percentage = totalQuantity > 0 ? (quantity / totalQuantity * 100) : 0;
                                    label += quantity.toFixed(1) + ' đơn vị';
                                    label += ' (' + percentage.toFixed(1) + '%)';
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Số lượng vật liệu',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toFixed(1);
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Dự án',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        }
    @endif
});
</script>
@endpush

@push('styles')
<style>
.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

canvas {
    max-width: 100%;
    height: auto !important;
}
</style>
@endpush --}}



clent