@extends('layouts.app')

@section('title', 'Chỉnh sửa Vật tư Sử dụng')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('tasks.show', $materialUsage->task) }}" class="inline-flex items-center text-sm text-gray-700 hover:text-blue-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    Công việc: {{ $materialUsage->task->task_name }}
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Chỉnh sửa vật tư</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Chỉnh sửa Vật tư Sử dụng</h1>
            <p class="text-gray-600 mt-2">Cập nhật thông tin vật tư sử dụng trong công việc</p>
        </div>
        <a href="{{ route('tasks.show', $materialUsage->task) }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Quay lại
        </a>
    </div>

    <!-- Current Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h4 class="text-sm font-medium text-blue-800 mb-1">Công việc</h4>
                <p class="text-blue-900">{{ $materialUsage->task->task_name }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-blue-800 mb-1">Vật tư hiện tại</h4>
                <p class="text-blue-900">{{ $materialUsage->material->materials_name }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md p-6">
                <form action="{{ route('material_usage.update', $materialUsage) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Material Selection -->
                    <div class="mb-6">
                        <label for="material_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Vật tư <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="material_id" 
                                    name="material_id" 
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none bg-white @error('material_id') border-red-500 @enderror"
                                    required>
                                <option value="">-- Chọn vật tư --</option>
                                @foreach($materials as $material)
                                    <option value="{{ $material->id }}" 
                                            data-unit="{{ $material->unit }}"
                                            {{ old('material_id', $materialUsage->material_id) == $material->id ? 'selected' : '' }}>
                                        {{ $material->materials_name }} 
                                        ({{ $material->supplier }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        @error('material_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Quantity -->
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                Số lượng <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       id="quantity" 
                                       name="quantity" 
                                       value="{{ old('quantity', $materialUsage->quantity) }}"
                                       step="0.01"
                                       min="0.01"
                                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('quantity') border-red-500 @enderror"
                                       placeholder="0.00"
                                       required>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span id="unitDisplay" class="text-gray-500">
                                        @php
                                            $units = App\Models\Material::getUnits();
                                            $currentUnit = $materialUsage->material->unit;
                                        @endphp
                                        {{ $units[$currentUnit] ?? $currentUnit }}
                                    </span>
                                </div>
                            </div>
                            @error('quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Usage Date -->
                        <div>
                            <label for="usage_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Ngày sử dụng <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="date" 
                                       id="usage_date" 
                                       name="usage_date" 
                                       value="{{ old('usage_date', $materialUsage->usage_date->format('Y-m-d')) }}"
                                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('usage_date') border-red-500 @enderror"
                                       required>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            @error('usage_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Notes Field (Thêm nếu cần) -->
                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Ghi chú
                        </label>
                        <textarea id="notes" 
                                  name="notes" 
                                  rows="3"
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror"
                                  placeholder="Ghi chú về việc sử dụng vật tư...">{{ old('notes', $materialUsage->notes ?? '') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-between pt-6 border-t border-gray-200">
                        <button type="button" 
                                onclick="if(confirm('Bạn có chắc muốn xóa bản ghi này?')) { document.getElementById('delete-form').submit(); }"
                                class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Xóa bản ghi
                        </button>
                        <div class="flex space-x-3">
                            <a href="{{ route('tasks.show', $materialUsage->task) }}" 
                               class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                                Hủy
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Cập nhật
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Delete Form (hidden) -->
                <form id="delete-form" 
                      action="{{ route('material_usage.destroy', $materialUsage) }}" 
                      method="POST" 
                      class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Material Details -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Thông tin vật tư
                </h3>
                @php
                    $material = $materialUsage->material;
                @endphp
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Tên vật tư</p>
                        <p class="font-medium">{{ $material->materials_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Nhà cung cấp</p>
                        <p class="font-medium">{{ $material->supplier }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Loại</p>
                        <p class="font-medium">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                {{ App\Models\Material::getTypes()[$material->type] ?? $material->type }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Đơn vị</p>
                        <p class="font-medium">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                {{ App\Models\Material::getUnits()[$material->unit] ?? $material->unit }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Other Materials in Task -->
            @php
                // Sửa: Thay vì materials(), dùng materialUsages()
                $otherMaterialUsages = $materialUsage->task->materialUsages()
                    ->where('id', '!=', $materialUsage->id)
                    ->with('material')
                    ->get();
            @endphp
            
            @if($otherMaterialUsages->count() > 0)
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Vật tư khác trong công việc
                    </h3>
                    <div class="space-y-3">
                        @foreach($otherMaterialUsages as $otherUsage)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        @if($otherUsage->material)
                                            {{ $otherUsage->material->materials_name }}
                                        @else
                                            <span class="text-red-500">Vật tư đã bị xóa</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ \Carbon\Carbon::parse($otherUsage->usage_date)->format('d/m/Y') }}
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    @if($otherUsage->material)
                                        {{ $otherUsage->quantity }} {{ $otherUsage->material->unit }}
                                    @else
                                        {{ $otherUsage->quantity }} đơn vị
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const materialSelect = document.getElementById('material_id');
        const unitDisplay = document.getElementById('unitDisplay');
        
        // Update unit display when material is selected
        materialSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const unit = selectedOption.getAttribute('data-unit');
            const units = @json(App\Models\Material::getUnits());
            
            if (unit && units[unit]) {
                unitDisplay.textContent = units[unit];
            } else if (unit) {
                unitDisplay.textContent = unit;
            }
        });
        
        // Trigger change on page load if there's a selected value
        if (materialSelect.value) {
            materialSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush
@endsection