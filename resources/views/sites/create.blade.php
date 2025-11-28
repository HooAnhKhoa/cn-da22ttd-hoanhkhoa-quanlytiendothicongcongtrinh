@extends('layouts.app')

@section('title', 'Tạo Dự Án Mới')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div class="w-full">
                <nav class="mb-4">
                    <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Quay lại
                    </a>
                </nav>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Thêm dự án mới</h1>
                <p class="text-gray-600">Tạo dự án xây dựng mới trong hệ thống</p>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            
            <form action="{{ route('projects.store') }}" method="POST" class="p-8">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Cột trái -->
                    <div class="space-y-6">
                        <!-- Tên dự án -->
                        <div>
                            <label for="project_name" class="block text-lg font-semibold text-gray-800 mb-3">
                                Tên dự án <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="project_name" 
                                id="project_name"
                                value="{{ old('project_name') }}"
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                placeholder="Nhập tên dự án..."
                                required
                            >
                            @error('project_name')
                                <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Mô tả -->
                        <div>
                            <label for="description" class="block text-lg font-semibold text-gray-800 mb-3">
                                Mô tả dự án
                            </label>
                            <textarea 
                                name="description" 
                                id="description"
                                rows="5"
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                placeholder="Mô tả chi tiết về dự án..."
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Địa điểm -->
                        <div>
                            <label for="location" class="block text-lg font-semibold text-gray-800 mb-3">
                                Địa điểm <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="location" 
                                id="location"
                                value="{{ old('location') }}"
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                placeholder="Nhập địa điểm dự án..."
                                required
                            >
                            @error('location')
                                <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Thông tin thời gian -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Ngày bắt đầu -->
                            <div>
                                <label for="start_date" class="block text-lg font-semibold text-gray-800 mb-3">
                                    Ngày bắt đầu <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    name="start_date" 
                                    id="start_date"
                                    value="{{ old('start_date') }}"
                                    class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                    required
                                >
                                @error('start_date')
                                    <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Ngày kết thúc dự kiến -->
                            <div>
                                <label for="end_date" class="block text-lg font-semibold text-gray-800 mb-3">
                                    Ngày kết thúc dự kiến
                                </label>
                                <input 
                                    type="date" 
                                    name="end_date" 
                                    id="end_date"
                                    value="{{ old('end_date') }}"
                                    class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                >
                                @error('end_date')
                                    <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Cột phải -->
                    <div class="space-y-6">
                        <!-- Ngân sách -->
                        <div>
                            <label for="total_budget" class="block text-lg font-semibold text-gray-800 mb-3">
                                Ngân sách (VND) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="number" 
                                    name="total_budget" 
                                    id="total_budget"
                                    value="{{ old('total_budget') }}"
                                    class="w-full px-4 py-3 pr-12 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                    placeholder="0"
                                    min="0"
                                    step="1000"
                                    required
                                >
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-gray-500 font-medium">₫</span>
                                </div>
                            </div>
                            @error('total_budget')
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
                                <option value="planned" {{ old('status') == 'planned' ? 'selected' : '' }}>Lập kế hoạch</option>
                                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>Đang thi công</option>
                                <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>Tạm dừng</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                            @error('status')
                                <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Thông tin người dùng -->
                        <div class="space-y-4">
                            <h3 class="text-xl font-semibold text-gray-800 border-b pb-2">Thông tin người dùng</h3>
                            
                            <!-- Chủ đầu tư -->
                            <div>
                                <label for="owner_id" class="block text-lg font-medium text-gray-700 mb-2">
                                    Chủ đầu tư <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="owner_id" 
                                    id="owner_id"
                                    class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                    required
                                >
                                    <option value="">Chọn chủ đầu tư</option>
                                    @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}" {{ old('owner_id') == $owner->id ? 'selected' : '' }}>
                                            {{ $owner->username }} ({{ $owner->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('owner_id')
                                    <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nhà thầu -->
                            <div>
                                <label for="contractor_id" class="block text-lg font-medium text-gray-700 mb-2">
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
                                            {{ $contractor->username }} ({{ $contractor->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('contractor_id')
                                    <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kỹ sư -->
                            <div>
                                <label for="engineer_id" class="block text-lg font-medium text-gray-700 mb-2">
                                    Kỹ sư <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="engineer_id" 
                                    id="engineer_id"
                                    class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                    required
                                >
                                    <option value="">Chọn kỹ sư</option>
                                    @foreach($engineers as $engineer)
                                        <option value="{{ $engineer->id }}" {{ old('engineer_id') == $engineer->id ? 'selected' : '' }}>
                                            {{ $engineer->username }} ({{ $engineer->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('engineer_id')
                                    <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="mt-10 flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <button 
                        type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all text-lg font-semibold shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-plus-circle mr-2"></i>
                        Tạo dự án
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set min date for start_date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('start_date').min = today;
    
    // Set min date for end_date based on start_date
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
    });

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

<style>
/* Custom scrollbar for select multiple */
select[multiple] {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

select[multiple]::-webkit-scrollbar {
    width: 8px;
}

select[multiple]::-webkit-scrollbar-track {
    background: #f7fafc;
    border-radius: 4px;
}

select[multiple]::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 4px;
}

select[multiple]::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}
</style>
@endsection