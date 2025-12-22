@extends('layouts.app')

@section('title', 'Chỉnh sửa Vật tư: ' . $material->materials_name)

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
                    <a href="{{ route('admin.materials.show', $material) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">
                        {{ Str::limit($material->materials_name, 30) }}
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Chỉnh sửa</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Chỉnh sửa Vật tư</h1>
                    <p class="text-gray-600 mt-2">{{ $material->materials_name }}</p>
                </div>
                <a href="{{ route('admin.materials.show', $material) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Quay lại
                </a>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <form action="{{ route('admin.materials.update', $material) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Material Name -->
                    <div class="mb-6">
                        <label for="materials_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Tên vật tư <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="materials_name" 
                               name="materials_name" 
                               value="{{ old('materials_name', $material->materials_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('materials_name') border-red-500 @enderror"
                               required>
                        @error('materials_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Type -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Loại vật tư <span class="text-red-500">*</span>
                            </label>
                            <select id="type" 
                                    name="type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror"
                                    required>
                                <option value="">-- Chọn loại vật tư --</option>
                                @foreach(App\Models\Material::getTypes() as $value => $label)
                                    <option value="{{ $value }}" {{ old('type', $material->type) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Unit -->
                        <div>
                            <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">
                                Đơn vị tính <span class="text-red-500">*</span>
                            </label>
                            <select id="unit" 
                                    name="unit" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('unit') border-red-500 @enderror"
                                    required>
                                <option value="">-- Chọn đơn vị --</option>
                                @foreach(App\Models\Material::getUnits() as $value => $label)
                                    <option value="{{ $value }}" {{ old('unit', $material->unit) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unit')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Supplier -->
                    <div class="mb-8">
                        <label for="supplier" class="block text-sm font-medium text-gray-700 mb-2">
                            Nhà cung cấp <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="supplier" 
                               name="supplier" 
                               value="{{ old('supplier', $material->supplier) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('supplier') border-red-500 @enderror"
                               required>
                        @error('supplier')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-between pt-6 border-t border-gray-200">
                        <button type="button" 
                                onclick="if(confirm('Bạn có chắc muốn xóa vật tư này?')) { document.getElementById('delete-form').submit(); }"
                                class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Xóa vật tư
                        </button>
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.materials.show', $material) }}" 
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
                      action="{{ route('admin.materials.destroy', $material) }}" 
                      method="POST" 
                      class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Material Info -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Thông tin hiện tại
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Mã vật tư</p>
                        <p class="font-medium">#{{ $material->id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Ngày tạo</p>
                        <p class="font-medium">{{ $material->created_at}}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Cập nhật cuối</p>
                        <p class="font-medium">{{ $material->updated_at}}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Số lần sử dụng</p>
                        <p class="font-medium">{{ $material->tasks()->count() }} công việc</p>
                    </div>
                </div>
            </div>

            {{-- <!-- Recent Usage -->
            @if($material->tasks()->count() > 0)
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Sử dụng gần đây
                    </h3>
                    <div class="space-y-3">
                        @foreach($material->tasks()->latest()->take(3)->get() as $task)
                            <a href="{{ route('admin.tasks.show', $task) }}" 
                               class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-sm">{{ $task->task_name }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $task->pivot->usage_date}}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $task->pivot->quantity }} {{ $material->unit }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                        @if($material->tasks()->count() > 3)
                            <a href="{{ route('admin.materials.show', $material) }}" 
                               class="text-center block text-sm text-blue-600 hover:text-blue-800 pt-2">
                                Xem tất cả {{ $material->tasks()->count() }} bản ghi
                            </a>
                        @endif
                    </div>
                </div>
            @endif --}}
        </div>
    </div>
</div>
@endsection