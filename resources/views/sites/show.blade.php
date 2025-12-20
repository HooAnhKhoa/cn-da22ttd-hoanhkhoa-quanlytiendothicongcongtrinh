@extends('layouts.app')

@section('title', $site->site_name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div>
                <nav class="mb-4">
                    <a href="{{ route('sites.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Quay lại
                    </a>
                </nav>
                <h1 class="text-3xl font-bold text-gray-800">{{ $site->site_name }}</h1>
                <p class="text-gray-600">Chi tiết công trường</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('sites.edit', $site) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Chỉnh sửa
                </a>
            </div>
        </div>
    </div>

    <!-- Thông báo -->
    @include('components.alert')

    <!-- Thông tin chính -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Thông tin cơ bản -->
        <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Thông tin công trường</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên công trường</label>
                    <p class="text-gray-900">{{ $site->site_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dự án</label>
                    <p class="text-gray-900">{{ $site->project->project_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày bắt đầu</label>
                    <p class="text-gray-900">{{ $site->start_date ? $site->start_date->format('d/m/Y') : 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày kết thúc</label>
                    <p class="text-gray-900">{{ $site->end_date ? $site->end_date->format('d/m/Y') : 'Chưa có ngày' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($site->status == 'planned') bg-blue-100 text-blue-800
                        @elseif($site->status == 'in_progress') bg-green-100 text-green-800
                        @elseif($site->status == 'completed') bg-gray-100 text-gray-800
                        @elseif($site->status == 'on_hold') bg-yellow-100 text-yellow-800
                        @elseif($site->status == 'cancelled') bg-red-100 text-red-800
                        @endif">
                        @if($site->status == 'planned') Lập kế hoạch
                        @elseif($site->status == 'in_progress') Đang thi công
                        @elseif($site->status == 'completed') Hoàn thành
                        @elseif($site->status == 'on_hold') Tạm dừng
                        @elseif($site->status == 'cancelled') Đã hủy
                        @endif
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tiến độ</label>
                    <div class="flex items-center">
                        <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $site->progress_percent }}%"></div>
                        </div>
                        <span class="text-sm text-gray-600">{{ $site->progress_percent }}%</span>
                    </div>
                </div>
            </div>
            
            @if($site->description)
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                <p class="text-gray-900">{{ $site->description }}</p>
            </div>
            @endif
        </div>

        <!-- Thông tin bổ sung -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Thông tin bổ sung</h2>
            <div class="space-y-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Ngày tạo</p>
                        <p class="text-sm text-gray-500">{{ $site->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-edit text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Cập nhật lần cuối</p>
                        <p class="text-sm text-gray-500">{{ $site->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-hard-hat text-purple-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Loại</p>
                        <p class="text-sm text-gray-500">Công trường xây dựng</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Danh sách công việc của công trường -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Danh sách công việc</h2>
            <a href="{{ route('tasks.create.from.site', $site) }}" 
            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i> Thêm công việc
            </a>
        </div>
        
        @if(isset($tasks) && count($tasks) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên công việc</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mô tả</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ưu tiên</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tiến độ</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tasks as $task)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <a href="{{ route('tasks.show', $task) }}" class="font-medium text-blue-600 hover:text-blue-800">
                                        {{ $task->task_name }}
                                    </a>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        {{ $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('d/m/Y') : 'N/A' }}
                                        @if($task->end_date)
                                            → {{ \Carbon\Carbon::parse($task->end_date)->format('d/m/Y') }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ \Illuminate\Support\Str::limit($task->description, 50) }}
                                </td>
                                <td class="px-4 py-3">
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
                                    <span class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ $statusColors[$task->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusTexts[$task->status] ?? $task->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
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
                                    <span class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ $priorityColors[$task->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $priorityTexts[$task->priority] ?? $task->priority }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $task->progress_percent ?? 0 }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600">{{ $task->progress_percent ?? 0 }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('tasks.show', $task) }}" 
                                        class="text-blue-600 hover:text-blue-800" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('tasks.edit', $task) }}" 
                                        class="text-green-600 hover:text-green-800" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-800" 
                                                    title="Xóa"
                                                    onclick="return confirm('Bạn có chắc muốn xóa công việc này?')">
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
            
            <!-- Thống kê công việc -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm text-gray-600">Tổng công việc</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $tasks->count() }}</p>
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
            </div>
        @else
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-tasks text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có công việc nào</h3>
                <p class="text-gray-500 mb-6">Công trường chưa được gán công việc</p>
                <a href="{{ route('tasks.create', ['site_id' => $site->id]) }}" 
                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-plus mr-2"></i> Tạo công việc đầu tiên
                </a>
            </div>
        @endif
    </div>

    <!-- Tổng hợp vật tư sử dụng -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Tổng hợp vật tư sử dụng</h2>
            <div class="flex gap-2">
                @if(isset($materialSummary) && count($materialSummary) > 0)
                <button onclick="printMaterialReport()" 
                    class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    <i class="fas fa-print mr-1"></i> In báo cáo
                </button>
                @endif
            </div>
        </div>
        
        @if(isset($materialSummary) && count($materialSummary) > 0)
            <!-- Thống kê nhanh -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-boxes text-blue-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600">Loại vật tư</p>
                            <p class="text-xl font-bold text-gray-900">{{ count($typeSummary ?? []) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-weight text-green-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600">Tổng số lượng</p>
                            <p class="text-xl font-bold text-gray-900">
                                @php
                                    $totalQuantity = 0;
                                    foreach($materialSummary as $item) {
                                        $totalQuantity += $item->total_quantity;
                                    }
                                @endphp
                                {{ number_format($totalQuantity, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-cube text-purple-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600">Vật tư sử dụng</p>
                            <p class="text-xl font-bold text-gray-900">{{ count($materialSummary) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-orange-50 rounded-lg p-4 border border-orange-100">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-history text-orange-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600">Lần sử dụng</p>
                            <p class="text-xl font-bold text-gray-900">
                                @php
                                    $totalUsage = 0;
                                    foreach($materialSummary as $item) {
                                        $totalUsage += $item->usage_count;
                                    }
                                @endphp
                                {{ $totalUsage }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            @if(isset($typeSummary) && count($typeSummary) > 0)
                <!-- Biểu đồ đơn giản -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Phân bổ vật tư theo loại</h3>
                    <div class="space-y-3">
                        @foreach($typeSummary as $type)
                            @php
                                $typeName = \App\Models\Material::getTypes()[$type->type] ?? $type->type;
                                $percentage = $totalQuantity > 0 ? ($type->total_quantity / $totalQuantity * 100) : 0;
                                $colorClass = $getMaterialTypeColor($type->type);
                                // Tách màu từ class
                                preg_match('/bg-(\w+)-100/', $colorClass, $matches);
                                $colorName = $matches[1] ?? 'blue';
                            @endphp
                            <div class="mb-2">
                                <div class="flex justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">{{ $typeName }}</span>
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ number_format($type->total_quantity, 2) }} ({{ round($percentage, 1) }}%)
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full bg-{{ $colorName }}-500" 
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <!-- Bảng tổng hợp vật tư -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Chi tiết vật tư sử dụng</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vật tư</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nhà cung cấp</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tổng số lượng</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lần sử dụng</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lần cuối</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($materialSummary as $item)
                                @php
                                    $typeName = \App\Models\Material::getTypes()[$item->type] ?? $item->type;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $item->materials_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->unit }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-1 text-xs rounded-full 
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
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $item->supplier }}</td>
                                    <td class="px-4 py-3">
                                        <span class="font-bold text-gray-900">{{ number_format($item->total_quantity, 2) }}</span>
                                        <span class="text-sm text-gray-500 ml-1">{{ $item->unit }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-800 font-medium">
                                            {{ $item->usage_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        {{ $item->last_usage_date ? \Carbon\Carbon::parse($item->last_usage_date)->format('d/m/Y') : 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-box-open text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có dữ liệu vật tư</h3>
                <p class="text-gray-500 mb-6">Công trường chưa sử dụng vật tư nào</p>
                <a href="{{ route('material_usage.create') }}" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-plus mr-2"></i> Thêm vật tư sử dụng
                </a>
            </div>
        @endif
    </div>

    <!-- Hành động -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Hành động</h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('sites.edit', $site) }}" 
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                <i class="fas fa-edit mr-2"></i>Chỉnh sửa công trường
            </a>
            <form action="{{ route('sites.destroy', $site) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 flex items-center"
                        onclick="return confirm('Bạn có chắc muốn xóa công trường này?')">
                    <i class="fas fa-trash mr-2"></i>Xóa công trường
                </button>
            </form>
            <a href="#" 
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                <i class="fas fa-file-pdf mr-2"></i>Xuất báo cáo
            </a>
        </div>
    </div>
</div>

<script>
// In báo cáo vật tư
function printMaterialReport() {
    const originalContent = document.body.innerHTML;
    
    // Lấy phần nội dung cần in
    const materialSection = document.querySelector('.bg-white.rounded-lg.shadow.p-6.mb-6:nth-last-child(2)');
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

// Thêm CSS cho hiệu ứng hover
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        tr {
            transition: background-color 0.2s ease;
        }
        tr:hover {
            background-color: #f9fafb;
        }
        .progress-bar {
            transition: width 1s ease-in-out;
        }
    `;
    document.head.appendChild(style);
});
</script>
@endsection