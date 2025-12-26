@extends('layouts.app')

@section('title', 'Chi tiết Thanh toán')

@section('content')
@php
    $methodLabels = [
        'cash' => 'Tiền mặt',
        'bank_transfer' => 'Chuyển khoản',
        'credit_card' => 'Thẻ tín dụng',
        'other' => 'Khác'
    ];
    
    $methodIcons = [
        'cash' => 'fas fa-money-bill-wave',
        'bank_transfer' => 'fas fa-university',
        'credit_card' => 'fas fa-credit-card',
        'other' => 'fas fa-wallet'
    ];
    
    $methodColors = [
        'cash' => 'bg-green-100 text-green-800',
        'bank_transfer' => 'bg-blue-100 text-blue-800',
        'credit_card' => 'bg-purple-100 text-purple-800',
        'other' => 'bg-gray-100 text-gray-800'
    ];
@endphp

<div class="mb-6">
    <div class="flex justify-between items-start">
        <div>
            <nav class="mb-4">
                <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800">Thanh toán #PMT-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</h1>
            <p class="text-xl text-gray-600 mt-2">
                Hợp đồng: {{ $payment->contract->contract_number ?? 'N/A' }}
            </p>
        </div>
        <div class="flex gap-2">
            @if($payment->receipt_path)
            <form action="{{ route('admin.payments.download-receipt', $payment) }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>Tải biên lai
                </button>
            </form>
            @endif
            {{-- <a href="{{ route('admin.payments.edit', $payment) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
                <i class="fas fa-edit mr-2"></i>Chỉnh sửa
            </a> --}}
        </div>
    </div>
</div>

<!-- Thông báo -->
@include('components.alert')

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Payment Information -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-info-circle mr-2"></i>Thông tin thanh toán
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Mã thanh toán:</span>
                    <span class="font-medium text-gray-800">#PMT-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Số tiền:</span>
                    <span class="font-medium text-green-600">
                        {{ number_format($payment->amount) }} VNĐ
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngày thanh toán:</span>
                    <span class="font-medium text-gray-800">
                        {{ $payment->pay_date ? \Carbon\Carbon::parse($payment->pay_date)->format('d/m/Y') : 'Chưa xác định' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Thời gian:</span>
                    <span class="font-medium text-gray-800">
                        {{ $payment->pay_date ? \Carbon\Carbon::parse($payment->pay_date)->format('H:i') : 'N/A' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Phương thức:</span>
                    <span class="font-medium text-gray-800">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $methodColors[$payment->method] ?? 'bg-gray-100 text-gray-800' }}">
                            <i class="{{ $methodIcons[$payment->method] ?? 'fas fa-wallet' }} mr-1"></i>
                            {{ $methodLabels[$payment->method] ?? $payment->method }}
                        </span>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Mã tham chiếu:</span>
                    <span class="font-medium text-gray-800">
                        {{ $payment->reference_number ?: 'Không có' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Trạng thái:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Đã hoàn thành
                    </span>
                </div>
                
                <!-- Statistics Box -->
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Giá trị hợp đồng</span>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-bold text-green-600">{{ number_format($payment->contract->contract_value) }}</span>
                        <p class="text-xs text-gray-500">VNĐ</p>
                    </div>
                </div>
            </div>
            
            @if($payment->notes)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-2">Ghi chú:</h4>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-600">{{ $payment->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Contract Information -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-file-contract mr-2"></i>Thông tin hợp đồng
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <!-- Contract -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">
                                <a href="{{ route('admin.contracts.show', $payment->contract) }}" class="hover:text-blue-600">
                                    {{ $payment->contract->contract_number }}
                                </a>
                            </div>
                            <div class="text-sm text-gray-500">Hợp đồng</div>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ $payment->contract->contract_name }}</span>
                </div>

                <!-- Project -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">
                                <a href="{{ route('admin.projects.show', $payment->contract->project) }}" class="hover:text-blue-600">
                                    {{ $payment->contract->project->project_name }}
                                </a>
                            </div>
                            <div class="text-sm text-gray-500">Dự án</div>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ $payment->contract->project->location ?? '' }}</span>
                </div>

                <!-- Contractor/Owner -->
                @if(auth()->user()->user_type === 'owner')
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-hard-hat"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">{{ $payment->contract->contractor->username ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">Nhà thầu</div>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ $payment->contract->contractor->email ?? '' }}</span>
                </div>
                @else
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">{{ $payment->contract->owner->username ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">Chủ đầu tư</div>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ $payment->contract->owner->email ?? '' }}</span>
                </div>
                @endif
            </div>
            
            <!-- Statistics Grid -->
            <div class="mt-6 grid grid-cols-2 gap-4">
                <div class="text-center p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-600">Giá trị hợp đồng</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($payment->contract->contract_value) }}</p>
                    <p class="text-xs text-gray-500">VNĐ</p>
                </div>
                <div class="text-center p-3 bg-green-50 rounded-lg">
                    <p class="text-sm text-gray-600">Đã thanh toán</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($payment->amount) }}</p>
                    <p class="text-xs text-gray-500">VNĐ</p>
                </div>
                <div class="text-center p-3 bg-yellow-50 rounded-lg">
                    <p class="text-sm text-gray-600">Thanh toán lần này</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($payment->amount) }}</p>
                    <p class="text-xs text-gray-500">VNĐ</p>
                </div>
                <div class="text-center p-3 bg-red-50 rounded-lg">
                    <p class="text-sm text-gray-600">Tổng thanh toán</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($payment->contract->payments->sum('amount')) }}
                    </p>
                    <p class="text-xs text-gray-500">VNĐ</p>
                </div>
            </div>
            
            <!-- Timeline -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-3">Lịch sử thanh toán</h4>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-plus text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-800 text-sm">Tạo thanh toán</div>
                            <div class="text-xs text-gray-500">{{ $payment->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    @if($payment->pay_date)
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-800 text-sm">Thanh toán hoàn tất</div>
                            <div class="text-xs text-gray-500">{{ $payment->pay_date->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    @endif
                    @if($payment->receipt_path)
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-receipt text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-800 text-sm">Biên lai tạo</div>
                            <div class="text-xs text-gray-500">{{ $payment->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
    <div class="text-sm text-gray-500">
        Tạo ngày: {{ $payment->created_at->format('d/m/Y H:i') }}
        • Cập nhật: {{ $payment->updated_at->format('d/m/Y H:i') }}
        • Hợp đồng: {{ $payment->contract->contract_number }}
    </div>
    <div class="flex gap-2">
        {{-- <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST" class="inline"> --}}
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 transition-colors"
                    onclick="return confirm('Bạn có chắc chắn muốn xóa thanh toán này?')">
                <i class="fas fa-trash mr-2"></i>Xóa thanh toán
            </button>
        </form>
        {{-- <a href="{{ route('admin.payments.edit', $payment) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
            <i class="fas fa-edit mr-2"></i>Chỉnh sửa
        </a> --}}
        @if($payment->receipt_path)
        <form action="{{ route('admin.payments.download-receipt', $payment) }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 transition-colors">
                <i class="fas fa-file-pdf mr-2"></i>Xuất biên lai
            </button>
        </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            // Remove active classes from all tabs
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
                content.classList.add('hidden');
            });
            
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Add active class to clicked tab
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('active', 'border-blue-500', 'text-blue-600');
            
            // Show corresponding content
            const target = document.getElementById('tab-' + tabName);
            if (target) {
                target.classList.remove('hidden');
                target.classList.add('active');
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.hover\:bg-gray-50:hover {
    transition: background-color 0.2s ease;
}

.progress-bar {
    transition: width 1s ease-in-out;
}
</style>
@endpush