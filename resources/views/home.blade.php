@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-gray-600">Tổng quan về hệ thống quản lý xây dựng</p>
</div>

<!-- Thống kê tổng quan -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-project-diagram text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Tổng dự án</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_projects'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-tasks text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Tổng công việc</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_tasks'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-users text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Người dùng</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_users'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                <i class="fas fa-hard-hat text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Dự án đang thi công</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_projects'] }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Dự án gần đây -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">Dự án gần đây</h2>
            <a href="{{ route('projects.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Xem tất cả
            </a>
        </div>
        <div class="p-6">
            @if($recentProjects->count() > 0)
                <div class="space-y-4">
                    @foreach($recentProjects->take(5) as $project)
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800">{{ $project->project_name }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $project->location }}</p>
                            <div class="flex items-center mt-2 space-x-2">
                                <span class="inline-block px-2 py-1 text-xs rounded-full 
                                    @if($project->status == 'in_progress') bg-green-100 text-green-800
                                    @elseif($project->status == 'completed') bg-blue-100 text-blue-800
                                    @elseif($project->status == 'on_hold') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ \App\Models\Project::getStatuses()[$project->status] ?? $project->status }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $project->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('projects.show', $project) }}" class="ml-4 p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
                @if($recentProjects->count() > 5)
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-500">Đang hiển thị 5/{{ $recentProjects->count() }} dự án</p>
                </div>
                @endif
            @else
                <div class="text-center py-8">
                    <i class="fas fa-project-diagram text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Chưa có dự án nào</p>
                    <a href="{{ route('projects.create') }}" class="inline-block mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Tạo dự án đầu tiên
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Công việc gần đây -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">Công việc gần đây</h2>
            <a href="{{ route('tasks.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Xem tất cả
            </a>
        </div>
        <div class="p-6">
            @if($recentTasks->count() > 0)
                <div class="space-y-4">
                    @foreach($recentTasks->take(5) as $task)
                    <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-semibold text-gray-800">{{ $task->task_name }}</h3>
                            <span class="inline-block px-2 py-1 text-xs rounded-full 
                                @if($task->status == 'completed') bg-green-100 text-green-800
                                @elseif($task->status == 'in_progress') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ \App\Models\Project::getStatuses()[$task->status] ?? $task->status }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">Dự án: {{ $task->site->project->project_name }}</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $task->progress_percent }}%"></div>
                                </div>
                                <span class="text-sm text-gray-500">{{ $task->progress_percent }}%</span>
                            </div>
                            <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Xem chi tiết
                            </a>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            Cập nhật: {{ $task->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-tasks text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Chưa có công việc nào</p>
                    <a href="{{ route('tasks.create') }}" class="inline-block mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Tạo công việc đầu tiên
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Các section khác có thể thêm sau -->
<div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Có thể thêm các section khác như: -->
    <!-- - Công việc sắp đến hạn -->
    <!-- - Vật tư cần bổ sung -->
    <!-- - Sự cố cần xử lý -->
</div>
@endsection