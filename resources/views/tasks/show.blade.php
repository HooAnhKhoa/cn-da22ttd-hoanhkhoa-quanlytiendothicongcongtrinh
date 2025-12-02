@extends('layouts.app')

@section('title', $task->task_name . ' - Chi tiết dự án')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-start">
        <div>
            <nav class="mb-4">
                <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800">Tên công việc: {{ $task->task_name }}</h1>
            <p class="text-xl text-gray-600 mt-2">Vị trí: {{ $task->location }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('tasks.edit', $task) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
                <i class="fas fa-edit mr-2"></i>Chỉnh sửa
            </a>
            <!-- Nút thêm báo cáo tiến độ -->
            <a href="{{ route('progress_updates.create', ['task_id' => $task->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Thêm báo cáo
            </a>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Task Information -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-info-circle mr-2"></i>Thông tin công việc
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Tên công việc:</span>
                    <span class="font-medium text-gray-800">{{ $task->task_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Nhánh của công việc:</span>
                    @if($task->parent)
                        <a href="{{ route('tasks.show', $task->parent) }}" class="font-medium text-gray-800">{{ $task->parent->task_name }}</a>
                    @else
                        <span class="font-medium text-gray-800">Không có</span>
                    @endif
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Công trường:</span>
                    <a href="{{ route('sites.show', $task->site)}}">
                        <span class="font-medium text-gray-800">{{ $task->site->site_name ?? 'N/A' }}</span>                
                    </a>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngày bắt đầu:</span>
                    <span class="font-medium text-gray-800">{{ $task->start_date }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngày kết thúc:</span>
                    <span class="font-medium text-gray-800">
                        {{ $task->end_date ? $task->end_date : 'Chưa xác định' }}
                    </span>
                </div>
             
                <div class="flex justify-between">
                    <span class="text-gray-600">Trạng thái:</span>
                    <span class="font-medium text-gray-800">
                        {{ \App\Models\Project::getStatuses()[$task->status] ?? $task->status }}
                    </span>
                </div>
                <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700">Tiến độ tổng thể</span>
                    <span class="text-sm font-medium text-gray-700">{{ $task->progress_percent }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                         style="width: {{ $task->progress_percent }}%"></div>
                </div>
            </div>
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-tachometer-alt text-blue-500 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Tiến độ</span>
                </div>
                <div class="text-right">
                    <span class="text-lg font-bold text-blue-600">{{ $task->progress_percent }}%</span>
                    <p class="text-xs text-gray-500"> 
                        {{ $task->actual_duration ?? 0 }}/{{ $task->planned_duration }} ngày
                    </p>
                </div>
            </div>
            </div>
            
            @if($task->description)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-2">Mô tả:</h4>
                <p class="text-gray-600">{{ $task->description }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Task Team -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-users mr-2"></i>Thông tin bổ sung
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <!-- Owner -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">Ngày tạo</div>
                            <div class="text-sm text-gray-500">{{ $task->created_at }}</div>
                        </div>
                    </div>
                </div>

                <!-- Contractor --> 
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-hard-hat"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">Cập nhật lần cuối</div>
                            <div class="text-sm text-gray-500">{{ $task->updated_at}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Báo cáo tiến độ Section -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-chart-line mr-2"></i>Lịch sử báo cáo tiến độ
            <span class="text-sm font-normal text-gray-500 ml-2">({{ $task->progressUpdates->count() }} báo cáo)</span>
        </h2>
        <a href="{{ route('progress_updates.create', ['task_id' => $task->id]) }}" 
           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>Thêm báo cáo mới
        </a>
    </div>
    
    <div class="p-6">
        @if($task->progressReports->count() > 0)
            <!-- Timeline View -->
            <div class="relative">
                <!-- Timeline line -->
                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                
                <div class="space-y-8">
                    @foreach($task->progressReports->sortByDesc('date') as $report)
                    <div class="relative pl-12">
                        <!-- Timeline dot -->
                        <div class="absolute left-0 w-8 h-8 bg-blue-100 border-4 border-white rounded-full flex items-center justify-center">
                            <i class="fas fa-flag text-blue-600 text-sm"></i>
                        </div>
                        
                        <!-- Report card -->
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="font-semibold text-lg text-gray-800">
                                        <i class="fas fa-calendar-day mr-2"></i>{{ $report->date->format('d/m/Y') }}
                                    </h4>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <i class="fas fa-user mr-1"></i>{{ $report->created_by }}
                                        • <i class="fas fa-clock mr-1"></i>{{ $report->created_at->format('H:i') }}
                                    </p>
                                </div>
                                
                                <div class="text-right">
                                    <!-- Progress percentage badge -->
                                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if($report->progress_percent >= 90) bg-green-100 text-green-800
                                        @elseif($report->progress_percent >= 50) bg-blue-100 text-blue-800
                                        @elseif($report->progress_percent > 0) bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        <i class="fas fa-chart-line mr-1"></i>{{ $report->progress_percent }}%
                                    </div>
                                    
                                    <!-- Action buttons -->
                                    <div class="flex gap-2 mt-2">
                                        <a href="{{ route('progress_updates.show', $report) }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('progress_updates.edit', $report) }}" 
                                           class="text-yellow-600 hover:text-yellow-800 text-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('progress_updates.destroy', $report) }}" 
                                              method="POST" class="inline" 
                                              onsubmit="return confirm('Xóa báo cáo này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Progress bar -->
                            <div class="mb-4">
                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                    <span>Tiến độ báo cáo</span>
                                    <span>{{ $report->progress_percent }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                         style="width: {{ $report->progress_percent }}%"></div>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            @if($report->description)
                            <div class="mb-3">
                                <div class="flex items-center text-gray-700 mb-1">
                                    <i class="fas fa-align-left text-gray-400 mr-2"></i>
                                    <span class="font-medium">Mô tả:</span>
                                </div>
                                <p class="text-gray-600 bg-white p-3 rounded border border-gray-100">
                                    {{ $report->description }}
                                </p>
                            </div>
                            @endif
                            
                            <!-- Attached files -->
                            @if($report->attached_files)
                            <div>
                                <div class="flex items-center text-gray-700 mb-2">
                                    <i class="fas fa-paperclip text-gray-400 mr-2"></i>
                                    <span class="font-medium">Tệp đính kèm:</span>
                                </div>
                                {{-- <div class="flex flex-wrap gap-2">
                                    @php
                                        $files = json_decode($report->attached_files, true) ?? [$report->attached_files];
                                    @endphp
                                    @foreach($files as $file)
                                    @if($file)
                                    <a href="{{ Storage::url($file) }}" 
                                       target="_blank" 
                                       class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200 transition-colors">
                                        <i class="fas fa-file mr-1"></i>
                                        {{ basename($file) }}
                                    </a>
                                    @endif
                                    @endforeach
                                </div> --}}
                            </div>
                            @endif
                            
                            <!-- Timeline comparison -->
                            {{-- <div class="mt-4 pt-3 border-t border-gray-200 text-xs text-gray-500">
                                <div class="flex justify-between">
                                    <span>
                                        <i class="far fa-clock mr-1"></i>Cập nhật: {{ $report->updated_at->format('d/m/Y H:i') }}
                                    </span>
                                    @if($loop->iteration < $task->progressReports->count())
                                        @php
                                            $prevReport = $task->progressReports->sortByDesc('date')->values()[$loop->iteration];
                                            $progressChange = $report->progress_percent - $prevReport->progress_percent;
                                        @endphp
                                        @if($progressChange > 0)
                                            <span class="text-green-600">
                                                <i class="fas fa-arrow-up mr-1"></i>+{{ $progressChange }}% so với trước
                                            </span>
                                        @elseif($progressChange < 0)
                                            <span class="text-red-600">
                                                <i class="fas fa-arrow-down mr-1"></i>{{ $progressChange }}% so với trước
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Summary statistics -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-4">Thống kê báo cáo:</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-blue-600 font-bold text-xl">
                            {{ $task->progressReports->count() }}
                        </div>
                        <div class="text-gray-600 text-sm">Tổng số báo cáo</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="text-green-600 font-bold text-xl">
                            {{ $task->progressReports->max('progress_percent') ?? 0 }}%
                        </div>
                        <div class="text-gray-600 text-sm">Tiến độ cao nhất</div>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <div class="text-purple-600 font-bold text-xl">
                            @if($task->progressReports->count() > 1)
                                {{ $task->progressReports->sortByDesc('date')->first()->progress_percent - $task->progressReports->sortBy('date')->first()->progress_percent }}%
                            @else
                                0%
                            @endif
                        </div>
                        <div class="text-gray-600 text-sm">Tăng trưởng tiến độ</div>
                    </div>
                </div>
            </div>
            
        @else
            <!-- Empty state -->
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-chart-line text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Chưa có báo cáo tiến độ</h3>
                <p class="text-gray-500 mb-6">Hãy thêm báo cáo đầu tiên để theo dõi tiến độ công việc</p>
                <a href="{{ route('progress_updates.create', ['task_id' => $task->id]) }}" 
                   class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-white hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Thêm báo cáo đầu tiên
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Action Buttons -->
<div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
    <div class="text-sm text-gray-500">
        Tạo ngày: {{ $task->created_at->format('d/m/Y H:i') }}
        • Cập nhật: {{ $task->updated_at->format('d/m/Y H:i') }}
    </div>
    <div class="flex gap-2">
        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 transition-colors"
                    onclick="return confirm('Bạn có chắc chắn muốn xóa dự án này?')">
                <i class="fas fa-trash mr-2"></i>Xóa dự án
            </button>
        </form>
        <a href="{{ route('tasks.edit', $task) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
            <i class="fas fa-edit mr-2"></i>Chỉnh sửa
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality (if needed for future tabs)
    const tabButtons = document.querySelectorAll('.tab-button');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            this.classList.add('border-blue-500', 'text-blue-600');
            this.classList.remove('border-transparent', 'text-gray-500');
            
            const target = document.getElementById('tab-' + tabName);
            if (target) {
                target.style.display = 'block';
            }
        });
    });
    
    // Expand/collapse report descriptions
    const reportDescriptions = document.querySelectorAll('.report-description');
    reportDescriptions.forEach(desc => {
        const content = desc.querySelector('.description-content');
        const toggleBtn = desc.querySelector('.toggle-description');
        
        if (toggleBtn && content.textContent.length > 150) {
            toggleBtn.style.display = 'block';
            
            toggleBtn.addEventListener('click', function() {
                content.classList.toggle('line-clamp-3');
                const icon = this.querySelector('i');
                if (content.classList.contains('line-clamp-3')) {
                    icon.className = 'fas fa-chevron-down';
                    this.querySelector('span').textContent = 'Xem thêm';
                } else {
                    icon.className = 'fas fa-chevron-up';
                    this.querySelector('span').textContent = 'Thu gọn';
                }
            });
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush