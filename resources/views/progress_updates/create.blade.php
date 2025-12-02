@extends('layouts.app')

@section('title', 'Thêm báo cáo tiến độ')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <nav class="mb-4">
                @if(request('task_id'))
                    <a href="{{ route('tasks.show', request('task_id')) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Quay lại công việc
                    </a>
                @else
                    <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách
                    </a>
                @endif
            </nav>
            <h1 class="text-2xl font-bold text-gray-800">Thêm báo cáo tiến độ</h1>
            <p class="text-gray-600 mt-2">Điền thông tin báo cáo tiến độ công việc</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('progress_updates.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                @if(request('task_id') && isset($tasks) && $tasks->count() > 0)
                    <input type="hidden" name="task_id" value="{{ request('task_id') }}">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Công việc:</label>
                        <div class="p-3 bg-gray-50 rounded border border-gray-200">
                            @if($tasks->first())
                                <p class="font-medium">{{ $tasks->first()->task_name }}</p>
                                <p class="text-sm text-gray-500">Tiến độ hiện tại: {{ $tasks->first()->progress_percent }}%</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="mb-4">
                        <label for="task_id" class="block text-gray-700 text-sm font-medium mb-2">
                            Công việc <span class="text-red-500">*</span>
                        </label>
                        <select name="task_id" id="task_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Chọn công việc</option>
                            @if(isset($tasks))
                                @foreach($tasks as $task)
                                    <option value="{{ $task->id }}" {{ old('task_id') == $task->id ? 'selected' : '' }}>
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
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="progress_percent" class="block text-gray-700 text-sm font-medium mb-2">
                        Tiến độ (%) <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-4">
                        <input type="range" name="progress_percent" id="progress_percent" min="0" max="100" step="1"
                               value="{{ old('progress_percent', 0) }}"
                               class="flex-1"
                               oninput="updateProgressValue(this.value)">
                        <span id="progress_value" class="text-2xl font-bold text-blue-600 w-16 text-center">
                            {{ old('progress_percent', 0) }}%
                        </span>
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
                    <a href="{{ request('task_id') ? route('tasks.show', request('task_id')) : route('tasks.index') }}"
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

@push('scripts')
<script>
function updateProgressValue(value) {
    document.getElementById('progress_value').textContent = value + '%';
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

// Khởi tạo giá trị progress
document.addEventListener('DOMContentLoaded', function() {
    const progressInput = document.getElementById('progress_percent');
    updateProgressValue(progressInput.value);
});
</script>
@endpush
@endsection