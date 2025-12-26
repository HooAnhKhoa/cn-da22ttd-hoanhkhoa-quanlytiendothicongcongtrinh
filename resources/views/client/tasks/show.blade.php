@extends('layouts.app')

@section('title', $task->task_name . ' - Chi tiết dự án')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-start">
        <div>
            <nav class="mb-4">
                <a href="{{ route('client.tasks.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800">Tên công việc: {{ $task->task_name }}</h1>
            <p class="text-xl text-gray-600 mt-2">Vị trí: {{ $task->location }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('client.tasks.edit', $task) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
                <i class="fas fa-edit mr-2"></i>Chỉnh sửa
            </a>
            <!-- Nút thêm báo cáo tiến độ -->
            <a href="{{ route('client.progress_updates.create', ['task_id' => $task->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Thêm báo cáo
            </a>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Task Information -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-info-circle mr-2"></i>Thông tin công việc
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Tên công việc:</span>
                    <span class="font-medium text-gray-800">{{ $task->task_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Nhánh của công việc:</span>
                    @if($task->parent)
                        <a href="{{ route('client.tasks.show', $task->parent) }}" class="font-medium text-gray-800">{{ $task->parent->task_name }}</a>
                    @else
                        <span class="font-medium text-gray-800">Không có</span>
                    @endif
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Công trường:</span>
                    <a href="{{ route('client.sites.show', $task->site)}}">
                        <span class="font-medium text-gray-800">{{ $task->site->site_name ?? 'N/A' }}</span>                
                    </a>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngày bắt đầu:</span>
                    <span class="font-medium text-gray-800">{{ $task->start_date }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngày kết thúc:</span>
                    <span class="font-medium text-gray-800">
                        {{ $task->end_date ? $task->end_date : 'Chưa xác định' }}
                    </span>
                </div>
             
                <div class="flex justify-between">
                    <span class="text-gray-600">Trạng thái:</span>
                    <span class="font-medium text-gray-800">
                        {{ \App\Models\Project::getStatuses()[$task->status] ?? $task->status }}
                    </span>
                </div>
                <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700">Tiến độ tổng thể</span>
                    <span class="text-sm font-medium text-gray-700">{{ $task->progress_percent }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                         style="width: {{ $task->progress_percent }}%"></div>
                </div>
            </div>
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-tachometer-alt text-blue-500 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Tiến độ</span>
                </div>
                <div class="text-right">
                    <span class="text-lg font-bold text-blue-600">{{ $task->progress_percent }}%</span>
                    <p class="text-xs text-gray-500"> 
                        {{ $task->actual_duration ?? 0 }}/{{ $task->planned_duration }} ngày
                    </p>
                </div>
            </div>
            </div>
            
            @if($task->description)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-2">Mô tả:</h4>
                <p class="text-gray-600">{{ $task->description }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Task Team -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-users mr-2"></i>Thông tin bổ sung
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <!-- Owner -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">Ngày tạo</div>
                            <div class="text-sm text-gray-500">{{ $task->created_at }}</div>
                        </div>
                    </div>
                </div>

                <!-- Contractor --> 
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-hard-hat"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">Cập nhật lần cuối</div>
                            <div class="text-sm text-gray-500">{{ $task->updated_at}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>      
</div>

<!-- Báo cáo tiến độ Section -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-chart-line mr-2"></i>Lịch sử báo cáo tiến độ
            <span class="text-sm font-normal text-gray-500 ml-2">({{ $task->progressUpdates->count() }} báo cáo)</span>
        </h2>
        <a href="{{ route('client.progress_updates.create', ['task_id' => $task->id]) }}" 
           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>Thêm báo cáo mới
        </a>
    </div>
    
    <div class="p-6">
        @if($task->progressUpdates->count() > 0)
            <div class="relative">
                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                
                <div class="space-y-8">
                    @foreach($task->progressUpdates->sortByDesc('date') as $report)
                    <div class="relative pl-12">
                        <div class="absolute left-0 w-8 h-8 bg-blue-100 border-4 border-white rounded-full flex items-center justify-center">
                            <i class="fas fa-flag text-blue-600 text-sm"></i>
                        </div>
                        
                        <div class="bg-gray-5 rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="font-semibold text-lg text-gray-800">
                                        <i class="fas fa-calendar-day mr-2"></i>{{ $report->date->format('d/m/Y') }}
                                    </h4>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <i class="fas fa-user mr-1"></i>{{ $report->created_by }}
                                        • <i class="fas fa-clock mr-1"></i>{{ $report->created_at->format('H:i') }}
                                    </p>
                                </div>
                                
                                <div class="text-right">
                                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if($report->progress_percent >= 90) bg-green-100 text-green-800
                                        @elseif($report->progress_percent >= 50) bg-blue-100 text-blue-800
                                        @elseif($report->progress_percent > 0) bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        <i class="fas fa-chart-line mr-1"></i>{{ $report->progress_percent }}%
                                    </div>
                                    
                                    <div class="flex gap-2 mt-2">
                                        <a href="{{ route('client.progress_updates.show', $report) }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('client.progress_updates.edit', $report) }}" 
                                           class="text-yellow-600 hover:text-yellow-800 text-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('client.progress_updates.destroy', $report) }}" 
                                              method="POST" class="inline" 
                                              onsubmit="return confirm('Xóa báo cáo này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                    <span>Tiến độ báo cáo</span>
                                    <span>{{ $report->progress_percent }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                         style="width: {{ $report->progress_percent }}%"></div>
                                </div>
                            </div>
                            
                            @if($report->description)
                            <div class="mb-3">
                                <div class="flex items-center text-gray-700 mb-1">
                                    <i class="fas fa-align-left text-gray-400 mr-2"></i>
                                    <span class="font-medium">Mô tả:</span>
                                </div>
                                <p class="text-gray-600 bg-white p-3 rounded border border-gray-100">
                                    {{ $report->description }}
                                </p>
                            </div>
                            @endif
                            
                            @if($report->attached_files)
                            <div>
                                <div class="flex items-center text-gray-700 mb-2">
                                    <i class="fas fa-paperclip text-gray-400 mr-2"></i>
                                    <span class="font-medium">Tệp đính kèm:</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-4">Thống kê báo cáo:</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-blue-600 font-bold text-xl">
                            {{ $task->progressUpdates->count() }}
                        </div>
                        <div class="text-gray-600 text-sm">Tổng số báo cáo</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="text-green-600 font-bold text-xl">
                            {{ $task->progressUpdates->max('progress_percent') ?? 0 }}%
                        </div>
                        <div class="text-gray-600 text-sm">Tiến độ cao nhất</div>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <div class="text-purple-600 font-bold text-xl">
                            @if($task->progressUpdates->count() > 1)
                                {{ $task->progressUpdates->sortByDesc('date')->first()->progress_percent - $task->progressUpdates->sortBy('date')->first()->progress_percent }}%
                            @else
                                0%
                            @endif
                        </div>
                        <div class="text-gray-600 text-sm">Tăng trưởng tiến độ</div>
                    </div>
                </div>
            </div>
            
        @else
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-chart-line text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Chưa có báo cáo tiến độ</h3>
                <p class="text-gray-500 mb-6">Hãy thêm báo cáo đầu tiên để theo dõi tiến độ công việc</p>
                <a href="{{ route('client.progress_updates.create', ['task_id' => $task->id]) }}" 
                   class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-white hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Thêm báo cáo đầu tiên
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Vật tư đã sử dụng Section -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-boxes mr-2"></i>Vật tư đã sử dụng
            <span class="text-sm font-normal text-gray-500 ml-2">({{ $task->materialUsages->count() }} bản ghi)</span>
        </h2>
        <a href="{{ route('client.material_usage.create', ['task_id' => $task->id]) }}" 
           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>Thêm vật tư
        </a>
    </div>
    
    <div class="p-6">
        @if($task->materialUsages->count() > 0)
            <!-- Material Usage Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg mr-3">
                            <i class="fas fa-boxes text-blue-600"></i>
                        </div>
                        <div>
                            <div class="text-blue-600 font-bold text-xl">{{ $task->materialUsages->count() }}</div>
                            <div class="text-gray-600 text-sm">Bản ghi sử dụng</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg mr-3">
                            <i class="fas fa-weight text-green-600"></i>
                        </div>
                        <div>
                            <div class="text-green-600 font-bold text-xl">
                                @php
                                    $totalQuantity = $task->materialUsages->sum('quantity');
                                @endphp
                                {{ number_format($totalQuantity, 2) }}
                            </div>
                            <div class="text-gray-600 text-sm">Tổng số lượng</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg mr-3">
                            <i class="fas fa-money-bill-wave text-purple-600"></i>
                        </div>
                        <div>
                            <div class="text-purple-600 font-bold text-xl">
                                @php
                                    // Tính tổng chi phí ước tính
                                    $estimatedCost = 0;
                                    foreach($task->materialUsages as $usage) {
                                        // Giả sử có trường unit_price trong model Material
                                        $unitPrice = isset($usage->material->unit_price) ? $usage->material->unit_price : 0;
                                        $estimatedCost += $usage->quantity * $unitPrice;
                                    }
                                    echo number_format($estimatedCost) . ' đ';
                                @endphp
                            </div>
                            <div class="text-gray-600 text-sm">Chi phí ước tính</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                            <i class="fas fa-calendar-alt text-yellow-600"></i>
                        </div>
                        <div>
                            <div class="text-yellow-600 font-bold text-xl">
                                @if($task->materialUsages->isNotEmpty())
                                    {{ \Carbon\Carbon::parse($task->materialUsages->max('usage_date'))->format('d/m/Y') }}
                                @else
                                    N/A
                                @endif
                            </div>
                            <div class="text-gray-600 text-sm">Lần sử dụng cuối</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Material Usage Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên vật tư</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nhà cung cấp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày sử dụng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ghi chú</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($task->materialUsages as $index => $usage)
                            @php
                                $material = $usage->material;
                            @endphp
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
                                            <div class="text-sm font-medium text-gray-900">
                                                @if($material)
                                                        {{-- <a href="{{ route('client.materials.show', $material) }}" class="hover:text-blue-600">
                                                            {{ $material->materials_name }}
                                                        </a> --}}
                                                @else
                                                    <span class="text-red-500">Vật tư đã bị xóa</span>
                                                @endif
                                            </div>
                                            @if($material)
                                            <div class="text-sm text-gray-500">
                                                Đơn vị: {{ $material->unit }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($material)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ \App\Models\Material::getTypes()[$material->type] ?? $material->type }}
                                    </span>
                                    @else
                                    <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $material->supplier ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 text-center">
                                        {{ number_format($usage->quantity, 2) }}
                                    </div>
                                    @if($material)
                                    <div class="text-xs text-gray-500 text-center">
                                        {{ $material->unit }}
                                    </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($usage->usage_date)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @if($usage->notes)
                                        <div class="max-w-xs truncate" title="{{ $usage->notes }}">
                                            {{ $usage->notes }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">Không có</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('client.material_usage.edit', $usage) }}" 
                                           class="text-yellow-600 hover:text-yellow-900" 
                                           title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('client.material_usage.destroy', $usage) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Bạn có chắc muốn xóa vật tư này khỏi công việc?')">
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
                        <!-- Total Row -->
                        <tr class="bg-gray-50 font-semibold">
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                TỔNG CỘNG:
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                {{ number_format($totalQuantity, 2) }}
                            </td>
                            <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Material Usage by Type Chart -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-4">Phân bố vật tư theo loại:</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Bar Chart -->
                    <div>
                        <canvas id="materialTypeChart" height="200"></canvas>
                    </div>
                    
                    <!-- Type Breakdown -->
                    <div>
                        <div class="space-y-3">
                            @php
                                $materialsByType = [];
                                foreach($task->materialUsages as $usage) {
                                    if ($usage->material) {
                                        $type = $usage->material->type;
                                        if (!isset($materialsByType[$type])) {
                                            $materialsByType[$type] = [
                                                'count' => 0,
                                                'quantity' => 0,
                                                'type_name' => \App\Models\Material::getTypes()[$type] ?? $type
                                            ];
                                        }
                                        $materialsByType[$type]['count']++;
                                        $materialsByType[$type]['quantity'] += $usage->quantity;
                                    }
                                }
                            @endphp
                            @foreach($materialsByType as $type => $data)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full mr-2" 
                                             style="background-color: {{ $chartColors[$loop->index % count($chartColors)] ?? '#4f46e5' }}"></div>
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ $data['type_name'] }}
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-bold text-gray-900">{{ $data['count'] }} bản ghi</span>
                                        <div class="text-xs text-gray-500">
                                            {{ number_format($data['quantity'], 2) }} đơn vị
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
        @else
            <!-- Empty state -->
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-boxes text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Chưa có vật tư nào được sử dụng</h3>
                <p class="text-gray-500 mb-6">Hãy thêm vật tư đã sử dụng cho công việc này</p>
                <a href="{{ route('client.material_usage.create', ['task_id' => $task->id]) }}" 
                   class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-white hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Thêm vật tư đầu tiên
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Action Buttons -->
<div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
    <div class="text-sm text-gray-500">
        Tạo ngày: {{ $task->created_at->format('d/m/Y H:i') }}
        • Cập nhật: {{ $task->updated_at->format('d/m/Y H:i') }}
    </div>
    <div class="flex gap-2">
        <form action="{{ route('client.tasks.destroy', $task) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 transition-colors"
                    onclick="return confirm('Bạn có chắc chắn muốn xóa dự án này?')">
                <i class="fas fa-trash mr-2"></i>Xóa dự án
            </button>
        </form>
        <a href="{{ route('client.tasks.edit', $task) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
            <i class="fas fa-edit mr-2"></i>Chỉnh sửa
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Material Usage Chart
    @if($task->materialUsages->count() > 0)
        const materialTypeChart = document.getElementById('materialTypeChart');
        if (materialTypeChart) {
            // Dữ liệu cho biểu đồ
            @php
                $materialsByType = [];
                foreach($task->materialUsages as $usage) {
                    if ($usage->material) {
                        $type = $usage->material->type;
                        if (!isset($materialsByType[$type])) {
                            $materialsByType[$type] = [
                                'count' => 0,
                                'quantity' => 0,
                                'type_name' => \App\Models\Material::getTypes()[$type] ?? $type
                            ];
                        }
                        $materialsByType[$type]['count']++;
                        $materialsByType[$type]['quantity'] += $usage->quantity;
                    }
                }
            @endphp
            
            const chartColors = [
                '#4f46e5', '#10b981', '#f59e0b', '#ef4444', 
                '#8b5cf6', '#06b6d4', '#84cc16', '#f97316'
            ];
            
            const typeLabels = [];
            const typeCounts = [];
            const typeColors = [];
            const typeQuantities = [];
            
            let colorIndex = 0;
            @foreach($materialsByType as $type => $data)
                typeLabels.push("{{ $data['type_name'] }}");
                typeCounts.push({{ $data['count'] }});
                typeQuantities.push({{ $data['quantity'] }});
                typeColors.push(chartColors[{{ $loop->index }} % chartColors.length]);
            @endforeach
            
            new Chart(materialTypeChart, {
                type: 'bar',
                data: {
                    labels: typeLabels,
                    datasets: [{
                        label: 'Số bản ghi',
                        data: typeCounts,
                        backgroundColor: typeColors,
                        borderColor: typeColors.map(color => color.replace('0.8', '1')),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
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
                                    label += context.parsed.y + ' bản ghi';
                                    
                                    // Thêm thông tin về tổng số lượng
                                    const typeIndex = context.dataIndex;
                                    const quantity = typeQuantities[typeIndex];
                                    label += ` (${quantity.toFixed(2)} đơn vị)`;
                                    
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
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

/* Hover effect for material rows */
.hover\:bg-gray-50:hover {
    transition: background-color 0.2s ease;
}
</style>
@endpush