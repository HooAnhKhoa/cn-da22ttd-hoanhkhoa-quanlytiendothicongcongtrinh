@extends('layouts.app')

@section('title', 'Danh sách báo cáo tiến độ')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Danh sách báo cáo tiến độ</h1>
                <p class="text-gray-600 mt-2">Tất cả báo cáo tiến độ trong hệ thống</p>
            </div>
            <a href="{{ route('admin.progress_updates.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Thêm báo cáo
            </a>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.progress_updates.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="task_id" class="block text-sm font-medium text-gray-700 mb-2">Công việc</label>
                <select name="task_id" id="task_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả công việc</option>
                    @foreach(App\Models\Admin\Task::all() as $task)
                        <option value="{{ $task->id }}" {{ request('task_id') == $task->id ? 'selected' : '' }}>
                            {{ $task->task_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Từ ngày</label>
                <input type="date" name="date_from" id="date_from"
                       value="{{ request('date_from') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Đến ngày</label>
                <input type="date" name="date_to" id="date_to"
                       value="{{ request('date_to') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    <i class="fas fa-filter mr-2"></i>Lọc
                </button>
                <a href="{{ route('admin.progress_updates.index') }}"
                   class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors text-center">
                    <i class="fas fa-redo mr-2"></i>Đặt lại
                </a>
            </div>
        </form>
    </div>

    <!-- Danh sách báo cáo -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($progressUpdates->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ngày báo cáo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Công việc
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tiến độ
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Người báo cáo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Thao tác
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($progressUpdates as $update)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $update->date->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $update->created_at->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <a href="{{ route('admin.tasks.show', $update->task_id) }}" 
                                       class="text-blue-600 hover:text-blue-800">
                                        {{ $update->task->task_name ?? 'N/A' }}
                                    </a>
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $update->task->site->site_name ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full" 
                                             style="width: {{ $update->progress_percent }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ $update->progress_percent }}%
                                    </span>
                                </div>
                                @if($update->description)
                                <div class="text-xs text-gray-500 mt-1 truncate max-w-xs">
                                    {{ Str::limit($update->description, 50) }}
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $update->created_by }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.progress_updates.show', $update) }}" 
                                       class="text-blue-600 hover:text-blue-900" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.progress_updates.edit', $update) }}" 
                                       class="text-yellow-600 hover:text-yellow-900" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.progress_updates.destroy', $update) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900" 
                                                title="Xóa"
                                                onclick="return confirm('Xóa báo cáo này?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Phân trang -->
            @if($progressUpdates->hasPages())
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                {{ $progressUpdates->links() }}
            </div>
            @endif
        @else
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-chart-line text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Chưa có báo cáo tiến độ</h3>
                <p class="text-gray-500 mb-6">Hãy thêm báo cáo đầu tiên để bắt đầu theo dõi tiến độ</p>
                <a href="{{ route('admin.progress_updates.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-white hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Thêm báo cáo đầu tiên
                </a>
            </div>
        @endif
    </div>
</div>
@endsection