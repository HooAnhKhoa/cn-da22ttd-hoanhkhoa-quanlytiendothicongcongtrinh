@extends('layouts.app')

@section('title', 'Thêm báo cáo tiến độ')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <nav class="mb-4">
                @if(request('task_id'))
                    <a href="{{ route('admin.tasks.show', request('task_id')) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Quay lại công việc
                    </a>
                @else
                    <a href="{{ route('admin.tasks.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách
                    </a>
                @endif
            </nav>
            <h1 class="text-2xl font-bold text-gray-800">Thêm báo cáo tiến độ</h1>
            <p class="text-gray-600 mt-2">Điền thông tin báo cáo tiến độ công việc</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.progress_updates.store') }}" method="POST" enctype="multipart/form-data" id="progressForm">
                @csrf
                
                @php
                    $currentTask = null;
                    $currentProgress = 0;
                    
                    if(request('task_id') && isset($tasks) && $tasks->count() > 0) {
                        $currentTask = $tasks->first();
                        $currentProgress = $currentTask ? $currentTask->progress_percent : 0;
                    }
                @endphp
                
                @if(request('task_id') && $currentTask)
                    <input type="hidden" name="task_id" value="{{ request('task_id') }}">
                    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Công việc:</label>
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-3 rounded-lg mr-3">
                                <i class="fas fa-tasks text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">{{ $currentTask->task_name }}</p>
                                <div class="mt-2">
                                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                                        <span>Tiến độ hiện tại</span>
                                        <span id="current-progress-text" class="font-bold text-blue-700">{{ $currentProgress }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div id="current-progress-bar" 
                                             class="bg-blue-600 h-2.5 rounded-full transition-all duration-500"
                                             style="width: {{ $currentProgress }}%"></div>
                                    </div>
                                </div>
                                @if($currentTask->site)
                                    <p class="text-sm text-gray-500 mt-2">
                                        <i class="fas fa-hard-hat mr-1"></i>
                                        Công trình: {{ $currentTask->site->site_name }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mb-4">
                        <label for="task_id" class="block text-gray-700 text-sm font-medium mb-2">
                            Công việc <span class="text-red-500">*</span>
                        </label>
                        <select name="task_id" id="task_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                onchange="updateTaskProgress(this.value)">
                            <option value="">Chọn công việc</option>
                            @if(isset($tasks))
                                @foreach($tasks as $task)
                                    <option value="{{ $task->id }}" 
                                            data-progress="{{ $task->progress_percent }}"
                                            data-task-name="{{ $task->task_name }}"
                                            {{ old('task_id') == $task->id ? 'selected' : '' }}>
                                        {{ $task->task_name }} ({{ $task->progress_percent }}%)
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('task_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div class="mb-4">
                    <label for="date" class="block text-gray-700 text-sm font-medium mb-2">
                        Ngày báo cáo <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date" id="date" required
                           value="{{ old('date', date('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           max="{{ date('Y-m-d') }}">
                    @error('date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <label class="block text-gray-700 text-sm font-medium">
                            Tiến độ mới (%) <span class="text-red-500">*</span>
                        </label>
                        <div class="text-sm">
                            <span class="text-gray-600 mr-2">Hiện tại: <span id="display-current-progress" class="font-bold text-blue-700">{{ $currentProgress }}%</span></span>
                            <span class="text-green-600">Mới: <span id="display-new-progress" class="font-bold">0%</span></span>
                        </div>
                    </div>
                    
                    <!-- Progress comparison -->
                    <div class="mb-4 relative">
                        <!-- Current progress line -->
                        <div class="absolute top-1/2 left-0 right-0 transform -translate-y-1/2 z-10">
                            <div id="current-line" 
                                 class="h-1 bg-blue-500 border-l-2 border-blue-700 absolute"
                                 style="width: 0%; left: 0;"></div>
                            <div class="absolute text-xs text-blue-700 font-medium transform -translate-x-1/2"
                                 id="current-line-label"
                                 style="top: -20px; left: 0;"></div>
                        </div>
                        
                        <!-- Progress slider -->
                        <div class="relative z-20">
                            <input type="range" name="progress_percent" id="progress_percent" 
                                   min="{{ $currentProgress }}" max="100" step="1"
                                   value="{{ old('progress_percent', $currentProgress) }}"
                                   class="w-full h-3 bg-gradient-to-r from-gray-300 via-blue-300 to-green-300 rounded-lg appearance-none cursor-pointer [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:h-6 [&::-webkit-slider-thumb]:w-6 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-white [&::-webkit-slider-thumb]:border-2 [&::-webkit-slider-thumb]:border-blue-600 [&::-webkit-slider-thumb]:shadow-lg"
                                   oninput="updateProgressDisplay(this.value)">
                        </div>
                        
                        <!-- Progress markers -->
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>{{ $currentProgress }}%</span>
                            <span class="text-green-600 font-medium">100%</span>
                        </div>
                    </div>
                    
                    <!-- Progress change info -->
                    <div id="progress-change-info" class="p-3 rounded-lg hidden transition-all duration-300">
                        <div class="flex items-center">
                            <i class="fas fa-arrow-up text-green-600 mr-2"></i>
                            <span class="text-sm font-medium">
                                Tăng <span id="progress-increase" class="text-green-700"></span>% 
                                (<span id="progress-from-to"></span>)
                            </span>
                        </div>
                    </div>
                    
                    @error('progress_percent')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 text-sm font-medium mb-2">
                        Mô tả chi tiết
                    </label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Mô tả những gì đã hoàn thành, khó khăn gặp phải, giải pháp...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="attached_files" class="block text-gray-700 text-sm font-medium mb-2">
                        Tệp đính kèm (hình ảnh, tài liệu...)
                    </label>
                    <input type="file" name="attached_files[]" id="attached_files" multiple
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                    <p class="text-sm text-gray-500 mt-1">Có thể chọn nhiều file. Tối đa 10MB/file.</p>
                    <div id="file-list" class="mt-2 space-y-2"></div>
                    @error('attached_files.*')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ request('task_id') ? route('admin.tasks.show', request('task_id')) : route('admin.tasks.index') }}"
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        Hủy
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 border border-transparent rounded-md font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-save mr-2"></i>Lưu báo cáo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Custom range slider styles */
#progress_percent {
    -webkit-appearance: none;
    appearance: none;
    background: transparent;
}

#progress_percent::-webkit-slider-track {
    background: linear-gradient(to right, #d1d5db 0%, var(--current-progress) 0%, #93c5fd var(--current-progress), #6ee7b7 100%);
    height: 8px;
    border-radius: 4px;
}

#progress_percent::-moz-range-track {
    background: linear-gradient(to right, #d1d5db 0%, var(--current-progress) 0%, #93c5fd var(--current-progress), #6ee7b7 100%);
    height: 8px;
    border-radius: 4px;
}

#progress_percent::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    height: 24px;
    width: 24px;
    background-color: white;
    border-radius: 50%;
    border: 2px solid #2563eb;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    cursor: pointer;
    transition: all 0.2s;
}

#progress_percent::-webkit-slider-thumb:hover {
    transform: scale(1.1);
    box-shadow: 0 3px 8px rgba(0,0,0,0.3);
}

#progress_percent::-moz-range-thumb {
    height: 24px;
    width: 24px;
    background-color: white;
    border-radius: 50%;
    border: 2px solid #2563eb;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    cursor: pointer;
    transition: all 0.2s;
}

#progress_percent::-moz-range-thumb:hover {
    transform: scale(1.1);
    box-shadow: 0 3px 8px rgba(0,0,0,0.3);
}

/* Current progress line */
#current-line {
    transition: left 0.3s ease;
}

#current-line::after {
    content: '';
    position: absolute;
    right: -2px;
    top: -4px;
    width: 4px;
    height: 12px;
    background-color: #1e40af;
    border-radius: 1px;
}
</style>
@endpush

@push('scripts')
<script>
// Biến lưu tiến độ hiện tại
let currentProgress = {{ $currentProgress }};

// Cập nhật CSS custom property cho màu nền slider
function updateSliderBackground() {
    const slider = document.getElementById('progress_percent');
    slider.style.setProperty('--current-progress', currentProgress + '%');
}

// Cập nhật hiển thị tiến độ
function updateProgressDisplay(value) {
    const newProgress = parseInt(value);
    const currentLine = document.getElementById('current-line');
    const currentLabel = document.getElementById('current-line-label');
    const progressChangeInfo = document.getElementById('progress-change-info');
    const progressIncrease = document.getElementById('progress-increase');
    const progressFromTo = document.getElementById('progress-from-to');
    
    // Cập nhật số liệu
    document.getElementById('display-new-progress').textContent = newProgress + '%';
    
    // Cập nhật vị trí đường tiến độ hiện tại
    if (currentLine) {
        currentLine.style.left = (currentProgress - 1) + '%';
        currentLine.style.width = '2px';
    }
    
    if (currentLabel) {
        currentLabel.textContent = currentProgress + '%';
        currentLabel.style.left = (currentProgress - 1) + '%';
    }
    
    // Hiển thị thông tin thay đổi nếu có
    if (newProgress > currentProgress) {
        const increase = newProgress - currentProgress;
        progressIncrease.textContent = increase;
        progressFromTo.textContent = currentProgress + '% → ' + newProgress + '%';
        
        // Cập nhật màu sắc dựa trên mức tăng
        let colorClass = 'bg-green-50 border border-green-200';
        if (increase >= 20) {
            colorClass = 'bg-green-100 border border-green-300';
        } else if (increase >= 10) {
            colorClass = 'bg-green-50 border border-green-200';
        }
        
        progressChangeInfo.className = colorClass + ' p-3 rounded-lg transition-all duration-300';
        progressChangeInfo.classList.remove('hidden');
    } else {
        progressChangeInfo.classList.add('hidden');
    }
    
    // Validate form nếu giá trị nhỏ hơn tiến độ hiện tại
    if (newProgress < currentProgress) {
        alert('Tiến độ mới không được nhỏ hơn tiến độ hiện tại (' + currentProgress + '%)!');
        document.getElementById('progress_percent').value = currentProgress;
        updateProgressDisplay(currentProgress);
    }
}

// Cập nhật tiến độ khi chọn task khác
function updateTaskProgress(taskId) {
    const taskSelect = document.getElementById('task_id');
    const selectedOption = taskSelect.options[taskSelect.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        currentProgress = parseInt(selectedOption.dataset.progress) || 0;
        
        // Cập nhật hiển thị
        document.getElementById('display-current-progress').textContent = currentProgress + '%';
        
        // Cập nhật slider
        const progressSlider = document.getElementById('progress_percent');
        progressSlider.min = currentProgress;
        progressSlider.value = currentProgress;
        
        // Cập nhật CSS
        updateSliderBackground();
        
        // Cập nhật hiển thị
        updateProgressDisplay(currentProgress);
    }
}

// Hiển thị danh sách file được chọn
document.getElementById('attached_files').addEventListener('change', function(e) {
    const fileList = document.getElementById('file-list');
    fileList.innerHTML = '';
    
    Array.from(e.target.files).forEach((file, index) => {
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between p-2 bg-gray-50 rounded border border-gray-200';
        div.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-file text-gray-400 mr-2"></i>
                <span class="text-sm text-gray-700">${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
            </div>
            <button type="button" onclick="removeFile(${index})" class="text-red-600 hover:text-red-800">
                <i class="fas fa-times"></i>
            </button>
        `;
        fileList.appendChild(div);
    });
});

function removeFile(index) {
    const input = document.getElementById('attached_files');
    const files = Array.from(input.files);
    files.splice(index, 1);
    
    // Tạo một DataTransfer mới để cập nhật file list
    const dt = new DataTransfer();
    files.forEach(file => dt.items.add(file));
    input.files = dt.files;
    
    // Kích hoạt sự kiện change để cập nhật danh sách hiển thị
    input.dispatchEvent(new Event('change'));
}

// Form validation
document.getElementById('progressForm').addEventListener('submit', function(e) {
    const progressInput = document.getElementById('progress_percent');
    const selectedProgress = parseInt(progressInput.value);
    
    if (selectedProgress < currentProgress) {
        e.preventDefault();
        alert('Lỗi: Tiến độ mới (' + selectedProgress + '%) không được nhỏ hơn tiến độ hiện tại (' + currentProgress + '%)!');
        progressInput.focus();
        return false;
    }
    
    if (selectedProgress > 100) {
        e.preventDefault();
        alert('Lỗi: Tiến độ không được vượt quá 100%!');
        progressInput.focus();
        return false;
    }
    
    // Cảnh báo nếu tăng tiến độ quá nhiều
    const increase = selectedProgress - currentProgress;
    if (increase > 30) {
        if (!confirm('Bạn đang tăng tiến độ lên ' + increase + '%. Tiến độ này có chính xác không?')) {
            e.preventDefault();
            return false;
        }
    }
});

// Khởi tạo khi trang tải
document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo slider background
    updateSliderBackground();
    
    // Khởi tạo hiển thị tiến độ
    updateProgressDisplay({{ old('progress_percent', $currentProgress) }});
    
    // Nếu có task select, cập nhật khi thay đổi
    const taskSelect = document.getElementById('task_id');
    if (taskSelect) {
        taskSelect.addEventListener('change', function() {
            updateTaskProgress(this.value);
        });
        
        // Khởi tạo giá trị ban đầu nếu có task được chọn
        if (taskSelect.value) {
            updateTaskProgress(taskSelect.value);
        }
    }
    
    // Đặt ngày tối đa là hôm nay
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date').max = today;
});
</script>
@endpush
@endsection