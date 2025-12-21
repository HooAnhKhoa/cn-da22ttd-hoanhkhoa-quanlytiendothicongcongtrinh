@extends('layouts.app')

@section('title', 'Quản lý công trường')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Quản lý công trường</h1>
        <p class="text-gray-600">Danh sách tất cả công trường trong hệ thống</p>
    </div>
    <a href="{{ route('sites.create') }}" 
       class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 flex items-center">
        <i class="fas fa-plus mr-2"></i>Thêm công trường
    </a>
</div>

<!-- Bộ lọc và tìm kiếm -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4"> <!-- Đổi từ 4 thành 5 -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
            <input type="text" id="search" placeholder="Tìm kiếm công trường..." 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <!-- Thêm filter theo dự án -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Dự án</label>
            <select id="project-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả dự án</option>
                @foreach($projects as $project)
                <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
            <select id="status-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả</option>
                <option value="planned">Lập kế hoạch</option>
                <option value="in_progress">Đang thi công</option>
                <option value="completed">Hoàn thành</option>
                <option value="on_hold">Tạm dừng</option>
                <option value="cancelled">Đã hủy</option>
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

<!-- Danh sách công trường -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên công trường</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dự án</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày bắt đầu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiến độ</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($sites as $site)
                <tr class="hover:bg-gray-50">
                    {{-- tên công trường --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-location-dot text-blue-600 text-lg"></i>
                            </div>

                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $site->site_name }}</div>
                                @if($site->description)
                                    <div class="text-sm text-gray-500">{{ Str::limit($site->description, 50) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    {{-- dự án --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{  $site->project->project_name ?? 'N/A' }}</div>
                    </td>
                    {{-- ngày bắt đầu --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $site->start_date ? $site->start_date : 'N/A' }}
                    </td>
                    {{-- tiến độ --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $site->progress_percent }}%"></div>
                            </div>
                            <span class="text-sm text-gray-600">{{ $site->progress_percent }}%</span>
                        </div>
                    </td>
                    {{-- trạng thái --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            @if($site->status == 'planned') bg-blue-100 text-blue-800
                            @elseif($site->status == 'in_progress') bg-green-100 text-green-800
                            @elseif($site->status == 'completed') bg-gray-100 text-gray-800
                            @elseif($site->status == 'on_hold') bg-yellow-100 text-yellow-800
                            @elseif($site->status == 'cancelled') bg-red-100 text-red-800
                            @endif">
                            @if($site->status == 'planned') Lập kế hoạch
                            @elseif($site->status == 'in_progress') Đang thi công
                            @elseif($site->status == 'completed') Hoàn thành
                            @elseif($site->status == 'on_hold') Tạm dừng
                            @elseif($site->status == 'cancelled') Đã hủy
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('sites.show', $site) }}" 
                               class="text-blue-600 hover:text-blue-900" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('sites.edit', $site) }}" 
                               class="text-green-600 hover:text-green-900" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('sites.destroy', $site) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-900" 
                                        title="Xóa"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa công trường này?')">
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
                        <p>Chưa có công trường nào</p>
                        <a href="{{ route('sites.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">Tạo công trường đầu tiên</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Phân trang -->
    @if($sites->hasPages())
    <div class="bg-white px-6 py-4 border-t border-gray-200">
        {{ $sites->links() }}
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const projectFilter = document.getElementById('project-filter');
    const statusFilter = document.getElementById('status-filter');
    const sortSelect = document.getElementById('sort');
    const resetButton = document.getElementById('reset-filters');

    function applyFilters() {
        const params = new URLSearchParams();
        
        if (searchInput.value) params.set('search', searchInput.value);
        if (projectFilter.value) params.set('project', projectFilter.value);
        if (statusFilter.value) params.set('status', statusFilter.value);
        if (sortSelect.value) params.set('sort', sortSelect.value);
        
        window.location.href = '{{ route('sites.index') }}?' + params.toString();
    }

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') applyFilters();
    });

    projectFilter.addEventListener('change', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
    sortSelect.addEventListener('change', applyFilters);

    resetButton.addEventListener('click', function() {
        window.location.href = '{{ route('sites.index') }}';
    });

    // Set current values from URL
    const urlParams = new URLSearchParams(window.location.search);
    searchInput.value = urlParams.get('search') || '';
    projectFilter.value = urlParams.get('project') || '';
    statusFilter.value = urlParams.get('status') || '';
    sortSelect.value = urlParams.get('sort') || 'newest';
});
</script>
@endsection