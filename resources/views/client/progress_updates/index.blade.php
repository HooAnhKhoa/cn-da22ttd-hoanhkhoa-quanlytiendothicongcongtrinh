@extends('layouts.app')

@section('title', 'Lịch sử tiến độ')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                @if(isset($currentTask))
                    Tiến độ công việc: {{ $currentTask->task_name }}
                @else
                    Lịch sử cập nhật tiến độ
                @endif
            </h1>
            <p class="text-gray-500 text-sm mt-1">Theo dõi quá trình thực hiện và hình ảnh thực tế</p>
        </div>
        
        @if(isset($currentTask))
        <a href="{{ route('client.tasks.show', $currentTask->id) }}" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại Công việc
        </a>
        @endif
    </div>

    @if(!isset($currentTask))
    <div class="bg-white p-4 rounded-lg shadow-sm mb-6 border border-gray-100">
        <form method="GET" action="{{ route('client.progress_updates.index') }}" class="flex gap-4">
            <input type="text" name="search" placeholder="Tìm theo tên công việc..." class="border rounded-md px-3 py-2 w-full max-w-xs focus:ring-blue-500 focus:border-blue-500">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Lọc</button>
        </form>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        @if($updates->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày báo cáo</th>
                            @if(!isset($currentTask))
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Công việc / Dự án</th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 200px">Tiến độ (%)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nội dung</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người tạo</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Đính kèm</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($updates as $update)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($update->date)->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $update->created_at->format('H:i') }}
                                </div>
                            </td>

                            @if(!isset($currentTask))
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <a href="{{ route('client.tasks.show', $update->task_id) }}" class="hover:text-blue-600">
                                        {{ $update->task->task_name ?? 'N/A' }}
                                    </a>
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $update->task->site->project->project_name ?? 'Project N/A' }}
                                </div>
                            </td>
                            @endif

                            <td class="px-6 py-4 align-middle">
                                <div class="flex items-center">
                                    <span class="text-sm font-bold {{ $update->progress_percent == 100 ? 'text-green-600' : 'text-blue-600' }} mr-2 w-8">
                                        {{ $update->progress_percent }}%
                                    </span>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-{{ $update->progress_percent == 100 ? 'green' : 'blue' }}-600 h-2 rounded-full" 
                                             style="width: {{ $update->progress_percent }}%"></div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-700 line-clamp-2" title="{{ $update->description }}">
                                    {{ $update->description ?? 'Không có mô tả' }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-xs font-bold mr-2">
                                        {{ substr($update->creator->username ?? 'U', 0, 2) }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ $update->creator->username ?? 'N/A' }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                @if($update->attached_files)
                                    @php
                                        // Xử lý decode JSON nếu model chưa cast
                                        $files = is_string($update->attached_files) ? json_decode($update->attached_files, true) : $update->attached_files;
                                    @endphp
                                    
                                    @if(is_array($files) && count($files) > 0)
                                        <div class="flex justify-end -space-x-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 cursor-help" title="{{ count($files) }} file đính kèm">
                                                <i class="fas fa-paperclip mr-1"></i> {{ count($files) }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $updates->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-tasks text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Chưa có cập nhật nào</h3>
                <p class="text-gray-500 mt-1">Các báo cáo tiến độ sẽ xuất hiện tại đây.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush