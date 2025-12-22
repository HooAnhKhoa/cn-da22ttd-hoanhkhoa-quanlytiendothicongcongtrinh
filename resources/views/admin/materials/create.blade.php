@extends('layouts.app')

@section('title', 'Thêm Vật tư Mới')

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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Thêm mới</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Thêm Vật tư Mới</h1>
            <p class="text-gray-600 mt-2">Thêm vật tư mới vào hệ thống</p>
        </div>
        <a href="{{ route('admin.materials.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Quay lại
        </a>
    </div>

    <!-- Form -->
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-md p-6">
            <form action="{{ route('admin.materials.store') }}" method="POST">
                @csrf

                <!-- Material Name -->
                <div class="mb-6">
                    <label for="materials_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Tên vật tư <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="materials_name" 
                           name="materials_name" 
                           value="{{ old('materials_name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('materials_name') border-red-500 @enderror"
                           placeholder="Ví dụ: Xi măng PCB40, Thép phi 8..."
                           required>
                    @error('materials_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Tên vật tư nên rõ ràng, dễ nhận biết</p>
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
                                <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>
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
                                <option value="{{ $value }}" {{ old('unit') == $value ? 'selected' : '' }}>
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
                <div class="mb-6">
                    <label for="supplier" class="block text-sm font-medium text-gray-700 mb-2">
                        Nhà cung cấp <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="supplier" 
                           name="supplier" 
                           value="{{ old('supplier') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('supplier') border-red-500 @enderror"
                           placeholder="Ví dụ: Công ty Xi măng Hà Tiên..."
                           required>
                    @error('supplier')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Nhập tên nhà cung cấp vật tư</p>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.materials.index') }}" 
                       class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Hủy bỏ
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Lưu vật tư
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
                    <h3 class="font-medium text-blue-800 mb-2">Mẹo nhập nhanh</h3>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Tên vật tư nên đầy đủ và rõ ràng</li>
                        <li>• Chọn đúng loại để dễ dàng phân loại sau này</li>
                        <li>• Đơn vị tính phải chính xác để quản lý số lượng</li>
                        <li>• Ghi rõ nhà cung cấp để theo dõi nguồn cung</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('materials_name');
        const typeSelect = document.getElementById('type');
        
        // Auto-detect type based on material name
        nameInput.addEventListener('blur', function() {
            const name = this.value.toLowerCase();
            
            if (name.includes('xi măng') || name.includes('cát') || name.includes('đá') || 
                name.includes('gạch') || name.includes('thép') || name.includes('sắt')) {
                typeSelect.value = 'building_materials';
            } else if (name.includes('ống') || name.includes('van') || name.includes('bồn') || name.includes('nước')) {
                typeSelect.value = 'plumbing';
            } else if (name.includes('dây') || name.includes('công tắc') || name.includes('ổ cắm') || name.includes('điện')) {
                typeSelect.value = 'electrical';
            } else if (name.includes('sơn') || name.includes('gỗ') || name.includes('kính') || name.includes('gạch men')) {
                typeSelect.value = 'finishing';
            } else if (name.includes('búa') || name.includes('kìm') || name.includes('cưa') || name.includes('máy')) {
                typeSelect.value = 'tools';
            } else if (name.includes('bảo hộ') || name.includes('nón') || name.includes('dây an toàn')) {
                typeSelect.value = 'safety';
            }
        });
    });
</script>
@endpush
@endsection