@extends('layouts.app')

@section('title', 'Hợp đồng của tôi')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div class="mb-4 md:mb-0">
            <h1 class="text-3xl font-bold text-gray-900">Hợp đồng của tôi</h1>
            <p class="text-gray-600 mt-2">Danh sách các hợp đồng liên quan đến tài khoản của bạn</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('client.contracts.index') }}" class="space-y-4 md:space-y-0 md:grid md:grid-cols-6 md:gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Số hợp đồng, tên hợp đồng..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Dự án</label>
                <select name="project_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả dự án</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->project_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                <select name="status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    @foreach(\App\Http\Controllers\Admin\ContractsController::getStatuses() as $value => $label)
                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end space-x-2 md:col-span-1">
                <button type="submit" 
                        class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Lọc
                </button>
                @if(request()->hasAny(['search', 'project_id', 'status']))
                    <a href="{{ route('client.contracts.index') }}" 
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        <i class="fas fa-undo"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-md p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="ml-4">
                    <div class="text-lg font-bold text-gray-900">{{ $contracts->total() }}</div>
                    <div class="text-sm text-gray-600">Tổng hợp đồng</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="ml-4">
                    <div class="text-lg font-bold text-gray-900">{{ $activeCount }}</div>
                    <div class="text-sm text-gray-600">Đang thực hiện</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="ml-4">
                    <div class="text-lg font-bold text-gray-900">{{ $pendingCount }}</div>
                    <div class="text-sm text-gray-600">Chờ phê duyệt</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="ml-4">
                    <div class="text-lg font-bold text-gray-900">{{ number_format($totalValue) }} đ</div>
                    <div class="text-sm text-gray-600">Tổng giá trị</div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hợp đồng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dự án</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Đối tác</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giá trị</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thời hạn</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($contracts as $contract)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="ml-2">
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('client.contracts.show', $contract) }}" class="hover:text-blue-600">
                                            {{ $contract->contract_number ?: 'HĐ-' . str_pad($contract->id, 6, '0', STR_PAD_LEFT) }}
                                        </a>
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $contract->contract_name ?: 'Không có tên' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $contract->project->project_name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if(auth()->user()->user_type === 'owner')
                                <div class="text-sm text-gray-900">{{ $contract->contractor->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500 italic">Nhà thầu</div>
                            @else
                                <div class="text-sm text-gray-900">{{ $contract->owner->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500 italic">Chủ đầu tư</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-900">{{ number_format($contract->contract_value) }} đ</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $contract->due_date ? $contract->due_date->format('d/m/Y') : 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($contract->status == 'active') bg-green-100 text-green-800
                                @elseif($contract->status == 'pending_signature' || $contract->status == 'draft') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ \App\Http\Controllers\Admin\ContractsController::getStatuses()[$contract->status] ?? $contract->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center space-x-3">
                                <a href="{{ route('client.contracts.show', $contract) }}" class="text-blue-600 hover:text-blue-900" title="Xem chi tiết">
                                    <i class="fas fa-eye w-5 h-5"></i>
                                </a>
                                
                                {{-- Nút phê duyệt dành riêng cho Chủ đầu tư --}}
                                @if(auth()->user()->user_type === 'owner' && ($contract->status == 'draft' || $contract->status == 'pending_signature'))
                                    <form action="{{ route('client.contracts_approve', $contract) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900" 
                                                onclick="return confirm('Bạn có chắc muốn phê duyệt hợp đồng này?')">
                                            <i class="fas fa-check-circle w-5 h-5"></i> Duyệt
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            Không có hợp đồng nào liên quan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contracts->hasPages())
        <div class="bg-white px-6 py-4 border-t border-gray-200">
            {{ $contracts->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection