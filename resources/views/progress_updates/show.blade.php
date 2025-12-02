@extends('layouts.app')

@section('title', 'Chi tiết báo cáo tiến độ')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <nav class="mb-4">
                <a href="{{ route('tasks.show', $progressUpdate->task_id) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại công việc
                </a>
            </nav>
            <h1 class="text-2xl font-bold text-gray-800">Chi tiết báo cáo tiến độ</h1>
            <p class="text-gray-600 mt-2">Ngày: {{ $progressUpdate->date->format('d/m/Y') }}</p>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">
                            <i class="fas fa-chart-line mr-2"></i>Báo cáo tiến độ
                        </h2>
                        <p class="text-gray-600 text-sm mt-1">
                            Công việc: <a href="{{ route('tasks.show', $progressUpdate->task_id) }}" 
                                         class="font-medium text-blue-600 hover:text-blue-800">
                                {{ $progressUpdate->task->task_name ?? 'N/A' }}
                            </a>
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('progress_updates.edit', $progressUpdate->id) }}"
                           class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-medium text-white hover:bg-yellow-700 transition-colors">
                            <i class="fas fa-edit mr-2"></i>Sửa
                        </a>
                        <form action="{{ route('progress_updates.destroy', $progressUpdate->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Xóa báo cáo này?')"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-medium text-white hover:bg-red-700 transition-colors">
                                <i class="fas fa-trash mr-2"></i>Xóa
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Progress Card -->
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-tachometer-alt text-blue-500 text-xl mr-3"></i>
                            <h3 class="text-lg font-bold text-gray-800">Tiến độ</h3>
                        </div>
                        <div class="text-center">
                            <div class="text-5xl font-bold text-blue-600 mb-2">
                                {{ $progressUpdate->progress_percent }}%
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                                <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" 
                                     style="width: {{ $progressUpdate->progress_percent }}%"></div>
                            </div>
                            <p class="text-sm text-gray-600">Tiến độ tại thời điểm báo cáo</p>
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-3">Thông tin báo cáo</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Ngày báo cáo:</span>
                                <span class="font-medium">{{ $progressUpdate->date->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Người báo cáo:</span>
                                <span class="font-medium">{{ $progressUpdate->created_by }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Thời gian tạo:</span>
                                <span class="font-medium">{{ $progressUpdate->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Cập nhật cuối:</span>
                                <span class="font-medium">{{ $progressUpdate->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if($progressUpdate->description)
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-align-left text-gray-400 mr-2"></i>
                        Mô tả chi tiết
                    </h3>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-gray-700 whitespace-pre-line">{{ $progressUpdate->description }}</p>
                    </div>
                </div>
                @endif

                <!-- Attached Files -->
                @php
                    // Xử lý file đính kèm an toàn
                    $files = [];
                    if ($progressUpdate->attached_files) {
                        if (is_array($progressUpdate->attached_files)) {
                            $files = $progressUpdate->attached_files;
                        } elseif (is_string($progressUpdate->attached_files)) {
                            $decoded = json_decode($progressUpdate->attached_files, true);
                            $files = is_array($decoded) ? $decoded : [];
                        }
                    }
                    $files = array_filter($files, function($file) {
                        return is_string($file) && !empty($file);
                    });
                @endphp
                
                @if(count($files) > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-paperclip text-gray-400 mr-2"></i>
                        Tệp đính kèm ({{ count($files) }})
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($files as $file)
                            @php
                                $ext = pathinfo($file, PATHINFO_EXTENSION);
                                $icon = 'fa-file';
                                $color = 'text-gray-500';
                                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                                    $icon = 'fa-image';
                                    $color = 'text-green-500';
                                } elseif ($ext == 'pdf') {
                                    $icon = 'fa-file-pdf';
                                    $color = 'text-red-500';
                                } elseif (in_array($ext, ['doc', 'docx'])) {
                                    $icon = 'fa-file-word';
                                    $color = 'text-blue-500';
                                } elseif (in_array($ext, ['xls', 'xlsx'])) {
                                    $icon = 'fa-file-excel';
                                    $color = 'text-green-500';
                                }
                                $filename = basename($file);
                            @endphp
                            <div class="bg-white border border-gray-200 rounded-lg p-3 hover:shadow-md transition-shadow">
                                <div class="flex items-center mb-2">
                                    <i class="fas {{ $icon }} {{ $color }} text-xl mr-3"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate" title="{{ $filename }}">
                                            {{ $filename }}
                                        </p>
                                        <p class="text-xs text-gray-500 uppercase">{{ $ext }}</p>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2">
                                    @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
                                    <a href="{{ Storage::url($file) }}" target="_blank"
                                       class="inline-flex items-center px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors">
                                        <i class="fas fa-eye mr-1"></i> Xem
                                    </a>
                                    @endif
                                    <a href="{{ route('progress_updates.download', ['id' => $progressUpdate->id, 'filename' => $file]) }}"
                                       class="inline-flex items-center px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">
                                        <i class="fas fa-download mr-1"></i> Tải
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection