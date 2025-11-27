@extends('layouts.app')

@section('title', 'Quản lý dự án')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Quản lý dự án</h1>
        <p class="text-gray-600">Danh sách tất cả dự án trong hệ thống</p>
    </div>
    <a href="{{ route('projects.create') }}" 
       class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 flex items-center">
        <i class="fas fa-plus mr-2"></i>Thêm dự án
    </a>
</div>

<!-- Bộ lọc và tìm kiếm -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
            <input type="text" id="search" placeholder="Tìm kiếm dự án..." 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
            <select id="status-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả</option>
                <option value="planned">Đã lên kế hoạch</option>
                <option value="in_progress">Đang thi công</option>
                <option value="completed">Hoàn thành</option>
                <option value="on_hold">Tạm dừng</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Sắp xếp</label>
            <select id="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="newest">Mới nhất</option>
                <option value="oldest">Cũ nhất</option>
                <option value="name">Tên A-Z</option>
            </select>
        </div>
        <div class="flex items-end">
            <button id="reset-filters" class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                <i class="fas fa-redo mr-2"></i>Đặt lại
            </button>
        </div>
    </div>
</div>

<!-- Danh sách dự án -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên dự án</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Địa điểm</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chủ đầu tư</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày bắt đầu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($projects as $project)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-project-diagram text-blue-600"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $project->project_name }}</div>
                                <div class="text-sm text-gray-500">Ngân sách: {{ number_format($project->total_budget) }} VNĐ</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $project->location }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $project->owner->username }}</div>
                        <div class="text-sm text-gray-500">{{ $project->owner->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $project->start_date->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            @if($project->status == 'in_progress') bg-green-100 text-green-800
                            @elseif($project->status == 'completed') bg-blue-100 text-blue-800
                            @elseif($project->status == 'on_hold') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800 @endif">
                            @if($project->status == 'in_progress') Đang thi công
                            @elseif($project->status == 'completed') Hoàn thành
                            @elseif($project->status == 'on_hold') Tạm dừng
                            @else Đã lên kế hoạch @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('projects.show', $project) }}" 
                               class="text-blue-600 hover:text-blue-900" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('projects.edit', $project) }}" 
                               class="text-green-600 hover:text-green-900" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-900" 
                                        title="Xóa"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa dự án này?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                        <p>Chưa có dự án nào</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Phân trang -->
    @if($projects->hasPages())
    <div class="bg-white px-6 py-4 border-t border-gray-200">
        {{ $projects->links() }}
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const statusFilter = document.getElementById('status-filter');
    const sortSelect = document.getElementById('sort');
    const resetButton = document.getElementById('reset-filters');

    function applyFilters() {
        const params = new URLSearchParams();
        
        if (searchInput.value) params.set('search', searchInput.value);
        if (statusFilter.value) params.set('status', statusFilter.value);
        if (sortSelect.value) params.set('sort', sortSelect.value);
        
        window.location.href = '{{ route('projects.index') }}?' + params.toString();
    }

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') applyFilters();
    });

    statusFilter.addEventListener('change', applyFilters);
    sortSelect.addEventListener('change', applyFilters);

    resetButton.addEventListener('click', function() {
        window.location.href = '{{ route('projects.index') }}';
    });

    // Set current values from URL
    const urlParams = new URLSearchParams(window.location.search);
    searchInput.value = urlParams.get('search') || '';
    statusFilter.value = urlParams.get('status') || '';
    sortSelect.value = urlParams.get('sort') || 'newest';
});
</script>
@endsection