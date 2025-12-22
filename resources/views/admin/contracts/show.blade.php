@extends('layouts.app')

@section('title', 'Chi tiết Hợp đồng')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div class="mb-4 md:mb-0">
            <h1 class="text-3xl font-bold text-gray-900">Chi tiết Hợp đồng</h1>
            <div class="flex items-center space-x-4 mt-2">
                <span class="text-gray-600">Mã hợp đồng: <strong>{{ $contract->contract_code ?? 'N/A' }}</strong></span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                    @if($contract->status == 'active') bg-green-100 text-green-800
                    @elseif($contract->status == 'completed') bg-blue-100 text-blue-800
                    @elseif($contract->status == 'pending') bg-yellow-100 text-yellow-800
                    @elseif($contract->status == 'cancelled') bg-red-100 text-red-800
                    @elseif($contract->status == 'suspended') bg-gray-100 text-gray-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ App\Http\Controllers\Admin\ContractsController::getStatuses()[$contract->status] ?? $contract->status }}
                </span>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.contracts.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại
            </a>
            <a href="{{ route('admin.contracts.edit', $contract) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Chỉnh sửa
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Contract Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contract Information Card -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Thông tin Hợp đồng</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Giá trị hợp đồng</label>
                        <div class="mt-1 text-xl font-bold text-green-600">
                            {{ number_format($contract->contract_value) }} VND
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Đã thanh toán</label>
                        <div class="mt-1 text-xl font-bold text-blue-600">
                            {{ number_format($totalPaid) }} VND
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Còn lại</label>
                        <div class="mt-1 text-xl font-bold text-gray-600">
                            {{ number_format($remaining) }} VND
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Tiến độ thanh toán</label>
                        <div class="mt-1">
                            <div class="flex items-center">
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ min($progress, 100) }}%"></div>
                                </div>
                                <span class="ml-2 text-sm font-medium text-gray-700">{{ round($progress, 1) }}%</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Ngày ký</label>
                        <div class="mt-1 text-gray-900">
                            {{ $contract->signed_date ? $contract->signed_date->format('d/m/Y') : 'N/A' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Hạn hoàn thành</label>
                        <div class="mt-1 text-gray-900">
                            {{ $contract->due_date ? $contract->due_date->format('d/m/Y') : 'N/A' }}
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500">Mô tả</label>
                        <div class="mt-1 text-gray-900">
                            {{ $contract->description ?? 'Không có mô tả' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Information Card -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Thông tin Dự án</h2>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                @if($contract->project)
                                    <a href="{{ route('admin.projects.show', $contract->project) }}" class="hover:text-blue-600">
                                        {{ $contract->project->project_name }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </div>
                            <div class="text-sm text-gray-500">
                                @if($contract->project)
                                    {{ $contract->project->location ?? 'N/A' }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Chủ đầu tư</label>
                            <div class="mt-1 text-gray-900">
                                {{ $contract->project->owner->username ?? 'N/A' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Trạng thái dự án</label>
                            <div class="mt-1">
                                @if($contract->project)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($contract->project->status == 'in_progress') bg-green-100 text-green-800
                                        @elseif($contract->project->status == 'completed') bg-blue-100 text-blue-800
                                        @elseif($contract->project->status == 'on_hold') bg-yellow-100 text-yellow-800
                                        @elseif($contract->project->status == 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        @if($contract->project->status == 'in_progress')
                                            Đang thi công
                                        @elseif($contract->project->status == 'completed')
                                            Hoàn thành
                                        @elseif($contract->project->status == 'on_hold')
                                            Tạm dừng
                                        @elseif($contract->project->status == 'cancelled')
                                            Đã hủy
                                        @else
                                            {{ $contract->project->status }}
                                        @endif
                                    </span>
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contractor Information Card -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Thông tin Nhà thầu</h2>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $contract->contractor->name ?? 'N/A' }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $contract->contractor->email ?? '' }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $contract->contractor->phone ?? '' }}
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Địa chỉ</label>
                        <div class="mt-1 text-gray-900">
                            {{ $contract->contractor->address ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Payments and Actions -->
        <div class="space-y-6">
            <!-- Payment Summary Card -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Thanh toán</h2>
                    <a href="{{ route('admin.payments.create', ['contract_id' => $contract->id]) }}" 
                       class="text-sm text-blue-600 hover:text-blue-800">
                        + Thêm thanh toán
                    </a>
                </div>
                
                <div class="space-y-3">
                    @forelse($contract->payments->take(5) as $payment)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="text-sm font-medium text-gray-900">
                                {{ number_format($payment->amount) }} VND
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $payment->pay_date ? $payment->pay_date->format('d/m/Y') : 'N/A' }}
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                @if($payment->method == 'cash') bg-green-100 text-green-800
                                @elseif($payment->method == 'bank_transfer') bg-blue-100 text-blue-800
                                @elseif($payment->method == 'credit_card') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $payment->method == 'cash' ? 'Tiền mặt' : 
                                   ($payment->method == 'bank_transfer' ? 'Chuyển khoản' : 
                                   ($payment->method == 'credit_card' ? 'Thẻ tín dụng' : 'Khác')) }}
                            </span>
                            <a href="{{ route('admin.payments.show', $payment) }}" 
                               class="text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>Chưa có thanh toán nào</p>
                    </div>
                    @endforelse
                    
                    @if($contract->payments->count() > 5)
                    <div class="text-center pt-2">
                        <a href="{{ route('admin.payments.index', ['contract_id' => $contract->id]) }}" 
                           class="text-sm text-blue-600 hover:text-blue-800">
                            Xem tất cả {{ $contract->payments->count() }} thanh toán
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Contract Terms Card -->
            @if($contract->terms)
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Điều khoản Hợp đồng</h2>
                <div class="prose max-w-none text-sm text-gray-700">
                    {!! nl2br(e($contract->terms)) !!}
                </div>
            </div>
            @endif

            <!-- Actions Card -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Thao tác</h2>
                <div class="space-y-3">
                    <a href="{{ route('admin.contracts.edit', $contract) }}" 
                       class="w-full flex items-center justify-center px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Chỉnh sửa hợp đồng
                    </a>
                    
                    <form action="{{ route('admin.contracts.destroy', $contract) }}" 
                          method="POST" 
                          onsubmit="return confirm('Bạn có chắc muốn xóa hợp đồng này? Hành động này không thể hoàn tác.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full flex items-center justify-center px-4 py-2 border border-red-600 text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Xóa hợp đồng
                        </button>
                    </form>
                    
                    @if($contract->status == 'pending')
                    <form action="{{ route('admin.contracts.approve', $contract) }}" 
                          method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Phê duyệt hợp đồng
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Lịch sử</h2>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                            <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900">Tạo hợp đồng</div>
                            <div class="text-sm text-gray-500">
                                {{ $contract->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                            <div class="w-2 h-2 bg-green-600 rounded-full"></div>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900">Ký hợp đồng</div>
                            <div class="text-sm text-gray-500">
                                {{ $contract->signed_date ? $contract->signed_date->format('d/m/Y') : 'Chưa ký' }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center">
                            <div class="w-2 h-2 bg-yellow-600 rounded-full"></div>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900">Hạn hoàn thành</div>
                            <div class="text-sm text-gray-500">
                                {{ $contract->due_date ? $contract->due_date->format('d/m/Y') : 'N/A' }}
                                @if($contract->due_date && $contract->due_date->isPast() && $contract->status == 'active')
                                    <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        Quá hạn
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection