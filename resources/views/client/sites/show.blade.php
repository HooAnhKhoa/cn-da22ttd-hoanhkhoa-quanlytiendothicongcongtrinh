@extends('layouts.app')

@section('title', $site->site_name . ' - Chi tiết công trường')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-start">
        <div>
            <nav class="mb-4">
                <a href="{{ route('client.sites.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800">Tên công trường: {{ $site->site_name }}</h1>
            @if($site->location)
                <p class="text-xl text-gray-600 mt-2">Địa điểm: {{ $site->location }}</p>
            @endif
        </div>
        <div class="flex gap-2">
            <a href="{{ route('client.sites.edit', $site) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
                <i class="fas fa-edit mr-2"></i>Chỉnh sửa
            </a>
        </div>
    </div>
</div>

<!-- Thông báo -->
@include('components.alert')

<!-- Tính toán tiến độ tổng thể từ các công việc -->
@php
    $totalTasks = $tasks->count();
    $totalProgress = 0;
    $overallProgress = 0;
    
    if($totalTasks > 0) {
        foreach($tasks as $task) {
            $totalProgress += $task->progress_percent ?? 0;
        }
        $overallProgress = round($totalProgress / $totalTasks, 1);
    }
    
    // Tính tổng hợp vật tư theo loại
    $materialTypes = [];
    $materialTypeData = [];
    $chartColors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];
    
    if(isset($typeSummary) && count($typeSummary) > 0) {
        $colorIndex = 0;
        foreach($typeSummary as $type) {
            $typeName = \App\Models\Material::getTypes()[$type->type] ?? $type->type;
            $materialTypes[] = $typeName;
            $materialTypeData[] = [
                'quantity' => $type->total_quantity,
                'count' => $type->type_count,
                'color' => $chartColors[$colorIndex % count($chartColors)]
            ];
            $colorIndex++;
        }
    }
@endphp

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Site Information -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-info-circle mr-2"></i>Thông tin công trường
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Tên công trường:</span>
                    <span class="font-medium text-gray-800">{{ $site->site_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Dự án:</span>
                    @if($site->project)
                        <a href="{{ route('client.projects.show', $site->project) }}" class="font-medium text-gray-800 hover:text-blue-600">
                            {{ $site->project->project_name }}
                        </a>
                    @else
                        <span class="font-medium text-gray-800">Không có</span>
                    @endif
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Địa chỉ:</span>
                    <span class="font-medium text-gray-800">{{ $site->address ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngày bắt đầu:</span>
                    <span class="font-medium text-gray-800">
                        {{ $site->start_date ? $site->start_date->format('d/m/Y') : 'Chưa xác định' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngày kết thúc:</span>
                    <span class="font-medium text-gray-800">
                        {{ $site->end_date ? $site->end_date->format('d/m/Y') : 'Chưa xác định' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Trạng thái:</span>
                    <span class="font-medium text-gray-800">
                        @if($site->status == 'planned') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Lập kế hoạch
                            </span>
                        @elseif($site->status == 'in_progress') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Đang thi công
                            </span>
                        @elseif($site->status == 'completed') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Hoàn thành
                            </span>
                        @elseif($site->status == 'on_hold') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Tạm dừng
                            </span>
                        @elseif($site->status == 'cancelled') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Đã hủy
                            </span>
                        @endif
                    </span>
                </div>
                
                <!-- Tiến độ tổng thể (tính từ công việc) -->
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">Tiến độ tổng thể</span>
                        <span class="text-sm font-medium text-gray-700">{{ $overallProgress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $overallProgress }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Tính từ {{ $totalTasks }} công việc
                        @if($totalTasks > 0)
                            • Trung bình: {{ round($totalProgress / $totalTasks, 1) }}%
                        @endif
                    </p>
                </div>
                
                <!-- Statistics Box -->
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-tachometer-alt text-blue-500 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Tiến độ</span>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-bold text-blue-600">{{ $overallProgress }}%</span>
                        <p class="text-xs text-gray-500"> 
                            {{ $tasks->where('status', 'completed')->count() }}/{{ $totalTasks }} công việc
                        </p>
                    </div>
                </div>
            </div>
            
            @if($site->description)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-2">Mô tả:</h4>
                <p class="text-gray-600">{{ $site->description }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Additional Information -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-chart-bar mr-2"></i>Thống kê công việc
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="text-center p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-600">Tổng công việc</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalTasks }}</p>
                </div>
                <div class="text-center p-3 bg-green-50 rounded-lg">
                    <p class="text-sm text-gray-600">Đã hoàn thành</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $tasks->where('status', 'completed')->count() }}
                    </p>
                </div>
                <div class="text-center p-3 bg-yellow-50 rounded-lg">
                    <p class="text-sm text-gray-600">Đang thực hiện</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $tasks->where('status', 'in_progress')->count() }}
                    </p>
                </div>
                <div class="text-center p-3 bg-red-50 rounded-lg">
                    <p class="text-sm text-gray-600">Trễ hạn</p>
                    <p class="text-2xl font-bold text-gray-900">
                        @php
                            $overdueCount = 0;
                            foreach($tasks as $task) {
                                if ($task->end_date && \Carbon\Carbon::parse($task->end_date)->isPast() && $task->status != 'completed') {
                                    $overdueCount++;
                                }
                            }
                        @endphp
                        {{ $overdueCount }}
                    </p>
                </div>
            </div>
            
            <div class="mt-4 space-y-4">
                <div>
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Tỷ lệ hoàn thành</span>
                        <span>{{ $totalTasks > 0 ? round(($tasks->where('status', 'completed')->count() / $totalTasks) * 100, 1) : 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $totalTasks > 0 ? ($tasks->where('status', 'completed')->count() / $totalTasks) * 100 : 0 }}%"></div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">Ngày tạo</div>
                            <div class="text-sm text-gray-500">{{ $site->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">Cập nhật lần cuối</div>
                            <div class="text-sm text-gray-500">{{ $site->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>      
</div>

<!-- Danh sách công việc của công trường -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-tasks mr-2"></i>Danh sách công việc
            <span class="text-sm font-normal text-gray-500 ml-2">({{ $tasks->count() ?? 0 }} công việc)</span>
        </h2>
        <a href="{{ route('client.tasks.create', ['site_id' => $site->id]) }}" 
           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>Thêm công việc
        </a>
    </div>
    
    <div class="p-6">
        @if($tasks && $tasks->count() > 0)
            <!-- Tasks Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên công việc</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ưu tiên</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiến độ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tasks as $index => $task)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-tasks text-blue-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('client.tasks.show', $task) }}" class="hover:text-blue-600">
                                                {{ $task->task_name }}
                                            </a>
                                        </div>
                                        @if($task->description)
                                        <div class="text-sm text-gray-500 max-w-xs truncate">
                                            {{ $task->description }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'in_progress' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                    $statusTexts = [
                                        'pending' => 'Chờ thực hiện',
                                        'in_progress' => 'Đang thực hiện',
                                        'completed' => 'Hoàn thành',
                                        'cancelled' => 'Đã hủy',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$task->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusTexts[$task->status] ?? $task->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $priorityColors = [
                                        'low' => 'bg-green-100 text-green-800',
                                        'medium' => 'bg-yellow-100 text-yellow-800',
                                        'high' => 'bg-red-100 text-red-800',
                                    ];
                                    $priorityTexts = [
                                        'low' => 'Thấp',
                                        'medium' => 'Trung bình',
                                        'high' => 'Cao',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityColors[$task->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $priorityTexts[$task->priority] ?? $task->priority }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                             style="width: {{ $task->progress_percent ?? 0 }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $task->progress_percent ?? 0 }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>{{ $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('d/m/Y') : 'N/A' }}</div>
                                @if($task->end_date)
                                <div class="text-gray-500 text-xs">→ {{ \Carbon\Carbon::parse($task->end_date)->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('client.tasks.show', $task) }}" 
                                       class="text-blue-600 hover:text-blue-900" 
                                       title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('client.tasks.edit', $task) }}" 
                                       class="text-yellow-600 hover:text-yellow-900" 
                                       title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('client.tasks.destroy', $task) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Bạn có chắc muốn xóa công việc này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900" 
                                                title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
        @else
            <!-- Empty state -->
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-tasks text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Chưa có công việc nào</h3>
                <p class="text-gray-500 mb-6">Công trường chưa được gán công việc</p>
                <a href="{{ route('client.tasks.create', ['site_id' => $site->id]) }}" 
                   class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-white hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Tạo công việc đầu tiên
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Tổng hợp vật tư sử dụng -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-boxes mr-2"></i>Tổng hợp vật tư sử dụng
            <span class="text-sm font-normal text-gray-500 ml-2">({{ isset($materialSummary) ? count($materialSummary) : 0 }} vật tư)</span>
        </h2>
        @if(isset($materialSummary) && count($materialSummary) > 0)
        <button onclick="printMaterialReport()" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
            <i class="fas fa-print mr-2"></i>In báo cáo
        </button>
        @endif
    </div>
    
    <div class="p-6">
        @if(isset($materialSummary) && count($materialSummary) > 0)
            <!-- Material Usage Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg mr-3">
                            <i class="fas fa-boxes text-blue-600"></i>
                        </div>
                        <div>
                            <div class="text-blue-600 font-bold text-xl">{{ count($typeSummary ?? []) }}</div>
                            <div class="text-gray-600 text-sm">Loại vật tư</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg mr-3">
                            <i class="fas fa-weight text-green-600"></i>
                        </div>
                        <div>
                            @php
                                $totalQuantity = 0;
                                foreach($materialSummary as $item) {
                                    $totalQuantity += $item->total_quantity;
                                }
                            @endphp
                            <div class="text-green-600 font-bold text-xl">
                                {{ number_format($totalQuantity, 2) }}
                            </div>
                            <div class="text-gray-600 text-sm">Tổng số lượng</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg mr-3">
                            <i class="fas fa-cube text-purple-600"></i>
                        </div>
                        <div>
                            <div class="text-purple-600 font-bold text-xl">{{ count($materialSummary) }}</div>
                            <div class="text-gray-600 text-sm">Vật tư sử dụng</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-orange-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 rounded-lg mr-3">
                            <i class="fas fa-history text-orange-600"></i>
                        </div>
                        <div>
                            @php
                                $totalUsage = 0;
                                foreach($materialSummary as $item) {
                                    $totalUsage += $item->usage_count;
                                }
                            @endphp
                            <div class="text-orange-600 font-bold text-xl">
                                {{ $totalUsage }}
                            </div>
                            <div class="text-gray-600 text-sm">Lần sử dụng</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Biểu đồ cột phân bổ vật tư -->
            <div class="mb-8">
                <h4 class="font-semibold text-gray-700 mb-4">Phân bổ vật tư theo loại:</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Biểu đồ cột -->
                    <div>
                        <canvas id="materialTypeChart" height="250"></canvas>
                    </div>
                    
                    <!-- Bảng chi tiết -->
                    <div>
                        <div class="space-y-3">
                            @php
                                $typeIndex = 0;
                            @endphp
                            @foreach($typeSummary as $type)
                                @php
                                    $typeName = \App\Models\Material::getTypes()[$type->type] ?? $type->type;
                                    $percentage = $totalQuantity > 0 ? ($type->total_quantity / $totalQuantity * 100) : 0;
                                    $color = $chartColors[$typeIndex % count($chartColors)];
                                    $typeIndex++;
                                @endphp
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $color }}"></div>
                                        <span class="text-sm font-medium text-gray-700">{{ $typeName }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-bold text-gray-900">{{ number_format($type->total_quantity, 2) }}</span>
                                        <div class="text-xs text-gray-500">
                                            {{ round($percentage, 1) }}% • {{ $type->type_count }} loại
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Material Summary Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên vật tư</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nhà cung cấp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng số lượng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lần sử dụng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lần cuối</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($materialSummary as $index => $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-box text-blue-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->materials_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $item->unit }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $typeName = \App\Models\Material::getTypes()[$item->type] ?? $item->type;
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($item->type == 'building_materials') bg-blue-100 text-blue-800
                                    @elseif($item->type == 'electrical') bg-yellow-100 text-yellow-800
                                    @elseif($item->type == 'plumbing') bg-green-100 text-green-800
                                    @elseif($item->type == 'finishing') bg-purple-100 text-purple-800
                                    @elseif($item->type == 'tools') bg-red-100 text-red-800
                                    @elseif($item->type == 'safety') bg-orange-100 text-orange-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $typeName }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->supplier ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 text-center">
                                    {{ number_format($item->total_quantity, 2) }}
                                </div>
                                <div class="text-xs text-gray-500 text-center">
                                    {{ $item->unit }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-800 font-medium">
                                    {{ $item->usage_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->last_usage_date ? \Carbon\Carbon::parse($item->last_usage_date)->format('d/m/Y') : 'N/A' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
        @else
            <!-- Empty state -->
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-box-open text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Chưa có dữ liệu vật tư</h3>
                <p class="text-gray-500 mb-6">Công trường chưa sử dụng vật tư nào</p>
                <a href="{{ route('client.material_usage.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-white hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Thêm vật tư sử dụng
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Action Buttons -->
<div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
    <div class="text-sm text-gray-500">
        Tạo ngày: {{ $site->created_at->format('d/m/Y H:i') }}
        • Cập nhật: {{ $site->updated_at->format('d/m/Y H:i') }}
        • Tiến độ tổng thể: {{ $overallProgress }}%
    </div>
    <div class="flex gap-2">
        <form action="{{ route('client.sites.destroy', $site) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 transition-colors"
                    onclick="return confirm('Bạn có chắc chắn muốn xóa công trường này?')">
                <i class="fas fa-trash mr-2"></i>Xóa công trường
            </button>
        </form>
        <a href="{{ route('client.sites.edit', $site) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
            <i class="fas fa-edit mr-2"></i>Chỉnh sửa
        </a>
        <a href="#" 
           class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-white hover:bg-purple-700 transition-colors">
            <i class="fas fa-file-pdf mr-2"></i>Xuất báo cáo
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Material Usage Chart - Biểu đồ cột
    @if(isset($typeSummary) && count($typeSummary) > 0)
        const materialTypeChart = document.getElementById('materialTypeChart');
        if (materialTypeChart) {
            @php
                $typeLabels = [];
                $typeQuantities = [];
                $typeColors = [];
                $typeCounts = [];
                
                $colorIndex = 0;
                foreach($typeSummary as $type) {
                    $typeName = \App\Models\Material::getTypes()[$type->type] ?? $type->type;
                    $typeLabels[] = $typeName;
                    $typeQuantities[] = $type->total_quantity;
                    $typeCounts[] = $type->type_count;
                    $typeColors[] = $chartColors[$colorIndex % count($chartColors)];
                    $colorIndex++;
                }
            @endphp
            
            const chartColors = [
                '#4f46e5', '#10b981', '#f59e0b', '#ef4444', 
                '#8b5cf6', '#06b6d4', '#84cc16', '#f97316'
            ];
            
            const typeLabels = @json($typeLabels);
            const typeQuantities = @json($typeQuantities);
            const typeColors = @json($typeColors);
            const typeCounts = @json($typeCounts);
            
            new Chart(materialTypeChart, {
                type: 'bar',
                data: {
                    labels: typeLabels,
                    datasets: [{
                        label: 'Số lượng (đơn vị)',
                        data: typeQuantities,
                        backgroundColor: typeColors,
                        borderColor: typeColors.map(color => color.replace('0.8', '1')),
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
                            display: true,
                            position: 'top',
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
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    const quantity = context.parsed.y;
                                    const percentage = (quantity / {{ $totalQuantity }}) * 100;
                                    label += quantity.toFixed(2) + ' đơn vị';
                                    label += ' (' + percentage.toFixed(1) + '%)';
                                    
                                    // Thêm thông tin số loại vật tư
                                    const typeIndex = context.dataIndex;
                                    const typeCount = typeCounts[typeIndex];
                                    label += '\nSố loại: ' + typeCount;
                                    
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
                                text: 'Số lượng (đơn vị)',
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
                                text: 'Loại vật tư',
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

// In báo cáo vật tư
function printMaterialReport() {
    const originalContent = document.body.innerHTML;
    
    // Lấy phần nội dung cần in
    const materialSection = document.querySelector('.bg-white.rounded-lg.shadow.mb-8:last-child');
    const printContent = materialSection.innerHTML;
    
    // Tạo cửa sổ in
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Báo cáo vật tư - {{ $site->site_name }}</title>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
            <style>
                @media print {
                    body { margin: 20px; }
                    .no-print { display: none !important; }
                    h1, h2, h3 { color: #000 !important; }
                    table { width: 100% !important; }
                }
                body { font-family: Arial, sans-serif; }
                .print-header { 
                    border-bottom: 2px solid #333; 
                    margin-bottom: 20px; 
                    padding-bottom: 10px; 
                }
            </style>
        </head>
        <body class="p-8">
            <div class="print-header">
                <h1 class="text-2xl font-bold mb-2">Báo cáo vật tư sử dụng</h1>
                <div class="text-gray-600">
                    <p>Công trường: {{ $site->site_name }}</p>
                    <p>Ngày in: ${new Date().toLocaleDateString('vi-VN')}</p>
                    <p>Tiến độ tổng thể: {{ $overallProgress }}%</p>
                </div>
            </div>
            ${printContent}
            <div class="mt-8 text-center text-sm text-gray-500 no-print">
                <button onclick="window.print()" class="px-4 py-2 bg-blue-500 text-white rounded mr-2">In</button>
                <button onclick="window.close()" class="px-4 py-2 bg-gray-500 text-white rounded">Đóng</button>
            </div>
            <script>
                window.onload = function() {
                    window.print();
                };
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}
</script>
@endpush

@push('styles')
<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Tooltip styles for truncated text */
.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Hover effect for table rows */
.hover\:bg-gray-50:hover {
    transition: background-color 0.2s ease;
}

/* Progress bar animation */
.progress-bar {
    transition: width 1s ease-in-out;
}

/* Chart container responsive */
canvas {
    max-width: 100%;
    height: auto !important;
}
</style>
@endpush