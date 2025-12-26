@extends('layouts.app')

@section('title', 'Tạo Công Trường Mới')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div class="w-full">
                <nav class="mb-4">
                    <a href="{{ request('project_id') 
                            ? route('client.projects.show', request('project_id')) 
                            : route('client.sites.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Quay lại
                    </a>

                </nav>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Thêm công trường mới</h1>
                <p class="text-gray-600">Tạo công trường xây dựng mới trong hệ thống</p>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            
            <form action="{{ route('client.sites.store') }}" method="POST" class="p-8">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Cột trái -->
                    <div class="space-y-6">
                        <!-- Tên công trường -->
                        <div>
                            <label for="site_name" class="block text-lg font-semibold text-gray-800 mb-3">
                                Tên công trường <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="site_name" 
                                id="site_name"
                                value="{{ old('site_name') }}"
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                placeholder="Nhập tên công trường..."
                                required
                            >
                            @error('site_name')
                                <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- resources/views/client/sites/create.blade.php --}}
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
                                {{ $selectedProject ? 'disabled' : '' }}
                            >
                                <option value="">Chọn dự án</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" 
                                        {{ old('project_id', $selectedProject ? $selectedProject->id : null) == $project->id ? 'selected' : '' }}>
                                        {{ $project->project_name }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <!-- Nếu đã chọn dự án từ trước, thêm hidden input -->
                            @if($selectedProject)
                                <input type="hidden" name="project_id" value="{{ $selectedProject->id }}">
                            @endif
                            
                            @error('project_id')
                                <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Mô tả -->
                        <div>
                            <label for="description" class="block text-lg font-semibold text-gray-800 mb-3">
                                Mô tả công trường
                            </label>
                            <textarea 
                                name="description" 
                                id="description"
                                rows="5"
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                placeholder="Mô tả chi tiết về công trường..."
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
                                placeholder="Nhập địa điểm công trường..."
                                required
                            >
                            @error('location')
                                <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Cột phải -->
                    <div class="space-y-6">
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

                        <!-- Tiến độ -->
                        <div>
                            <label for="progress_percent" class="block text-lg font-semibold text-gray-800 mb-3">
                                Tiến độ (%) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="number" 
                                    name="progress_percent" 
                                    id="progress_percent"
                                    value="{{ old('progress_percent', 0) }}"
                                    class="w-full px-4 py-3 pr-12 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                    placeholder="0"
                                    min="0"
                                    max="100"
                                    required
                                >
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-gray-500 font-medium">%</span>
                                </div>
                            </div>
                            @error('progress_percent')
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
{{-- 
                        <!-- Thông tin nhân sự -->
                        <div class="space-y-4">
                            <h3 class="text-xl font-semibold text-gray-800 border-b pb-2">Thông tin nhân sự</h3>
                            
                            <!-- Kỹ sư chính -->
                            <div>
                                <label for="engineer_id" class="block text-lg font-medium text-gray-700 mb-2">
                                    Kỹ sư chính
                                </label>
                                <select 
                                    name="engineer_id" 
                                    id="engineer_id"
                                    class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                >
                                    <option value="">Chọn kỹ sư chính</option>
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

                            <!-- Giám sát viên -->
                            <div>
                                <label for="contractor" class="block text-lg font-medium text-gray-700 mb-2">
                                    Giám sát viên
                                </label>
                                <select 
                                    name="contractor" 
                                    id="contractor"
                                    class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                >
                                    <option value="">Chọn giám sát viên</option>
                                    @foreach($contractor as $contractor)
                                        <option value="{{ $contractor->id }}" {{ old('contractor') == $contractor->id ? 'selected' : '' }}>
                                            {{ $contractor->username }} ({{ $contractor->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('contractor')
                                    <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div> --}}
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
                        Tạo công trường
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