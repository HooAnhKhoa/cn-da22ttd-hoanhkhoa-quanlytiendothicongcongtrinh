@extends('layouts.app')

@section('title', $material->materials_name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.materials.index') }}" class="inline-flex items-center text-sm text-gray-700 hover:text-blue-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    Vật tư
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ Str::limit($material->materials_name, 30) }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $material->materials_name }}</h1>
            <div class="flex items-center space-x-4 mt-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    {{ App\Models\Material::getTypes()[$material->type] ?? $material->type }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                    {{ App\Models\Material::getUnits()[$material->unit] ?? $material->unit }}
                </span>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.materials.edit', $material) }}" 
               class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Chỉnh sửa
            </a>
            <a href="{{ route('admin.materials.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Material Details -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Thông tin chi tiết
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500">Mã vật tư</p>
                            <p class="font-medium">#{{ $material->id }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tên vật tư</p>
                            <p class="font-medium">{{ $material->materials_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Loại vật tư</p>
                            <p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ App\Models\Material::getTypes()[$material->type] ?? $material->type }}
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500">Đơn vị tính</p>
                            <p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ App\Models\Material::getUnits()[$material->unit] ?? $material->unit }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nhà cung cấp</p>
                            <p class="font-medium">{{ $material->supplier }}</p>
                        </div>
                        {{-- <div>
                            <p class="text-sm text-gray-500">Ngày tạo</p>
                            <p class="font-medium">{{ $material->created_at->format('d/m/Y H:i') }}</p>
                        </div> --}}
                    </div>
                </div>
            </div>

            <!-- Usage History -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Lịch sử sử dụng
                    </h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        {{ $usageHistory->total() }} bản ghi
                    </span>
                </div>

                @if($usageHistory->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Công việc</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Số lượng</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dự án</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($usageHistory as $usage)
                                    @php
                                        $task = $usage->task;
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $usage->usage_date }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('admin.tasks.show', $usage) }}" 
                                               class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                                {{ $usage->task_name }}
                                            </a>
                                            @if($usage->description)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ Str::limit($usage->description, 50) }}
                                                </p>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $usage->quantity }} {{ $material->unit }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $task->project->project_name ?? 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($usageHistory->hasPages())
                        <div class="mt-4">
                            {{ $usageHistory->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-500">Vật tư chưa được sử dụng trong công việc nào</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Hành động nhanh
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.material_usage.create', ['material_id' => $material->id]) }}" 
                       class="block w-full text-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Thêm vào công việc
                    </a>
                    <a href="{{ route('admin.materials.edit', $material) }}" 
                       class="block w-full text-center px-4 py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Chỉnh sửa
                    </a>
                    <button onclick="if(confirm('Bạn có chắc muốn xóa vật tư này?')) { document.getElementById('delete-form').submit(); }"
                            class="block w-full text-center px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Xóa vật tư
                    </button>
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Thống kê sử dụng
                </h3>
                <div class="space-y-3">
                    @php
                        $totalUsage = $material->usages()->sum('quantity');
                        $usageCount = $material->usages()->count();
                        $firstUsage = $material->usages()->orderBy('usage_date')->first();
                        $lastUsage  = $material->usages()->orderBy('usage_date', 'desc')->first();
                    @endphp

                    
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Tổng số lần sử dụng</span>
                        <span class="font-medium">{{ $usageCount }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Tổng số lượng đã dùng</span>
                        <span class="font-medium">{{ $totalUsage }} {{ $material->unit }}</span>
                    </div>
                    @if($firstUsage)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">Lần đầu sử dụng</p>
                            <p class="font-medium">{{ $firstUsage->usage_date}}</p>
                        </div>
                    @endif
                    @if($lastUsage)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">Lần cuối sử dụng</p>
                            <p class="font-medium">{{ $lastUsage->usage_date}}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Related Materials -->
            @php
                $relatedMaterials = App\Models\Material::where('type', $material->type)
                    ->where('id', '!=', $material->id)
                    ->take(5)
                    ->get();
            @endphp
            
            @if($relatedMaterials->count() > 0)
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                        Vật tư cùng loại
                    </h3>
                    <div class="space-y-3">
                        @foreach($relatedMaterials as $related)
                            <a href="{{ route('admin.materials.show', $related) }}" 
                               class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-sm">{{ $related->materials_name }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $related->supplier }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $related->unit }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Form (hidden) -->
    <form id="delete-form" 
          action="{{ route('admin.materials.destroy', $material) }}" 
          method="POST" 
          class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection