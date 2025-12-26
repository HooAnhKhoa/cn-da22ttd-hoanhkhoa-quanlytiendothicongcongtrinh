@extends('layouts.app')

@section('title', 'Thêm Vật tư vào Công việc')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                @if(isset($task) && $task)
                    <a href="{{ route('client.tasks.show', $task) }}" class="inline-flex items-center text-sm text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        Công việc: {{ $task->task_name }}
                    </a>
                @else
                    <span class="inline-flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        Thêm vật tư
                    </span>
                @endif
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Thêm vật tư</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Thêm Vật tư vào Công việc</h1>
            <p class="text-gray-600 mt-2">
                @if(isset($task) && $task)
                    Thêm vật tư cho công việc: <strong>{{ $task->task_name }}</strong>
                @else
                    Chọn công việc và vật tư để thêm vào
                @endif
            </p>
        </div>
        <a href="{{ url()->previous() }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Quay lại
        </a>
    </div>

    <!-- Form -->
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-md p-6">
            <form action="{{ route('client.material_usage.store') }}" method="POST">
                @csrf
                
                <!-- Task Selection -->
                @if(isset($task) && $task)
                    <input type="hidden" name="task_id" value="{{ $task->id }}">
                    <div class="mb-6 p-4 bg-green-50 rounded-lg">
                        <h3 class="font-medium text-green-800 mb-2">Công việc đã chọn:</h3>
                        <p class="text-green-900">{{ $task->task_name }}</p>
                        <p class="text-sm text-green-700 mt-1">
                            Dự án: {{ $task->project->project_name ?? 'N/A' }}
                        </p>
                    </div>
                @else
                    <div class="mb-6">
                        <label for="task_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Chọn công việc <span class="text-red-500">*</span>
                        </label>
                        <select id="task_id" 
                                name="task_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('task_id') border-red-500 @enderror"
                                required>
                            <option value="">-- Chọn công việc --</option>
                            @foreach($tasks as $taskItem)
                                <option value="{{ $taskItem->id }}" {{ old('task_id') == $taskItem->id ? 'selected' : '' }}>
                                    {{ $taskItem->task_name }} 
                                    (Dự án: {{ $taskItem->project->project_name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('task_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <!-- Material Selection -->
                @if(isset($material) && $material)
                    <input type="hidden" name="material_id" value="{{ $material->id }}">
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="font-medium text-blue-800 mb-2">Vật tư đã chọn:</h3>
                        <p class="text-blue-900">{{ $material->materials_name }}</p>
                        <p class="text-sm text-blue-700 mt-1">
                            Nhà cung cấp: {{ $material->supplier }} | 
                            Đơn vị: {{ App\Models\Material::getUnits()[$material->unit] ?? $material->unit }}
                        </p>
                    </div>
                @else
                    <div class="mb-6">
                        <label for="material_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Chọn vật tư <span class="text-red-500">*</span>
                        </label>
                        <select id="material_id" 
                                name="material_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('material_id') border-red-500 @enderror"
                                required>
                            <option value="">-- Chọn vật tư --</option>
                            @foreach($materials as $materialItem)
                                <option value="{{ $materialItem->id }}" {{ old('material_id') == $materialItem->id ? 'selected' : '' }}>
                                    {{ $materialItem->materials_name }} 
                                    ({{ $materialItem->supplier }})
                                </option>
                            @endforeach
                        </select>
                        @error('material_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <!-- Quantity and Date -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                            Số lượng <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   id="quantity" 
                                   name="quantity" 
                                   value="{{ old('quantity') }}"
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
                        </div>
                        @error('quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Nhập số lượng vật tư đã sử dụng</p>
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
                                   value="{{ old('usage_date', date('Y-m-d')) }}"
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
                        <p class="mt-1 text-sm text-gray-500">Ngày thực tế sử dụng vật tư</p>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-8">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Ghi chú (tùy chọn)
                    </label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Ghi chú về cách sử dụng, vị trí, hoặc lưu ý đặc biệt...">{{ old('notes') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Thông tin bổ sung về việc sử dụng vật tư</p>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ url()->previous() }}" 
                       class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Hủy bỏ
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Thêm vật tư
                    </button>
                </div>
            </form>
        </div>

        <!-- Quick Tips -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mt-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h3 class="font-medium text-blue-800 mb-2">Hướng dẫn sử dụng</h3>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Chọn công việc cần thêm vật tư</li>
                        <li>• Chọn vật tư đã sử dụng</li>
                        <li>• Nhập chính xác số lượng và ngày sử dụng</li>
                        <li>• Có thể thêm ghi chú để theo dõi chi tiết</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection