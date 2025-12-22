@extends('layouts.app')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Bảng Điều Khiển Giám Sát</h1>
        <p class="text-gray-500 mt-2 font-medium">Chào mừng trở lại! Dưới đây là tóm tắt tiến độ thi công của các dự án của bạn.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center">
            <div class="p-4 bg-blue-100 rounded-2xl text-blue-600 mr-4">
                <i class="fas fa-project-diagram text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Tổng dự án</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['total_projects'] }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center">
            <div class="p-4 bg-red-100 rounded-2xl text-red-600 mr-4">
                <i class="fas fa-clock text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Trễ hạn</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $taskStats['overdue_tasks'] }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center">
            <div class="p-4 bg-green-100 rounded-2xl text-green-600 mr-4">
                <i class="fas fa-wallet text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Đã thanh toán</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ number_format($paymentStats['total_paid'] ?? 0) }}đ</h3>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center">
            <div class="p-4 bg-indigo-100 rounded-2xl text-indigo-600 mr-4">
                <i class="fas fa-check-double text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Công việc xong</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $taskStats['completed_tasks'] }}/{{ $taskStats['total_tasks'] }}</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-800">Nhật ký thi công mới nhất</h2>
                    <a href="{{ route('client.progress.index') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800">Xem tất cả</a>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            @foreach($recentProgress as $progress)
                            <li>
                                <div class="relative pb-8">
                                    @if (!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <i class="fas fa-hammer text-white text-xs"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-600">
                                                    {{ $progress->description }} 
                                                    <span class="font-bold text-gray-800">[{{ $progress->task->task_name }}]</span>
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">Dự án: {{ $progress->task->site->project->project_name }}</p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time>{{ $progress->created_at->diffForHumans() }}</time>
                                                <div class="mt-1 font-bold text-blue-600">{{ $progress->progress_percent }}%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50">
                    <h2 class="text-lg font-bold text-gray-800">Danh sách dự án tham gia</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tên dự án</th>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Thời hạn</th>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentProjects as $project)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-4">
                                    <div class="font-bold text-gray-800">{{ $project->project_name }}</div>
                                    <div class="text-xs text-gray-400">{{ $project->location }}</div>
                                </td>
                                <td class="p-4 text-sm text-gray-600">
                                    {{ $project->start_date->format('d/m/Y') }}
                                </td>
                                <td class="p-4">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full 
                                        @if($project->status == 'in_progress') bg-blue-50 text-blue-600 
                                        @elseif($project->status == 'completed') bg-green-50 text-green-600
                                        @else bg-gray-50 text-gray-600 @endif">
                                        {{ $project->status }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <a href="{{ route('client.projects.show', $project->id) }}" class="text-blue-600 hover:underline font-bold text-sm">Chi tiết</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-6">Tiêu thụ vật tư chính</h2>
                <div class="space-y-6">
                    @foreach($materialStats['top_materials'] ?? [] as $material)
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="font-semibold text-gray-700">{{ $material->name }}</span>
                            <span class="text-gray-500">{{ number_format($material->total_used) }} {{ $material->unit }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $material->percent }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl shadow-xl p-6 text-white">
                <h2 class="text-lg font-bold mb-4 opacity-90">Tổng quan tài chính</h2>
                <div class="space-y-4">
                    <div class="bg-white/10 rounded-2xl p-4 backdrop-blur-sm">
                        <p class="text-xs font-medium opacity-70 uppercase">Tổng giá trị hợp đồng</p>
                        <p class="text-xl font-bold">{{ number_format($paymentStats['total_contract_value'] ?? 0) }}đ</p>
                    </div>
                    <div class="bg-white/10 rounded-2xl p-4 backdrop-blur-sm border-l-4 border-green-400">
                        <p class="text-xs font-medium opacity-70 uppercase">Đã thanh toán</p>
                        <p class="text-xl font-bold">{{ number_format($paymentStats['total_paid'] ?? 0) }}đ</p>
                    </div>
                    <div class="bg-white/10 rounded-2xl p-4 backdrop-blur-sm border-l-4 border-yellow-400">
                        <p class="text-xs font-medium opacity-70 uppercase">Còn lại phải thu</p>
                        <p class="text-xl font-bold">{{ number_format(($paymentStats['total_contract_value'] ?? 0) - ($paymentStats['total_paid'] ?? 0)) }}đ</p>
                    </div>
                </div>
                <button class="w-full mt-6 py-3 bg-white text-blue-600 rounded-xl font-bold hover:bg-blue-50 transition-colors">
                    Yêu cầu sao kê
                </button>
            </div>
        </div>
    </div>
</div>
@endsection