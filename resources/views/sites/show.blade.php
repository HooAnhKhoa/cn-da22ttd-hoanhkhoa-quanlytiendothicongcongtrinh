@extends('layouts.app')

@section('title', $site->site_name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    {{-- <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ $site->site_name }}</h1>
            <p class="text-gray-600">Chi tiết công trường</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('sites.edit', $site) }}" 
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                <i class="fas fa-edit mr-2"></i>Chỉnh sửa
            </a>
            <a href="{{ route('sites.index') }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại
            </a>
        </div>
    </div> --}}
<div class="mb-6">
    <div class="flex justify-between items-start">
        <div>
            <nav class="mb-4">
                <a href="{{ route('sites.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800">{{ $site->site_name }}</h1>
            <p class="text-gray-600">Chi tiết công trường</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('sites.edit', $site) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
                <i class="fas fa-edit mr-2"></i>Chỉnh sửa
            </a>
        </div>
    </div>
</div>

    <!-- Thông báo -->
    @include('components.alert')

    <!-- Thông tin chính -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Thông tin cơ bản -->
        <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Thông tin công trường</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên công trường</label>
                    <p class="text-gray-900">{{ $site->site_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dự án</label>
                    <p class="text-gray-900">{{ $site->project->project_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày bắt đầu</label>
                    <p class="text-gray-900">{{ $site->start_date ? $site->start_date : 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày kết thúc</label>
                    <p class="text-gray-900">{{ $site->end_date ? $site->end_date : 'Chưa có ngày' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
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
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tiến độ</label>
                    <div class="flex items-center">
                        <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $site->progress_percent }}%"></div>
                        </div>
                        <span class="text-sm text-gray-600">{{ $site->progress_percent }}%</span>
                    </div>
                </div>
            </div>
            
            @if($site->description)
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                <p class="text-gray-900">{{ $site->description }}</p>
            </div>
            @endif
        </div>

        <!-- Thông tin bổ sung -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Thông tin bổ sung</h2>
            <div class="space-y-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Ngày tạo</p>
                        <p class="text-sm text-gray-500">{{ $site->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-edit text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Cập nhật lần cuối</p>
                        <p class="text-sm text-gray-500">{{ $site->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-hard-hat text-purple-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Loại</p>
                        <p class="text-sm text-gray-500">Công trường xây dựng</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tiến độ chi tiết -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Tiến độ chi tiết</h2>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700">Tiến độ tổng thể</span>
                    <span class="text-sm font-medium text-gray-700">{{ $site->progress_percent }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                         style="width: {{ $site->progress_percent }}%"></div>
                </div>
            </div>
            
            <!-- Các hạng mục công việc (có thể thêm sau) -->
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Các hạng mục chính</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-700">Chuẩn bị mặt bằng</span>
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Hoàn thành</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-700">Thi công móng</span>
                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Đang thực hiện</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-700">Thi công phần thô</span>
                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Chưa bắt đầu</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê nhanh -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Công nhân</p>
                    <p class="text-2xl font-bold text-gray-900">25</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tasks text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Công việc</p>
                    <p class="text-2xl font-bold text-gray-900">48</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Vấn đề</p>
                    <p class="text-2xl font-bold text-gray-900">3</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Trễ hạn</p>
                    <p class="text-2xl font-bold text-gray-900">2</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Hành động -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Hành động</h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('sites.edit', $site) }}" 
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                <i class="fas fa-edit mr-2"></i>Chỉnh sửa công trường
            </a>
            <form action="{{ route('sites.destroy', $site) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 flex items-center"
                        onclick="return confirm('Bạn có chắc muốn xóa công trường này?')">
                    <i class="fas fa-trash mr-2"></i>Xóa công trường
                </button>
            </form>
            <a href="#" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                <i class="fas fa-file-pdf mr-2"></i>Xuất báo cáo
            </a>
            <a href="#" 
               class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 flex items-center">
                <i class="fas fa-chart-line mr-2"></i>Xem thống kê
            </a>
        </div>
    </div>
</div>

<style>
.transition-all {
    transition: all 0.3s ease-in-out;
}
</style>
@endsection