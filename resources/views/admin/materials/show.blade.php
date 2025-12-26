@extends('layouts.app')

@section('title', $material->materials_name . ' - Chi tiết Vật tư')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-start">
        <div>
            <nav class="mb-4">
                <a href="{{ route('admin.materials.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800">Tên vật tư: {{ $material->materials_name }}</h1>
            @if($material->supplier)
                <p class="text-xl text-gray-600 mt-2">Nhà cung cấp: {{ $material->supplier }}</p>
            @endif
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.materials.edit', $material) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
                <i class="fas fa-edit mr-2"></i>Chỉnh sửa
            </a>
        </div>
    </div>
</div>

<!-- Thông báo -->
@include('components.alert')

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Material Information -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-info-circle mr-2"></i>Thông tin vật tư
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Mã vật tư:</span>
                    <span class="font-medium text-gray-800">#{{ $material->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tên vật tư:</span>
                    <span class="font-medium text-gray-800">{{ $material->materials_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Loại vật tư:</span>
                    <span class="font-medium text-gray-800">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ App\Models\Material::getTypes()[$material->type] ?? $material->type }}
                        </span>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Đơn vị tính:</span>
                    <span class="font-medium text-gray-800">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ App\Models\Material::getUnits()[$material->unit] ?? $material->unit }}
                        </span>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Nhà cung cấp:</span>
                    <span class="font-medium text-gray-800">{{ $material->supplier ?? 'Chưa có' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngày tạo:</span>
                    <span class="font-medium text-gray-800">
                        {{ $material->created_at ? \Carbon\Carbon::parse($material->created_at)->format('d/m/Y') : 'Chưa xác định' }}
                    </span>
                </div>
                
                <!-- Statistics Box -->
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-boxes text-blue-500 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Tổng sử dụng</span>
                    </div>
                    <div class="text-right">
                        @php
                            $totalUsage = $material->usages()->sum('quantity');
                            $usageCount = $material->usages()->count();
                        @endphp
                        <span class="text-lg font-bold text-blue-600">{{ $totalUsage }} {{ $material->unit }}</span>
                        <p class="text-xs text-gray-500">({{ $usageCount }} lần sử dụng)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Material Statistics -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-chart-bar mr-2"></i>Thống kê sử dụng
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <!-- Statistics Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm text-gray-600">Lần sử dụng</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $usageCount }}
                        </p>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-lg">
                        <p class="text-sm text-gray-600">Tổng số lượng</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $totalUsage }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $material->unit }}</p>
                    </div>
                    <div class="text-center p-3 bg-yellow-50 rounded-lg">
                        <p class="text-sm text-gray-600">Dự án sử dụng</p>
                        <p class="text-2xl font-bold text-gray-900">
                            @php
                                $projectCount = $material->usages()
                                    ->join('tasks', 'material_usages.task_id', '=', 'tasks.id')
                                    ->join('sites', 'tasks.site_id', '=', 'sites.id')
                                    ->join('projects', 'sites.project_id', '=', 'projects.id')
                                    ->distinct('projects.id')
                                    ->count('projects.id');
                            @endphp
                            {{ $projectCount }}
                        </p>
                    </div>
                    <div class="text-center p-3 bg-red-50 rounded-lg">
                        <p class="text-sm text-gray-600">Công việc sử dụng</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $material->usages()->distinct('task_id')->count('task_id') }}
                        </p>
                    </div>
                </div>
                
                <!-- Usage Timeline -->
                @php
                    $firstUsage = $material->usages()->orderBy('usage_date')->first();
                    $lastUsage  = $material->usages()->orderBy('usage_date', 'desc')->first();
                @endphp
                <div class="pt-6 border-t border-gray-200">
                    <h4 class="font-semibold text-gray-700 mb-3">Lịch sử sử dụng</h4>
                    <div class="space-y-3">
                        @if($firstUsage)
                        <div class="flex items-center">
                            <div class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-play text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-800 text-sm">Lần đầu sử dụng</div>
                                <div class="text-xs text-gray-500">{{ $firstUsage->usage_date->format('d/m/Y') }}</div>
                            </div>
                        </div>
                        @endif
                        @if($lastUsage)
                        <div class="flex items-center">
                            <div class="w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-flag-checkered text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-800 text-sm">Lần cuối sử dụng</div>
                                <div class="text-xs text-gray-500">{{ $lastUsage->usage_date->format('d/m/Y') }}</div>
                            </div>
                        </div>
                        @endif
                        <div class="flex items-center">
                            <div class="w-6 h-6 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-plus text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-800 text-sm">Tạo vật tư</div>
                                <div class="text-xs text-gray-500">{{ $material->created_at}}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="pt-6 border-t border-gray-200">
                    <h4 class="font-semibold text-gray-700 mb-3">Hành động nhanh</h4>
                    <div class="grid grid-cols-1 gap-2">
                        <a href="{{ route('admin.material_usage.create', ['material_id' => $material->id]) }}" 
                           class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                            <i class="fas fa-plus mr-2"></i>Thêm vào công việc
                        </a>
                        <a href="{{ route('admin.materials.edit', $material) }}" 
                           class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            <i class="fas fa-edit mr-2"></i>Chỉnh sửa vật tư
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách lịch sử sử dụng -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-history mr-2"></i>Lịch sử sử dụng
            <span class="text-sm font-normal text-gray-500 ml-2">({{ $usageHistory->total() }} bản ghi)</span>
        </h2>
        <a href="{{ route('admin.material_usage.create', ['material_id' => $material->id]) }}" 
           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            <i class="fas fa-plus mr-2"></i>Thêm sử dụng
        </a>
    </div>
    
    <div class="p-6">
        @if($usageHistory->count() > 0)
            <!-- Usage History Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày sử dụng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Công việc</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dự án</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mô tả</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($usageHistory as $index => $usage)
                        @php
                            $task = $usage->task;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ ($usageHistory->currentPage() - 1) * $usageHistory->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $usage->usage_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($task)
                                <a href="{{ route('admin.tasks.show', $task) }}" 
                                   class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                    {{ $task->task_name ?? 'N/A' }}
                                </a>
                                @else
                                <span class="text-sm text-gray-500">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $usage->quantity }} {{ $material->unit }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($task && $task->site && $task->site->project)
                                <a href="{{ route('admin.projects.show', $task->site->project) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    {{ $task->site->project->project_name }}
                                </a>
                                @else
                                <span class="text-gray-500">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 max-w-xs truncate">
                                {{ $usage->description ?? 'Không có mô tả' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.material_usage.edit', $usage) }}" 
                                       class="text-yellow-600 hover:text-yellow-900" 
                                       title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.material_usage.destroy', $usage) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Bạn có chắc muốn xóa bản ghi này?')">
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
            
            <!-- Pagination -->
            @if($usageHistory->hasPages())
            <div class="mt-6">
                {{ $usageHistory->links() }}
            </div>
            @endif
            
        @else
            <!-- Empty state -->
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-history text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Chưa có lịch sử sử dụng</h3>
                <p class="text-gray-500 mb-6">Vật tư chưa được sử dụng trong công việc nào</p>
                <a href="{{ route('admin.material_usage.create', ['material_id' => $material->id]) }}" 
                   class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-white hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Thêm sử dụng đầu tiên
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Tabs Section cho Related Materials và Usage Charts -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="border-b border-gray-200">
        <nav class="flex -mb-px">
            <button class="tab-button active py-4 px-6 text-sm font-medium border-b-2 border-blue-500 text-blue-600" data-tab="related">
                <i class="fas fa-boxes mr-2"></i>Vật tư cùng loại
                @php
                    $relatedCount = \App\Models\Material::where('type', $material->type)
                        ->where('id', '!=', $material->id)
                        ->count();
                @endphp
                <span class="ml-1">({{ $relatedCount }})</span>
            </button>
            <button class="tab-button py-4 px-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="projects">
                <i class="fas fa-project-diagram mr-2"></i>Dự án sử dụng
                <span class="ml-1">({{ $projectCount }})</span>
            </button>
        </nav>
    </div>

    <div class="p-6">
        <!-- Related Materials Tab -->
        <div id="tab-related" class="tab-content active">
            @php
                $relatedMaterials = \App\Models\Material::where('type', $material->type)
                    ->where('id', '!=', $material->id)
                    ->take(10)
                    ->get();
            @endphp
            
            @if($relatedMaterials->count() > 0)
                <div class="space-y-4">
                    @foreach($relatedMaterials as $related)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-box text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-semibold text-gray-800">{{ $related->materials_name }}</h4>
                                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                        <span>Nhà cung cấp: {{ $related->supplier ?: 'Chưa có' }}</span>
                                        <span>• Đơn vị: 
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $related->unit }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.materials.show', $related) }}" 
                                   class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-eye mr-1"></i>Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($relatedCount > 10)
                <div class="text-center mt-4">
                    <a href="{{ route('admin.materials.index', ['type' => $material->type]) }}" 
                       class="inline-flex items-center px-4 py-2 text-sm text-blue-600 hover:text-blue-800">
                        <i class="fas fa-arrow-right mr-2"></i>Xem tất cả vật tư cùng loại
                    </a>
                </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-boxes text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Không có vật tư cùng loại</h3>
                    <p class="text-gray-500 mb-6">Đây là vật tư duy nhất thuộc loại này</p>
                </div>
            @endif
        </div>

        <!-- Projects Tab -->
        <div id="tab-projects" class="tab-content hidden">
            @if($projectCount > 0)
                <div class="space-y-4">
                    @php
                        $projects = $material->usages()
                            ->join('tasks', 'material_usages.task_id', '=', 'tasks.id')
                            ->join('sites', 'tasks.site_id', '=', 'sites.id')
                            ->join('projects', 'sites.project_id', '=', 'projects.id')
                            ->select('projects.id', 'projects.project_name', 'projects.location')
                            ->selectRaw('SUM(material_usages.quantity) as total_quantity')
                            ->groupBy('projects.id', 'projects.project_name', 'projects.location')
                            ->orderBy('total_quantity', 'desc')
                            ->get();
                    @endphp
                    
                    @foreach($projects as $project)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-project-diagram text-green-600"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-semibold text-gray-800">{{ $project->project_name }}</h4>
                                    @if($project->location)
                                    <p class="text-gray-600 text-sm mt-1">{{ $project->location }}</p>
                                    @endif
                                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                        <span>Tổng sử dụng: 
                                            <span class="font-medium text-green-600">{{ $project->total_quantity }} {{ $material->unit }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.projects.show', $project) }}" 
                                   class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-eye mr-1"></i>Xem dự án
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-project-diagram text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Chưa có dự án sử dụng</h3>
                    <p class="text-gray-500 mb-6">Vật tư chưa được sử dụng trong dự án nào</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
    <div class="text-sm text-gray-500">
        Tạo ngày: {{ $material->created_at}}
        • Cập nhật: {{ $material->updated_at}}
        • Tổng sử dụng: {{ $usageCount }} lần
    </div>
    <div class="flex gap-2">
        <form action="{{ route('admin.materials.destroy', $material) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 transition-colors"
                    onclick="return confirm('Bạn có chắc chắn muốn xóa vật tư này?')">
                <i class="fas fa-trash mr-2"></i>Xóa vật tư
            </button>
        </form>
        <a href="{{ route('admin.materials.edit', $material) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
            <i class="fas fa-edit mr-2"></i>Chỉnh sửa
        </a>
        <a href="{{ route('admin.material_usage.create', ['material_id' => $material->id]) }}" 
           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>Thêm sử dụng
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            // Remove active classes from all tabs
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
                content.classList.add('hidden');
            });
            
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Add active class to clicked tab
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('active', 'border-blue-500', 'text-blue-600');
            
            // Show corresponding content
            const target = document.getElementById('tab-' + tabName);
            if (target) {
                target.classList.remove('hidden');
                target.classList.add('active');
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.hover\:bg-gray-50:hover {
    transition: background-color 0.2s ease;
}

.progress-bar {
    transition: width 1s ease-in-out;
}
</style>
@endpush