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
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Dự án gần đây</h2>
        </div>
        <div class="p-6">
            @if($recentProjects->count() > 0)
                <div class="space-y-4">
                    @foreach($recentProjects as $project)
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ $project->project_name }}</h3>
                            <p class="text-sm text-gray-600">{{ $project->location }}</p>
                            <span class="inline-block px-2 py-1 text-xs rounded-full 
                                @if($project->status == 'in_progress') bg-green-100 text-green-800
                                @elseif($project->status == 'completed') bg-blue-100 text-blue-800
                                @elseif($project->status == 'on_hold') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $project->status }}
                            </span>
                        </div>
                        <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Chưa có dự án nào</p>
            @endif
        </div>
    </div>

    <!-- Công việc gần đây -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Công việc gần đây</h2>
        </div>
        <div class="p-6">
            @if($recentTasks->count() > 0)
                <div class="space-y-4">
                    @foreach($recentTasks as $task)
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-semibold text-gray-800">{{ $task->task_name }}</h3>
                            <span class="inline-block px-2 py-1 text-xs rounded-full 
                                @if($task->status == 'completed') bg-green-100 text-green-800
                                @elseif($task->status == 'in_progress') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $task->status }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">Dự án: {{ $task->site->project->project_name }}</p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span>Tiến độ: {{ $task->progress_percent }}%</span>
                            <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-800">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Chưa có công việc nào</p>
            @endif
        </div>
    </div>
</div>
@endsection