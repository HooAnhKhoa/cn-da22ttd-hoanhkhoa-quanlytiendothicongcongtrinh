@extends('layouts.app')

@section('title', 'Chi tiết báo cáo tiến độ')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-start">
        <div>
            <nav class="mb-4">
                <a href="{{ route('admin.progress_updates.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách báo cáo
                </a>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800">Báo cáo tiến độ - {{ $progressUpdate->date->format('d/m/Y') }}</h1>
            <p class="text-xl text-gray-600 mt-2">
                Công việc: <a href="{{ route('admin.tasks.show', $progressUpdate->task_id) }}" class="text-blue-600 hover:text-blue-800">
                    {{ $progressUpdate->task->task_name ?? 'N/A' }}
                </a>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.progress_updates.edit', $progressUpdate->id) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
                <i class="fas fa-edit mr-2"></i>Chỉnh sửa
            </a>
        </div>
    </div>
</div>

<!-- Thông báo -->
@include('components.alert')

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Progress Information -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-info-circle mr-2"></i>Thông tin báo cáo
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngày báo cáo:</span>
                    <span class="font-medium text-gray-800">{{ $progressUpdate->date->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tiến độ:</span>
                    <span class="font-medium text-gray-800">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $progressUpdate->progress_percent }}%
                        </span>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Người báo cáo:</span>
                    <span class="font-medium text-gray-800">
                        @if($progressUpdate->reporter)
                            {{ $progressUpdate->reporter->name ?? $progressUpdate->reporter->username ?? 'N/A' }}
                        @else
                            {{ $progressUpdate->created_by ?? 'N/A' }}
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Công việc:</span>
                    <span class="font-medium text-gray-800">
                        <a href="{{ route('admin.tasks.show', $progressUpdate->task_id) }}" class="text-blue-600 hover:text-blue-800">
                            {{ $progressUpdate->task->task_name ?? 'N/A' }}
                        </a>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Dự án:</span>
                    <span class="font-medium text-gray-800">
                        @if($progressUpdate->task && $progressUpdate->task->site && $progressUpdate->task->site->project)
                        <a href="{{ route('admin.projects.show', $progressUpdate->task->site->project) }}" class="text-blue-600 hover:text-blue-800">
                            {{ $progressUpdate->task->site->project->project_name }}
                        </a>
                        @else
                        <span class="text-gray-500">N/A</span>
                        @endif
                    </span>
                </div>
                
                <!-- Progress Bar -->
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-chart-line text-blue-500 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Tiến độ công việc</span>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-bold text-blue-600">{{ $progressUpdate->progress_percent }}%</span>
                        <p class="text-xs text-gray-500">Hoàn thành</p>
                    </div>
                </div>
                
                <!-- Progress Visualization -->
                <div class="pt-4">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">0%</span>
                        <span class="text-sm font-medium text-gray-700">100%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" 
                             style="width: {{ $progressUpdate->progress_percent }}%"></div>
                    </div>
                </div>
            </div>
            
            @if($progressUpdate->description)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-2">Mô tả chi tiết:</h4>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-600 whitespace-pre-line">{{ $progressUpdate->description }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Task Information & Files -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-tasks mr-2"></i>Thông tin công việc
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <!-- Task Details -->
                @if($progressUpdate->task)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">
                                <a href="{{ route('admin.tasks.show', $progressUpdate->task) }}" class="hover:text-blue-600">
                                    {{ $progressUpdate->task->task_name }}
                                </a>
                            </div>
                            <div class="text-sm text-gray-500">Công việc</div>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">
                        {{ $progressUpdate->task->task_type }}
                    </span>
                </div>

                <!-- Site -->
                @if($progressUpdate->task->site)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">
                                <a href="{{ route('admin.sites.show', $progressUpdate->task->site) }}" class="hover:text-blue-600">
                                    {{ $progressUpdate->task->site->site_name }}
                                </a>
                            </div>
                            <div class="text-sm text-gray-500">Công trường</div>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ $progressUpdate->task->site->location ?? '' }}</span>
                </div>
                @endif

                <!-- Project -->
                @if($progressUpdate->task->site && $progressUpdate->task->site->project)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">
                                <a href="{{ route('admin.projects.show', $progressUpdate->task->site->project) }}" class="hover:text-blue-600">
                                    {{ $progressUpdate->task->site->project->project_name }}
                                </a>
                            </div>
                            <div class="text-sm text-gray-500">Dự án</div>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ $progressUpdate->task->site->project->location ?? '' }}</span>
                </div>
                @endif
                
                <!-- Reporter -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">
                                @if($progressUpdate->reporter)
                                    {{ $progressUpdate->reporter->name ?? $progressUpdate->reporter->username ?? 'N/A' }}
                                @else
                                    {{ $progressUpdate->created_by ?? 'N/A' }}
                                @endif
                            </div>
                            <div class="text-sm text-gray-500">Người báo cáo</div>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">
                        @if($progressUpdate->reporter && $progressUpdate->reporter->user_type)
                            @php
                                $userTypes = [
                                    'owner' => 'Chủ đầu tư',
                                    'contractor' => 'Nhà thầu',
                                    'engineer' => 'Kỹ sư',
                                    'admin' => 'Quản trị viên'
                                ];
                            @endphp
                            {{ $userTypes[$progressUpdate->reporter->user_type] ?? $progressUpdate->reporter->user_type }}
                        @endif
                    </span>
                </div>
                @endif
            </div>
            
            <!-- Statistics Grid -->
            <div class="mt-6 grid grid-cols-2 gap-4">
                <div class="text-center p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-600">Ngày báo cáo</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $progressUpdate->date->format('d/m') }}</p>
                    <p class="text-xs text-gray-500">{{ $progressUpdate->date->format('Y') }}</p>
                </div>
                <div class="text-center p-3 bg-green-50 rounded-lg">
                    <p class="text-sm text-gray-600">Tiến độ</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $progressUpdate->progress_percent }}%</p>
                </div>
                <div class="text-center p-3 bg-yellow-50 rounded-lg">
                    <p class="text-sm text-gray-600">Trạng thái</p>
                    @php
                        $statusColors = [
                            'completed' => 'bg-green-100 text-green-800',
                            'in_progress' => 'bg-blue-100 text-blue-800',
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        $taskStatus = $progressUpdate->task->status ?? 'pending';
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$taskStatus] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $taskStatus }}
                    </span>
                </div>
                <div class="text-center p-3 bg-red-50 rounded-lg">
                    <p class="text-sm text-gray-600">Tệp đính kèm</p>
                    <p class="text-2xl font-bold text-gray-900">
                        @php
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
                        {{ count($files) }}
                    </p>
                </div>
            </div>
            
            <!-- Timeline -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-3">Lịch sử báo cáo</h4>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-plus text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-800 text-sm">Tạo báo cáo</div>
                            <div class="text-xs text-gray-500">{{ $progressUpdate->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-calendar-day text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-800 text-sm">Ngày báo cáo</div>
                            <div class="text-xs text-gray-500">{{ $progressUpdate->date->format('d/m/Y') }}</div>
                        </div>
                    </div>
                    @if($progressUpdate->updated_at->gt($progressUpdate->created_at))
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-edit text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-800 text-sm">Cập nhật cuối</div>
                            <div class="text-xs text-gray-500">{{ $progressUpdate->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách tệp đính kèm -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-paperclip mr-2"></i>Tệp đính kèm
            <span class="text-sm font-normal text-gray-500 ml-2">({{ count($files) }} tệp)</span>
        </h2>
    </div>
    
    <div class="p-6">
        @if(count($files) > 0)
            <!-- Files Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($files as $file)
                    @php
                        $ext = pathinfo($file, PATHINFO_EXTENSION);
                        $icon = 'fa-file';
                        $color = 'bg-gray-100 text-gray-600';
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                            $icon = 'fa-image';
                            $color = 'bg-green-100 text-green-600';
                        } elseif ($ext == 'pdf') {
                            $icon = 'fa-file-pdf';
                            $color = 'bg-red-100 text-red-600';
                        } elseif (in_array($ext, ['doc', 'docx'])) {
                            $icon = 'fa-file-word';
                            $color = 'bg-blue-100 text-blue-600';
                        } elseif (in_array($ext, ['xls', 'xlsx'])) {
                            $icon = 'fa-file-excel';
                            $color = 'bg-green-100 text-green-600';
                        }
                        $filename = basename($file);
                    @endphp
                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start mb-3">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 {{ $color }} rounded-lg flex items-center justify-center">
                                    <i class="fas {{ $icon }} text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1 min-w-0">
                                <h4 class="font-medium text-gray-800 truncate" title="{{ $filename }}">
                                    {{ $filename }}
                                </h4>
                                <div class="flex items-center mt-1 text-sm text-gray-500">
                                    <span class="uppercase">{{ $ext }}</span>
                                    <span class="mx-2">•</span>
                                    @php
                                        try {
                                            $size = Storage::size($file);
                                            $sizeText = $size > 1024 * 1024 ? 
                                                round($size / (1024 * 1024), 2) . ' MB' : 
                                                round($size / 1024, 2) . ' KB';
                                        } catch (Exception $e) {
                                            $sizeText = 'N/A';
                                        }
                                    @endphp
                                    <span>{{ $sizeText }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2">
                            @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
                            <a href="{{ Storage::url($file) }}" target="_blank"
                               class="inline-flex items-center px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                <i class="fas fa-eye mr-1"></i>Xem
                            </a>
                            @endif
                            <a href="{{ route('admin.progress_updates.download', ['id' => $progressUpdate->id, 'filename' => $file]) }}"
                               class="inline-flex items-center px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700 transition-colors">
                                <i class="fas fa-download mr-1"></i>Tải xuống
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            
        @else
            <!-- Empty state -->
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-paperclip text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Không có tệp đính kèm</h3>
                <p class="text-gray-500 mb-6">Báo cáo chưa có tệp đính kèm nào</p>
            </div>
        @endif
    </div>
</div>

<!-- Action Buttons -->
<div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
    <div class="text-sm text-gray-500">
        Tạo ngày: {{ $progressUpdate->created_at->format('d/m/Y H:i') }}
        • Cập nhật: {{ $progressUpdate->updated_at->format('d/m/Y H:i') }}
        • Người báo cáo: 
            @if($progressUpdate->reporter)
                {{ $progressUpdate->reporter->name ?? $progressUpdate->reporter->username ?? 'N/A' }}
            @else
                {{ $progressUpdate->created_by ?? 'N/A' }}
            @endif
    </div>
    <div class="flex gap-2">
        <form action="{{ route('admin.progress_updates.destroy', $progressUpdate->id) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 transition-colors"
                    onclick="return confirm('Bạn có chắc chắn muốn xóa báo cáo này?')">
                <i class="fas fa-trash mr-2"></i>Xóa báo cáo
            </button>
        </form>
        <a href="{{ route('admin.progress_updates.edit', $progressUpdate->id) }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
            <i class="fas fa-edit mr-2"></i>Chỉnh sửa
        </a>
    </div>
</div>
@endsection

@push('styles')
<style>
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.hover\:bg-gray-50:hover {
    transition: background-color 0.2s ease;
}

.progress-bar {
    transition: width 1s ease-in-out;
}
</style>
@endpush