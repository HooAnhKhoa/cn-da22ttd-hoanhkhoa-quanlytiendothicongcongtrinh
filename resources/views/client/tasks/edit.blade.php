@extends('layouts.app')

@section('title', 'Chỉnh sửa công việc - ' . $task->task_name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div class="w-full">
                <nav class="mb-4">
                    <a href="{{ route('client.tasks.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách
                    </a>
                </nav>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Chỉnh sửa công việc</h1>
                <p class="text-gray-600">{{ $task->task_name }}</p>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            
            <form action="{{ route('client.tasks.update', $task) }}" method="POST" class="p-8" id="taskForm">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Cột trái -->
                    <div class="space-y-6">
                        <!-- Site/Công trình -->
                        <div>
                            <label for="site_id" class="block text-lg font-semibold text-gray-800 mb-3">
                                Công trình <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="site_id" 
                                id="site_id"
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                required
                                onchange="updateParentTasks()"
                            >
                                <option value="">Chọn công trình</option>
                                @if(isset($sites) && $sites->count() > 0)
                                    @foreach($sites as $site)
                                        <option value="{{ $site->id }}" {{ old('site_id', $task->site_id) == $site->id ? 'selected' : '' }}>
                                            {{ $site->site_name ?? $site->name ?? 'Công trình #' . $site->id }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('site_id')
                                <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tên công việc -->
                        <div>
                            <label for="task_name" class="block text-lg font-semibold text-gray-800 mb-3">
                                Tên công việc <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="task_name" 
                                id="task_name"
                                value="{{ old('task_name', $task->task_name) }}"
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                placeholder="Nhập tên công việc..."
                                required
                            >
                            @error('task_name')
                                <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Công việc cha -->
                        <div>
                            <label for="parent_id" class="block text-lg font-semibold text-gray-800 mb-3">
                                Công việc cha
                            </label>
                            <select 
                                name="parent_id" 
                                id="parent_id"
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all bg-white"
                                @if(!$task->site_id) disabled @endif
                            >
                                <option value="">-- Không có công việc cha (công việc gốc) --</option>
                                @if(isset($parentTasks) && $parentTasks->count() > 0)
                                    @foreach($parentTasks as $parentTask)
                                        <!-- Loại trừ chính task hiện tại và các task con của nó -->
                                        @if($parentTask->id != $task->id)
                                            <option value="{{ $parentTask->id }}" {{ old('parent_id', $task->parent_id) == $parentTask->id ? 'selected' : '' }}>
                                                {{ $parentTask->task_name }}
                                            </option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            <div id="parentTaskMessage" class="mt-1 text-sm text-gray-500">
                                @if($task->site_id)
                                    Có {{ $parentTasks->count() }} công việc trong công trình này
                                @else
                                    Chọn công trình để xem danh sách công việc
                                @endif
                            </div>
                            @error('parent_id')
                                <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Mô tả -->
                        <div>
                            <label for="description" class="block text-lg font-semibold text-gray-800 mb-3">
                                Mô tả công việc
                            </label>
                            <textarea 
                                name="description" 
                                id="description"
                                rows="4"
                                class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                placeholder="Mô tả chi tiết về công việc..."
                            >{{ old('description', $task->description) }}</textarea>
                            @error('description')
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
                                    value="{{ old('start_date', optional($task->start_date)->format('Y-m-d')) }}"
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
                                    value="{{ old('end_date', optional($task->end_date)->format('Y-m-d')) }}"
                                    class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                >
                                @error('end_date')
                                    <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Thông tin tiến độ -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Thời lượng dự kiến -->
                            <div>
                                <label for="planned_duration" class="block text-lg font-semibold text-gray-800 mb-3">
                                    Thời lượng dự kiến (ngày)
                                </label>
                                <input 
                                    type="number" 
                                    name="planned_duration" 
                                    id="planned_duration"
                                    value="{{ old('planned_duration', $task->planned_duration) }}"
                                    class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                    placeholder="0"
                                    min="0"
                                    step="1"
                                >
                                @error('planned_duration')
                                    <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tiến độ -->
                            <div>
                                <label for="progress_percent" class="block text-lg font-semibold text-gray-800 mb-3">
                                    Tiến độ (%)
                                </label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        name="progress_percent" 
                                        id="progress_percent"
                                        value="{{ old('progress_percent', $task->progress_percent ?? 0) }}"
                                        class="w-full px-4 py-3 pr-12 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all"
                                        placeholder="0"
                                        min="0"
                                        max="100"
                                        step="1"
                                        readonly
                                    >
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <span class="text-gray-500 font-medium">%</span>
                                    </div>
                                </div>
                                @error('progress_percent')
                                    <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
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
                                <option value="planned" {{ old('status', $task->status) == 'planned' ? 'selected' : '' }}>Đã lập kế hoạch</option>
                                <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>Đang thực hiện</option>
                                <option value="on_hold" {{ old('status', $task->status) == 'on_hold' ? 'selected' : '' }}>Tạm dừng</option>
                                <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="cancelled" {{ old('status', $task->status) == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                            @error('status')
                                <p class="mt-2 text-base text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Thông tin bổ sung -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Thông tin hệ thống</h3>
                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span>ID công việc:</span>
                                    <span class="font-medium">{{ $task->id }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Ngày tạo:</span>
                                    <span class="font-medium">{{ $task->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Lần cập nhật cuối:</span>
                                    <span class="font-medium">{{ $task->updated_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @if($task->actual_duration)
                                <div class="flex justify-between">
                                    <span>Thời lượng thực tế:</span>
                                    <span class="font-medium">{{ $task->actual_duration }} ngày</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="mt-10 flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('client.tasks.show', $task) }}" 
                        class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all text-lg font-semibold">
                        Hủy bỏ
                    </a>
                    <button 
                        type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all text-lg font-semibold shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-save mr-2"></i>
                        Cập nhật công việc
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form xóa công việc -->
<div class="max-w-6xl mx-auto mt-6">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-red-600">
                <i class="fas fa-exclamation-triangle mr-2"></i>Vùng nguy hiểm
            </h2>
        </div>
        <div class="p-8">
            <p class="text-gray-600 mb-4">
                Khi xóa công việc này, tất cả công việc con và dữ liệu liên quan sẽ bị mất vĩnh viễn. 
                Hành động này không thể hoàn tác.
            </p>
            <form action="{{ route('client.tasks.destroy', $task) }}" method="POST" 
                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa công việc này và tất cả công việc con?')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-red-600 border border-transparent rounded-xl font-semibold text-white hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash mr-2"></i>Xóa công việc
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set min date for start_date
    const today = new Date().toISOString().split('T')[0];
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const plannedDurationInput = document.getElementById('planned_duration');
    
    if (startDateInput) {
        startDateInput.min = today;
    }
    
    // Tính toán ngày kết thúc và thời lượng
    setupDateCalculations(startDateInput, endDateInput, plannedDurationInput);
    
    // Auto-resize textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });

    // Progress percent validation
    const progressInput = document.getElementById('progress_percent');
    if (progressInput) {
        progressInput.addEventListener('change', function() {
            let value = parseInt(this.value);
            if (isNaN(value)) value = 0;
            if (value < 0) value = 0;
            if (value > 100) value = 100;
            this.value = value;
        });
    }
});

function setupDateCalculations(startDateInput, endDateInput, plannedDurationInput) {
    if (startDateInput && endDateInput) {
        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value;
            
            // Auto-calculate end date based on planned duration
            if (plannedDurationInput && plannedDurationInput.value) {
                const startDate = new Date(this.value);
                const duration = parseInt(plannedDurationInput.value);
                if (!isNaN(duration) && duration > 0) {
                    const endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + duration);
                    endDateInput.value = endDate.toISOString().split('T')[0];
                }
            }
        });
    }
    
    // Auto-calculate end date when planned duration changes
    if (plannedDurationInput && startDateInput && endDateInput) {
        plannedDurationInput.addEventListener('change', function() {
            const duration = parseInt(this.value);
            const startDate = startDateInput.value;
            
            if (!isNaN(duration) && duration > 0 && startDate) {
                const start = new Date(startDate);
                const endDate = new Date(start);
                endDate.setDate(start.getDate() + duration);
                endDateInput.value = endDate.toISOString().split('T')[0];
            }
        });
    }
    
    // Auto-calculate planned duration when end date changes
    if (endDateInput && startDateInput && plannedDurationInput) {
        endDateInput.addEventListener('change', function() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(this.value);
            
            if (startDate && endDate && endDate > startDate) {
                const timeDiff = endDate.getTime() - startDate.getTime();
                const dayDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
                plannedDurationInput.value = dayDiff;
            }
        });
    }
}

// Hàm cập nhật danh sách công việc cha (nếu dùng AJAX)
function updateParentTasks() {
    const siteSelect = document.getElementById('site_id');
    const parentSelect = document.getElementById('parent_id');
    const messageDiv = document.getElementById('parentTaskMessage');
    
    if (!siteSelect || !parentSelect || !messageDiv) return;
    
    const selectedSiteId = siteSelect.value;
    const currentTaskId = "{{ $task->id }}"; // ID của task đang chỉnh sửa
    
    if (selectedSiteId) {
        // Kích hoạt select
        parentSelect.disabled = false;
        parentSelect.classList.remove('bg-gray-100');
        parentSelect.classList.add('bg-white');
        
        // Gọi API để lấy danh sách công việc của công trình
        fetch(`/api/sites/${selectedSiteId}/tasks?exclude=${currentTaskId}`)
            .then(response => response.json())
            .then(tasks => {
                // Xóa tất cả option cũ (giữ lại option đầu tiên)
                while (parentSelect.options.length > 0) {
                    parentSelect.remove(0);
                }
                
                // Thêm option mặc định
                const defaultOption = document.createElement('option');
                defaultOption.value = "";
                defaultOption.textContent = "-- Không có công việc cha (công việc gốc) --";
                parentSelect.appendChild(defaultOption);
                
                // Thêm các công việc của công trình
                if (tasks.length > 0) {
                    tasks.forEach(task => {
                        const option = document.createElement('option');
                        option.value = task.id;
                        option.textContent = task.task_name;
                        parentSelect.appendChild(option);
                    });
                    
                    messageDiv.innerHTML = `<span class="text-green-600">✓ Có ${tasks.length} công việc trong công trình này</span>`;
                } else {
                    messageDiv.innerHTML = `<span class="text-yellow-600">ℹ Không có công việc nào trong công trình này</span>`;
                }
                
                // Chọn lại giá trị cũ nếu có
                const oldParentId = "{{ old('parent_id', $task->parent_id) }}";
                if (oldParentId) {
                    parentSelect.value = oldParentId;
                }
            })
            .catch(error => {
                console.error('Error fetching tasks:', error);
                messageDiv.innerHTML = `<span class="text-red-600">✗ Lỗi khi tải danh sách công việc</span>`;
            });
    } else {
        // Vô hiệu hóa select nếu chưa chọn công trình
        parentSelect.disabled = true;
        parentSelect.classList.remove('bg-white');
        parentSelect.classList.add('bg-gray-100');
        
        // Chỉ giữ lại option đầu tiên
        while (parentSelect.options.length > 0) {
            parentSelect.remove(0);
        }
        
        const defaultOption = document.createElement('option');
        defaultOption.value = "";
        defaultOption.textContent = "-- Vui lòng chọn công trình trước --";
        parentSelect.appendChild(defaultOption);
        
        messageDiv.textContent = "Chọn công trình để xem danh sách công việc";
    }
}
</script>

<style>
select:disabled {
    background-color: #f9fafb;
    color: #9ca3af;
    cursor: not-allowed;
}
</style>
@endsection