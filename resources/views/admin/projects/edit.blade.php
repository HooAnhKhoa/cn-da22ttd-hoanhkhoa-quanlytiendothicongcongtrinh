@extends('layouts.app')

@section('title', 'Chỉnh sửa dự án - ' . $project->project_name)

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-start">
        <div>
            <nav class="mb-4">
                <a href="{{ route('admin.projects.show', $project) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800">Chỉnh sửa dự án</h1>
            <p class="text-xl text-gray-600 mt-2">{{ $project->project_name }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-edit mr-2"></i>Thông tin dự án
        </h2>
    </div>
    
    <div class="p-6">
        <form action="{{ route('admin.projects.update', $project) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tên dự án -->
                <div>
                    <label for="project_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Tên dự án <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="project_name" 
                           id="project_name"
                           value="{{ old('project_name', $project->project_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('project_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Địa điểm -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                        Địa điểm <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="location" 
                           id="location"
                           value="{{ old('location', $project->location) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ngày bắt đầu -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Ngày bắt đầu <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="start_date" 
                           id="start_date"
                           value="{{ old('start_date', optional($project->start_date)->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ngày kết thúc -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Ngày kết thúc
                    </label>
                    <input type="date" 
                           name="end_date" 
                           id="end_date"
                           value="{{ old('end_date', optional($project->end_date)->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ngân sách -->
                <div>
                    <label for="total_budget" class="block text-sm font-medium text-gray-700 mb-2">
                        Ngân sách (VNĐ) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="total_budget" 
                           id="total_budget"
                           value="{{ old('total_budget', $project->total_budget) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           required
                           min="0"
                           step="1000">
                    @error('total_budget')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Trạng thái -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Trạng thái <span class="text-red-500">*</span>
                    </label>
                    <select name="status" 
                            id="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            required>
                        @foreach(\App\Models\Project::getStatuses() as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $project->status) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Mô tả -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Mô tả dự án
                </label>
                <textarea name="description" 
                          id="description"
                          rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('description', $project->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Đội ngũ dự án -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-users mr-2"></i>Đội ngũ dự án
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Chủ đầu tư -->
                    <div>
                        <label for="owner_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Chủ đầu tư <span class="text-red-500">*</span>
                        </label>
                        <select name="owner_id" 
                                id="owner_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="">-- Chọn chủ đầu tư --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('owner_id', optional($project->owner)->id) == $user->id ? 'selected' : '' }}>
                                {{-- <option value="{{ $user->id }}" {{ old('owner_id', $project->owner_id) == $user->id ? 'selected' : '' }}> --}}
                                    {{ $user->username }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('owner_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nhà thầu -->
                    <div>
                        <label for="contractor_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Nhà thầu <span class="text-red-500">*</span>
                        </label>
                        <select name="contractor_id" 
                                id="contractor_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="">-- Chọn nhà thầu --</option>
                            @foreach($contractors as $contractor)
                                <option value="{{ $contractor->id }}" 
                                    {{ old('contractor_id', $project->contractor_id) == $contractor->id ? 'selected' : '' }}>
                                    {{ $contractor->username }} ({{ $contractor->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('contractor_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kỹ sư chính -->
                    <div>
                        <label for="engineer_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Kỹ sư chính <span class="text-red-500">*</span>
                        </label>
                        <select name="engineer_id" 
                                id="engineer_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="">-- Chọn kỹ sư chính --</option>
                            @foreach($engineers as $engineer)
                                <option value="{{ $engineer->id }}" {{ old('engineer_id', $project->engineer_id) == $engineer->id ? 'selected' : '' }}>
                                    {{ $engineer->username }} ({{ $engineer->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('engineer_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Nút hành động -->
            <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.projects.show', $project) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-400 transition-colors">
                    <i class="fas fa-times mr-2"></i>Hủy bỏ
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Cập nhật dự án
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Form xóa dự án -->
<div class="mt-6 bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-red-600">
            <i class="fas fa-exclamation-triangle mr-2"></i>Vùng nguy hiểm
        </h2>
    </div>
    <div class="p-6">
        <p class="text-gray-600 mb-4">Khi xóa dự án, tất cả dữ liệu liên quan sẽ bị mất vĩnh viễn. Hành động này không thể hoàn tác.</p>
        <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa dự án này?')">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 transition-colors">
                <i class="fas fa-trash mr-2"></i>Xóa dự án
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Format số tiền khi nhập
    const budgetInput = document.getElementById('total_budget');
    if (budgetInput) {
        budgetInput.addEventListener('blur', function() {
            const value = parseInt(this.value);
            if (!isNaN(value)) {
                this.value = value.toLocaleString('vi-VN');
            }
        });
        
        budgetInput.addEventListener('focus', function() {
            this.value = this.value.replace(/\./g, '');
        });
    }
});
</script>
@endpush