@extends('layouts.app')

@section('title', 'Quản lý Hợp đồng')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Quản lý Hợp đồng</h1>
        <p class="text-gray-600">Danh sách tất cả hợp đồng trong hệ thống</p>
    </div>
    <a href="{{ route('admin.contracts.create') }}" 
       class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 flex items-center">
        <i class="fas fa-plus mr-2"></i>Tạo hợp đồng mới
    </a>
</div>

<!-- Bộ lọc và tìm kiếm -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form method="GET" action="{{ route('admin.contracts.index') }}">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Tìm kiếm hợp đồng..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dự án</label>
                <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tất cả dự án</option>
                    @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->project_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nhà thầu</label>
                <select name="contractor_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tất cả nhà thầu</option>
                    @foreach($contractors as $contractor)
                    <option value="{{ $contractor->id }}" {{ request('contractor_id') == $contractor->id ? 'selected' : '' }}>
                        {{ $contractor->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Tạm dừng</option>
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sắp xếp</label>
                <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                    <option value="value_asc" {{ request('sort') == 'value_asc' ? 'selected' : '' }}>Giá trị tăng dần</option>
                    <option value="value_desc" {{ request('sort') == 'value_desc' ? 'selected' : '' }}>Giá trị giảm dần</option>
                    <option value="due_soon" {{ request('sort') == 'due_soon' ? 'selected' : '' }}>Sắp đến hạn</option>
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" 
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center justify-center">
                    <i class="fas fa-filter mr-2"></i>Áp dụng bộ lọc
                </button>
                <a href="{{ route('admin.contracts.index') }}" 
                   class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 flex items-center justify-center">
                    <i class="fas fa-redo mr-2"></i>Đặt lại
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Thống kê nhanh -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-blue-50 p-4 rounded-lg">
        <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg mr-3">
                <i class="fas fa-file-contract text-blue-600"></i>
            </div>
            <div>
                <div class="text-blue-600 font-bold text-xl">{{ $contracts->total() }}</div>
                <div class="text-gray-600 text-sm">Tổng hợp đồng</div>
            </div>
        </div>
    </div>
    
    <div class="bg-green-50 p-4 rounded-lg">
        <div class="flex items-center">
            <div class="p-2 bg-green-100 rounded-lg mr-3">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
            <div>
                <div class="text-green-600 font-bold text-xl">{{ $activeCount }}</div>
                <div class="text-gray-600 text-sm">Đang hoạt động</div>
            </div>
        </div>
    </div>
    
    <div class="bg-yellow-50 p-4 rounded-lg">
        <div class="flex items-center">
            <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                <i class="fas fa-clock text-yellow-600"></i>
            </div>
            <div>
                <div class="text-yellow-600 font-bold text-xl">{{ $pendingCount }}</div>
                <div class="text-gray-600 text-sm">Chờ xử lý</div>
            </div>
        </div>
    </div>
    
    <div class="bg-purple-50 p-4 rounded-lg">
        <div class="flex items-center">
            <div class="p-2 bg-purple-100 rounded-lg mr-3">
                <i class="fas fa-money-bill-wave text-purple-600"></i>
            </div>
            <div>
                <div class="text-purple-600 font-bold text-xl">{{ number_format($totalValue) }} đ</div>
                <div class="text-gray-600 text-sm">Tổng giá trị</div>
            </div>
        </div>
    </div>
    
    <div class="bg-red-50 p-4 rounded-lg">
        <div class="flex items-center">
            <div class="p-2 bg-red-100 rounded-lg mr-3">
                <i class="fas fa-calendar-times text-red-600"></i>
            </div>
            <div>
                <div class="text-red-600 font-bold text-xl">{{ $overdueCount }}</div>
                <div class="text-gray-600 text-sm">Quá hạn</div>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách hợp đồng -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã hợp đồng</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dự án</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nhà thầu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá trị</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời hạn</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thanh toán</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($contracts as $contract)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">HĐ-{{ str_pad($contract->id, 6, '0', STR_PAD_LEFT) }}</div>
                        <div class="text-sm text-gray-500">{{ $contract->signed_date->format('d/m/Y') }}</div>
                    </td>
                    
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $contract->project->project_name ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-500">ID: {{ $contract->project_id }}</div>
                    </td>
                    
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-blue-600 text-xs"></i>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $contract->contractor->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $contract->contractor->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900">{{ number_format($contract->contract_value) }} đ</div>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $contract->due_date->format('d/m/Y') }}</div>
                        @php
                            $daysLeft = now()->diffInDays($contract->due_date, false);
                        @endphp
                        <div class="text-xs {{ $daysLeft < 0 ? 'text-red-600' : ($daysLeft < 30 ? 'text-yellow-600' : 'text-green-600') }}">
                            @if($daysLeft < 0)
                                Quá hạn {{ abs($daysLeft) }} ngày
                            @elseif($daysLeft == 0)
                                Hết hạn hôm nay
                            @else
                                Còn {{ $daysLeft }} ngày
                            @endif
                        </div>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            @if($contract->status == 'active') bg-green-100 text-green-800
                            @elseif($contract->status == 'completed') bg-blue-100 text-blue-800
                            @elseif($contract->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($contract->status == 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ \App\Http\Controllers\Admin\ContractsController::getStatuses()[$contract->status] ?? $contract->status }}
                        </span>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $totalPaid = $contract->payments->sum('amount');
                            $progress = $contract->contract_value > 0 ? ($totalPaid / $contract->contract_value) * 100 : 0;
                        @endphp
                        <div class="flex items-center">
                            <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($progress, 100) }}%"></div>
                            </div>
                            <span class="text-sm text-gray-600">{{ round($progress, 1) }}%</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ number_format($totalPaid) }}/{{ number_format($contract->contract_value) }} đ
                        </div>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.contracts.show', $contract) }}" 
                               class="text-blue-600 hover:text-blue-900" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.contracts.edit', $contract) }}" 
                               class="text-green-600 hover:text-green-900" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.contracts.destroy', $contract) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-900" 
                                        title="Xóa"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa hợp đồng này?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                            <i class="fas fa-file-contract text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-lg font-medium text-gray-700 mb-2">Chưa có hợp đồng nào</p>
                        <p class="text-gray-500 mb-4">Hãy tạo hợp đồng đầu tiên cho dự án của bạn</p>
                        <a href="{{ route('admin.contracts.create') }}" 
                           class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-white hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Tạo hợp đồng đầu tiên
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Phân trang -->
    @if($contracts->hasPages())
    <div class="bg-white px-6 py-4 border-t border-gray-200">
        {{ $contracts->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection