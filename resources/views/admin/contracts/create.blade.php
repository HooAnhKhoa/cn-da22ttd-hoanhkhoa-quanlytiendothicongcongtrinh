@extends('layouts.app')

@section('title', 'Tạo Hợp đồng Mới')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div class="w-full">
                <nav class="mb-4">
                    <a href="{{ route('admin.contracts.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách
                    </a>
                </nav>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Tạo hợp đồng mới</h1>
                <p class="text-gray-600">Thêm hợp đồng xây dựng mới vào hệ thống</p>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            
            <form action="{{ route('admin.contracts.store') }}" method="POST" class="p-8">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Cột trái -->
                    <div class="space-y-6">
                        <!-- Dự án -->
                        <div>
                            <label for="project_id" class="block text-lg font-semibold text-gray-800 mb-3">
                                Dự án <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="project_id" 
                                id="project_id"
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                required
                            >
                                <option value="">Chọn dự án</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->project_name }} ({{ $project->project_code ?? '#' . $project->id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nhà thầu -->
                        <div>
                            <label for="contractor_id" class="block text-lg font-semibold text-gray-800 mb-3">
                                Nhà thầu <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="contractor_id" 
                                id="contractor_id"
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                required
                            >
                                <option value="">Chọn nhà thầu</option>
                                @foreach($contractors as $contractor)
                                    <option value="{{ $contractor->id }}" {{ old('contractor_id') == $contractor->id ? 'selected' : '' }}>
                                        {{ $contractor->name }} - {{ $contractor->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('contractor_id')
                                <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Giá trị hợp đồng -->
                        <div>
                            <label for="contract_value" class="block text-lg font-semibold text-gray-800 mb-3">
                                Giá trị hợp đồng (VNĐ) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="number" 
                                    name="contract_value" 
                                    id="contract_value"
                                    value="{{ old('contract_value') }}"
                                    class="w-full px-4 py-3 pl-12 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                    placeholder="Nhập giá trị hợp đồng..."
                                    required
                                    min="0"
                                    step="any" 
                                >
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 font-medium">đ</span>
                                </div>
                            </div>
                            @error('contract_value')
                                <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Trạng thái -->
                        <div>
                            <label for="status" class="block text-lg font-semibold text-gray-800 mb-3">
                                Trạng thái <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="status" 
                                id="status"
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                required
                            >
                                <option value="">Chọn trạng thái</option>
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}" {{ old('status') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Cột phải -->
                    <div class="space-y-6">
                        <!-- Thông tin thời gian -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Ngày ký -->
                            <div>
                                <label for="signed_date" class="block text-lg font-semibold text-gray-800 mb-3">
                                    Ngày ký <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    name="signed_date" 
                                    id="signed_date"
                                    value="{{ old('signed_date') }}"
                                    class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                    required
                                >
                                @error('signed_date')
                                    <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Ngày hết hạn -->
                            <div>
                                <label for="due_date" class="block text-lg font-semibold text-gray-800 mb-3">
                                    Ngày hết hạn <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    name="due_date" 
                                    id="due_date"
                                    value="{{ old('due_date') }}"
                                    class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                    required
                                >
                                @error('due_date')
                                    <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Mô tả -->
                        <div>
                            <label for="description" class="block text-lg font-semibold text-gray-800 mb-3">
                                Mô tả hợp đồng
                            </label>
                            <textarea 
                                name="description" 
                                id="description"
                                rows="3"
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                placeholder="Mô tả ngắn gọn về hợp đồng..."
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Điều khoản -->
                        <div>
                            <label for="terms" class="block text-lg font-semibold text-gray-800 mb-3">
                                Điều khoản hợp đồng
                            </label>
                            <textarea 
                                name="terms" 
                                id="terms"
                                rows="5"
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                placeholder="Các điều khoản và điều kiện của hợp đồng..."
                            >{{ old('terms') }}</textarea>
                            @error('terms')
                                <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Thông tin thêm -->
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-blue-800 mb-3">
                                <i class="fas fa-lightbulb mr-2"></i>Lưu ý
                            </h3>
                            <div class="space-y-2 text-sm text-blue-700">
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle mt-1 mr-2"></i>
                                    <span>Ngày hết hạn phải sau ngày ký hợp đồng</span>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle mt-1 mr-2"></i>
                                    <span>Giá trị hợp đồng phải được nhập bằng số nguyên dương</span>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle mt-1 mr-2"></i>
                                    <span>Các trường có dấu * là bắt buộc</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="mt-10 flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.contracts.index') }}" 
                        class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all text-lg font-semibold">
                        Hủy bỏ
                    </a>
                    <button 
                        type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all text-lg font-semibold shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-file-contract mr-2"></i>
                        Tạo hợp đồng
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set min date for signed_date to today
    const today = new Date().toISOString().split('T')[0];
    const signedDateInput = document.getElementById('signed_date');
    const dueDateInput = document.getElementById('due_date');
    
    if (signedDateInput) {
        signedDateInput.min = today;
    }
    
    // Validate dates
    if (signedDateInput && dueDateInput) {
        signedDateInput.addEventListener('change', function() {
            dueDateInput.min = this.value;
        });
        
        dueDateInput.addEventListener('change', function() {
            if (signedDateInput.value && this.value < signedDateInput.value) {
                alert('Ngày hết hạn phải sau ngày ký hợp đồng!');
                this.value = '';
            }
        });
    }
    
    // Format currency input
    // const contractValueInput = document.getElementById('contract_value');
    // if (contractValueInput) {
    //     contractValueInput.addEventListener('blur', function() {
    //         const value = parseInt(this.value.replace(/\D/g, ''));
    //         if (!isNaN(value)) {
    //             this.value = value.toLocaleString('vi-VN');
    //         }
    //     });
        
    //     contractValueInput.addEventListener('focus', function() {
    //         this.value = this.value.replace(/\D/g, '');
    //     });
    // }
    
    // Auto-resize textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
});
</script>
@endsection