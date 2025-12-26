@extends('layouts.app')

@section('title', $project->project_name . ' - Chi tiết dự án')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-start">
        <div>
            <nav class="mb-4">
                <a href="{{ route('client.projects.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800">Tên dự án: {{ $project->project_name }}</h1>
            @if($project->location)
                <p class="text-xl text-gray-600 mt-2">Vị trí: {{ $project->location }}</p>
            @endif
        </div>
    </div>
</div>

@include('components.alert')

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-info-circle mr-2"></i>Thông tin dự án
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Tên dự án:</span>
                    <span class="font-medium text-gray-800">{{ $project->project_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Địa điểm:</span>
                    <span class="font-medium text-gray-800">{{ $project->location }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngày bắt đầu:</span>
                    <span class="font-medium text-gray-800">
                        {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') : 'Chưa xác định' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngày kết thúc:</span>
                    <span class="font-medium text-gray-800">
                        {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') : 'Chưa xác định' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Trạng thái:</span>
                    <span class="font-medium text-gray-800">
                        @if($project->status == 'draft') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800">
                                Bản nháp
                            </span>
                        @elseif($project->status == 'pending_contract') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                Chờ hợp đồng
                            </span>
                        @elseif($project->status == 'in_progress') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Đang thi công
                            </span>
                        @elseif($project->status == 'completed') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Hoàn thành
                            </span>
                        @elseif($project->status == 'on_hold') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Tạm dừng
                            </span>
                        @elseif($project->status == 'cancelled') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Đã hủy
                            </span>
                        @endif
                    </span>
                </div>
                                
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Công trường</span>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-bold text-blue-600">{{ $project->sites->count() }}</span>
                        <p class="text-xs text-gray-500">Tổng số công trường</p>
                    </div>
                </div>
            </div>
            
            @if($project->description)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-2">Mô tả:</h4>
                <p class="text-gray-600">{{ $project->description }}</p>
            </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-users mr-2"></i>Đội ngũ dự án
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">{{ $project->owner->username }}</div>
                            <div class="text-sm text-gray-500">Chủ đầu tư</div>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ $project->owner->email }}</span>
                </div>

                @if($project->contractor)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-hard-hat"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">{{ $project->contractor->username }}</div>
                            <div class="text-sm text-gray-500">Nhà thầu</div>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ $project->contractor->email }}</span>
                </div>
                @endif

                @if($project->engineer)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">{{ $project->engineer->username }}</div>
                            <div class="text-sm text-gray-500">Kỹ sư chính</div>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ $project->engineer->email }}</span>
                </div>
                @endif
            </div>
            
            <div class="mt-6 grid grid-cols-2 gap-4">
                <div class="text-center p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-600">Công trường</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $project->sites->count() }}</p>
                </div>
                
                <div class="text-center p-3 bg-yellow-50 rounded-lg">
                    <p class="text-sm text-gray-600">Hợp đồng</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $project->contracts->count() }}
                    </p>
                </div>
                <div class="text-center p-3 bg-red-50 rounded-lg">
                    <p class="text-sm text-gray-600">Tài liệu</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $project->documents->count() ?? 0 }}
                    </p>
                </div>
            </div>
        </div>
    </div>      
</div>

<!-- Phần Accordion cho công trường -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-map-marker-alt mr-2"></i>Danh sách công trường
            <span class="text-sm font-normal text-gray-500 ml-2">({{ $project->sites->count() }} công trường)</span>
        </h2>
    </div>
    
    <div class="p-6">
        @if($project->sites && $project->sites->count() > 0)
            <div class="space-y-4" id="sites-accordion">
                @foreach($project->sites as $siteIndex => $site)
                    @php
                        $totalTasks = $site->tasks->count();
                        $completedTasks = $site->tasks->where('status', 'completed')->count();
                        $totalProgress = 0;
                        $siteProgress = 0;
                        
                        if($totalTasks > 0) {
                            foreach($site->tasks as $task) {
                                $totalProgress += $task->progress_percent ?? 0;
                            }
                            $siteProgress = round($totalProgress / $totalTasks, 1);
                        }

                        $statusColors = [
                            'planned' => 'bg-blue-100 text-blue-800',
                            'in_progress' => 'bg-green-100 text-green-800',
                            'completed' => 'bg-gray-100 text-gray-800',
                            'on_hold' => 'bg-yellow-100 text-yellow-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        $statusTexts = [
                            'planned' => 'Lập kế hoạch',
                            'in_progress' => 'Đang thi công',
                            'completed' => 'Hoàn thành',
                            'on_hold' => 'Tạm dừng',
                            'cancelled' => 'Đã hủy',
                        ];
                    @endphp
                    
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <!-- Header công trường -->
                        <div class="bg-gray-50 px-6 py-4 flex justify-between items-center cursor-pointer site-header hover:bg-gray-100 transition-colors"
                             data-site-id="{{ $site->id }}">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-map-marker-alt text-blue-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">{{ $site->site_name }}</h3>
                                    @if($site->location)
                                    <p class="text-sm text-gray-600">{{ $site->location }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-6">
                                <div class="text-center">
                                    <span class="block font-medium text-gray-800">{{ $totalTasks }}</span>
                                    <span class="text-xs text-gray-500">Công việc</span>
                                </div>
                                
                                <div class="text-center">
                                    <span class="block font-medium text-gray-800">{{ $completedTasks }}</span>
                                    <span class="text-xs text-gray-500">Hoàn thành</span>
                                </div>
                                
                                <div class="text-center">
                                    <span class="block font-medium text-gray-800">{{ $siteProgress }}%</span>
                                    <span class="text-xs text-gray-500">Tiến độ</span>
                                </div>
                                
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$site->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusTexts[$site->status] ?? $site->status }}
                                </span>
                                
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-600">{{ $site->start_date ? $site->start_date->format('d/m/Y') : 'N/A' }}</span>
                                    @if($site->end_date)
                                    <span class="mx-2">→</span>
                                    <span class="text-sm text-gray-600">{{ $site->end_date->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                                
                                <i class="fas fa-chevron-down text-gray-400 transition-transform site-arrow"></i>
                            </div>
                        </div>
                        
                        <!-- Nội dung công trường (ẩn ban đầu) -->
                        <div class="hidden site-content" id="site-content-{{ $site->id }}">
                            <div class="px-6 py-4 bg-white">
                                @if($site->tasks && $site->tasks->count() > 0)
                                    <div class="space-y-4">
                                        @foreach($site->tasks as $taskIndex => $task)
                                            <div class="border border-gray-200 rounded-lg">
                                                <!-- Header công việc -->
                                                <div class="bg-gray-50 px-4 py-3 flex justify-between items-center cursor-pointer task-header hover:bg-gray-100 transition-colors"
                                                     data-task-id="{{ $task->id }}">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-tasks text-green-600 text-sm"></i>
                                                        </div>
                                                        <div>
                                                            <h4 class="font-medium text-gray-800">{{ $task->task_name }}</h4>
                                                            <p class="text-xs text-gray-500">
                                                                {{ $task->start_date ? $task->start_date->format('d/m/Y') : 'N/A' }}
                                                                @if($task->end_date)
                                                                - {{ $task->end_date->format('d/m/Y') }}
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="flex items-center space-x-4">
                                                        <div class="flex items-center space-x-2">
                                                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                                                <div class="bg-green-600 h-2 rounded-full" 
                                                                     style="width: {{ $task->progress_percent ?? 0 }}%"></div>
                                                            </div>
                                                            <span class="text-sm font-medium text-gray-700">{{ $task->progress_percent ?? 0 }}%</span>
                                                        </div>
                                                        
                                                        @php
                                                            $taskStatusColors = [
                                                                'not_started' => 'bg-gray-100 text-gray-800',
                                                                'in_progress' => 'bg-yellow-100 text-yellow-800',
                                                                'completed' => 'bg-green-100 text-green-800',
                                                                'on_hold' => 'bg-red-100 text-red-800',
                                                            ];
                                                            $taskStatusTexts = [
                                                                'not_started' => 'Chưa bắt đầu',
                                                                'in_progress' => 'Đang thực hiện',
                                                                'completed' => 'Hoàn thành',
                                                                'on_hold' => 'Tạm dừng',
                                                            ];
                                                        @endphp
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $taskStatusColors[$task->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                            {{ $taskStatusTexts[$task->status] ?? $task->status }}
                                                        </span>
                                                        
                                                        <i class="fas fa-chevron-down text-gray-400 transition-transform task-arrow"></i>
                                                    </div>
                                                </div>
                                                
                                                <!-- Nội dung công việc (ẩn ban đầu) -->
                                                <div class="hidden task-content" id="task-content-{{ $task->id }}">
                                                    <div class="px-4 py-4">
                                                        <!-- Thông tin công việc -->
                                                        <div class="mb-4">
                                                            <h5 class="font-medium text-gray-700 mb-2">Thông tin công việc</h5>
                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                @if($task->description)
                                                                <div>
                                                                    <span class="text-sm text-gray-600">Mô tả:</span>
                                                                    <p class="text-sm text-gray-800 mt-1">{{ $task->description }}</p>
                                                                </div>
                                                                @endif
                                                                
                                                                <div>
                                                                    <span class="text-sm text-gray-600">Thời gian:</span>
                                                                    <p class="text-sm text-gray-800 mt-1">
                                                                        {{ $task->start_date ? $task->start_date->format('d/m/Y') : 'N/A' }}
                                                                        @if($task->end_date)
                                                                        → {{ $task->end_date->format('d/m/Y') }}
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                                
                                                                @if($task->assigned_to)
                                                                <div>
                                                                    <span class="text-sm text-gray-600">Người phụ trách:</span>
                                                                    <p class="text-sm text-gray-800 mt-1">{{ $task->assigned_to }}</p>
                                                                </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Tiến độ và hình ảnh -->
                                                        @if($task->progressUpdates && $task->progressUpdates->count() > 0)
                                                            <div class="border-t border-gray-200 pt-4">
                                                                <h5 class="font-medium text-gray-700 mb-3">Cập nhật tiến độ</h5>
                                                                <div class="space-y-3">
                                                                    @foreach($task->progressUpdates as $update)
                                                                        <div class="bg-gray-50 rounded-lg p-3">
                                                                            <div class="flex justify-between items-start mb-2">
                                                                                <div>
                                                                                    <span class="text-sm font-medium text-gray-800">
                                                                                        {{ $update->progress_percent ?? 0 }}% hoàn thành
                                                                                    </span>
                                                                                    @if($update->creator)
                                                                                    <p class="text-xs text-gray-500 mt-1">
                                                                                        <i class="fas fa-user mr-1"></i>{{ $update->creator->username }} 
                                                                                        • <i class="far fa-clock ml-2 mr-1"></i>{{ $update->date->format('d/m/Y H:i') }}
                                                                                    </p>
                                                                                    @endif
                                                                                </div>
                                                                                @if($update->status)
                                                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium 
                                                                                    {{ $update->status == 'completed' ? 'bg-green-100 text-green-800' : 
                                                                                    ($update->status == 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 
                                                                                    'bg-gray-100 text-gray-800') }}">
                                                                                    <i class="fas fa-circle mr-1 text-xs"></i>{{ $update->status }}
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                            
                                                                            @if($update->description)
                                                                            <div class="mb-3">
                                                                                <span class="text-xs font-medium text-gray-700 block mb-1">Ghi chú:</span>
                                                                                <p class="text-sm text-gray-600 bg-white p-2 rounded border border-gray-200">{{ $update->description }}</p>
                                                                            </div>
                                                                            @endif
                                                                            
                                                                            <!-- Hiển thị file đính kèm -->
                                                                            @if($update->attached_files && is_array($update->attached_files) && count($update->attached_files) > 0)
                                                                            <div class="mt-4">
                                                                                <div class="flex items-center justify-between mb-2">
                                                                                    <span class="text-sm font-medium text-gray-700">
                                                                                        <i class="fas fa-paperclip mr-2"></i>File đính kèm ({{ count($update->attached_files) }})
                                                                                    </span>
                                                                                </div>
                                                                                
                                                                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                                                                    @foreach($update->attached_files as $fileIndex => $filePath)
                                                                                    @php
                                                                                        $fileExt = pathinfo($filePath, PATHINFO_EXTENSION);
                                                                                        $isImage = in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                                                        $fileName = pathinfo($filePath, PATHINFO_BASENAME);
                                                                                    @endphp
                                                                                    
                                                                                    <div class="relative group">
                                                                                        @if($isImage)
                                                                                        <a href="{{ Storage::url($filePath) }}" 
                                                                                           data-lightbox="progress-images-{{ $update->id }}-{{ $task->id }}"
                                                                                           data-title="Cập nhật {{ $update->date->format('d/m/Y') }} - {{ $fileName }}"
                                                                                           class="block rounded-lg overflow-hidden border border-gray-200 hover:border-blue-500 transition-colors">
                                                                                            <img src="{{ Storage::url($filePath) }}" 
                                                                                                 alt="{{ $fileName }}"
                                                                                                 class="w-full h-32 object-cover"
                                                                                                 onerror="this.src='https://via.placeholder.com/300x200?text=Không+thể+hiển+thị+hình+ảnh'">
                                                                                            
                                                                                            <!-- Overlay trên ảnh -->
                                                                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                                                                                <i class="fas fa-search-plus text-white text-lg"></i>
                                                                                            </div>
                                                                                        </a>
                                                                                        @else
                                                                                        <a href="{{ Storage::url($filePath) }}" 
                                                                                           target="_blank"
                                                                                           class="block rounded-lg overflow-hidden border border-gray-200 hover:border-blue-500 transition-colors p-3 bg-gray-50">
                                                                                            <div class="flex flex-col items-center justify-center h-32">
                                                                                                <i class="fas fa-file text-3xl text-gray-400 mb-2"></i>
                                                                                                <span class="text-xs text-gray-600 truncate w-full text-center">
                                                                                                    {{ Str::limit($fileName, 20) }}
                                                                                                </span>
                                                                                                <span class="text-xs text-gray-500 mt-1">{{ strtoupper($fileExt) }}</span>
                                                                                            </div>
                                                                                        </a>
                                                                                        @endif
                                                                                        
                                                                                        <!-- Thông tin file -->
                                                                                        <p class="text-xs text-gray-500 mt-1 truncate" title="{{ $fileName }}">
                                                                                            {{ Str::limit($fileName, 20) }}
                                                                                        </p>
                                                                                    </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            </div>
                                                                            @endif
                                                                            
                                                                            <!-- Thông tin bổ sung -->
                                                                            <div class="mt-3 pt-3 border-t border-gray-200 text-xs text-gray-500">
                                                                                <div class="flex flex-wrap gap-3">
                                                                                    @if($update->updated_at && $update->updated_at != $update->created_at)
                                                                                    <span>
                                                                                        <i class="far fa-edit mr-1"></i>
                                                                                        Sửa lần cuối: {{ $update->updated_at->format('d/m/Y H:i') }}
                                                                                    </span>
                                                                                    @endif
                                                                                    
                                                                                    @if($update->location)
                                                                                    <span>
                                                                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                                                                        {{ $update->location }}
                                                                                    </span>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="text-center py-8 border-t border-gray-200">
                                                                <div class="inline-flex items-center justify-center w-14 h-14 bg-gray-100 rounded-full mb-3">
                                                                    <i class="fas fa-history text-gray-400 text-xl"></i>
                                                                </div>
                                                                <h6 class="font-medium text-gray-700 mb-1">Chưa có cập nhật tiến độ</h6>
                                                                <p class="text-gray-500 text-sm">Chưa có báo cáo nào cho công việc này</p>
                                                                
                                                                <!-- Nút để thêm cập nhật tiến độ -->
                                                                @can('create', App\Models\ProgressUpdate::class)
                                                                <a href="{{ route('client.progress_updates.create', ['task' => $task->id]) }}" 
                                                                class="inline-flex items-center px-3 py-1.5 mt-3 bg-blue-50 text-blue-600 rounded-lg text-sm hover:bg-blue-100 transition-colors">
                                                                    <i class="fas fa-plus mr-1"></i>Thêm cập nhật
                                                                </a>
                                                                @endcan
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    {{-- <div class="mt-4 flex justify-end">
                                        <a href="{{ route('client.progress_updates.index', $site) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                            <i class="fas fa-external-link-alt mr-2"></i>Xem chi tiết công trường
                                        </a>
                                    </div> --}}
                                @else
                                    <div class="text-center py-8">
                                        <div class="inline-flex items-center justify-center w-14 h-14 bg-gray-100 rounded-full mb-3">
                                            <i class="fas fa-tasks text-gray-400 text-xl"></i>
                                        </div>
                                        <h6 class="font-medium text-gray-700 mb-1">Chưa có công việc nào</h6>
                                        <p class="text-gray-500 text-sm">Công trường chưa được gán công việc</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-map-marker-alt text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Chưa có công trường nào</h3>
                <p class="text-gray-500 mb-6">Dự án chưa được gán công trường</p>
            </div>
        @endif
    </div>
</div>

<!-- Các phần hợp đồng và tài liệu -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="border-b border-gray-200">
        <nav class="flex -mb-px">
            <button class="tab-button active py-4 px-6 text-sm font-medium border-b-2 border-blue-500 text-blue-600" data-tab="contracts">
                <i class="fas fa-file-contract mr-2"></i>Hợp đồng ({{ $project->contracts->count() }})
            </button>
            <button class="tab-button py-4 px-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="documents">
                <i class="fas fa-file-alt mr-2"></i>Tài liệu ({{ $project->documents->count() ?? 0 }})
            </button>
        </nav>
    </div>

    <div class="p-6">
        <div id="tab-contracts" class="tab-content active">
            @if($project->contracts->count() > 0)
                <div class="space-y-4">
                    @foreach($project->contracts as $contract)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="font-semibold text-lg text-gray-800">Hợp đồng #{{ $contract->id }}</h4>
                                <p class="text-gray-600 mt-1">Nhà thầu: {{ $contract->contractor->username ?? 'N/A' }}</p>
                                <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                    <span>Ký ngày: {{ $contract->signed_date->format('d/m/Y') }}</span>
                                    <span>• Hạn: {{ $contract->due_date->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <div class="text-lg font-bold text-green-600">
                                    {{ number_format($contract->contract_value) }} VNĐ
                                </div>
                                @php
                                    $contractStatusColors = [
                                        'active' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'terminated' => 'bg-red-100 text-red-800',
                                        'expired' => 'bg-yellow-100 text-yellow-800',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $contractStatusColors[$contract->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $contract->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-file-contract text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Chưa có hợp đồng nào</h3>
                    <p class="text-gray-500 mb-6">Dự án chưa được thiết lập hợp đồng</p>
                    @if(auth()->user()->user_type === 'contractor')
                        <a href="{{ route('client.contracts.create', ['project_id' => $project->id]) }}" 
                        class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-white hover:bg-green-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Tạo hợp đồng
                        </a>
                    @endif
                </div>
            @endif
        </div>

        <div id="tab-documents" class="tab-content hidden">
            @if($project->documents && $project->documents->count() > 0)
                <div class="space-y-4">
                    @foreach($project->documents as $document)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-alt text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-semibold text-gray-800">{{ $document->document_name }}</h4>
                                    @if($document->description)
                                    <p class="text-gray-600 text-sm mt-1">{{ $document->description }}</p>
                                    @endif
                                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                        <span>Loại: {{ $document->document_type }}</span>
                                        <span>• Kích thước: {{ round($document->file_size / 1024, 2) }} KB</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($document->file_path)
                                <a href="{{ Storage::url($document->file_path) }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-download mr-1"></i>Tải xuống
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-file-alt text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Chưa có tài liệu nào</h3>
                    <p class="text-gray-500 mb-6">Dự án chưa được tải lên tài liệu</p>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
    <div class="text-sm text-gray-500">
        Tạo ngày: {{ $project->created_at->format('d/m/Y H:i') }}
        • Cập nhật: {{ $project->updated_at->format('d/m/Y H:i') }}
        • Tổng công trường: {{ $project->sites->count() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            // Remove active classes from all tabs
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
                content.classList.add('hidden');
            });
            
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Add active class to clicked tab
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('active', 'border-blue-500', 'text-blue-600');
            
            // Show corresponding content
            const target = document.getElementById('tab-' + tabName);
            if (target) {
                target.classList.remove('hidden');
                target.classList.add('active');
            }
        });
    });

    // Accordion functionality for sites
    const siteHeaders = document.querySelectorAll('.site-header');
    siteHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const siteId = this.getAttribute('data-site-id');
            const content = document.getElementById(`site-content-${siteId}`);
            const arrow = this.querySelector('.site-arrow');
            
            // Toggle content visibility
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                arrow.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                arrow.classList.remove('rotate-180');
            }
        });
    });

    // Accordion functionality for tasks
    const taskHeaders = document.querySelectorAll('.task-header');
    taskHeaders.forEach(header => {
        header.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent triggering parent site header
            const taskId = this.getAttribute('data-task-id');
            const content = document.getElementById(`task-content-${taskId}`);
            const arrow = this.querySelector('.task-arrow');
            
            // Toggle content visibility
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                arrow.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                arrow.classList.remove('rotate-180');
            }
        });
    });

    // Open first site and task by default (optional)
    const firstSiteHeader = document.querySelector('.site-header');
    if (firstSiteHeader) {
        const firstSiteId = firstSiteHeader.getAttribute('data-site-id');
        const firstSiteContent = document.getElementById(`site-content-${firstSiteId}`);
        const firstSiteArrow = firstSiteHeader.querySelector('.site-arrow');
        
        if (firstSiteContent) {
            firstSiteContent.classList.remove('hidden');
            firstSiteArrow.classList.add('rotate-180');
            
            // Open first task of first site
            const firstTaskHeader = firstSiteContent.querySelector('.task-header');
            if (firstTaskHeader) {
                const firstTaskId = firstTaskHeader.getAttribute('data-task-id');
                const firstTaskContent = document.getElementById(`task-content-${firstTaskId}`);
                const firstTaskArrow = firstTaskHeader.querySelector('.task-arrow');
                
                if (firstTaskContent && firstTaskArrow) {
                    firstTaskContent.classList.remove('hidden');
                    firstTaskArrow.classList.add('rotate-180');
                }
            }
        }
    }
});
</script>
@endpush

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

/* Rotate animation for arrows */
.rotate-180 {
    transform: rotate(180deg);
    transition: transform 0.3s ease;
}

.site-arrow, .task-arrow {
    transition: transform 0.3s ease;
}

/* Smooth accordion animations */
.site-content, .task-content {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endpush