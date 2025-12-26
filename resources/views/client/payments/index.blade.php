@extends('layouts.app')

@section('title', 'Lịch sử Thanh toán')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-start">
            <div>
                <nav class="mb-4">
                    <a href="{{ route('client.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Quay lại
                    </a>
                </nav>
                <h1 class="text-3xl font-bold text-gray-800">Lịch sử Thanh toán</h1>
                <p class="text-gray-600 mt-2">Danh sách các khoản thanh toán trong hợp đồng của bạn</p>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('client.payments.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Mã hợp đồng, ghi chú..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Contract Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hợp đồng</label>
                    <select name="contract_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả hợp đồng</option>
                        @foreach($contracts as $contract)
                            <option value="{{ $contract->id }}" {{ request('contract_id') == $contract->id ? 'selected' : '' }}>
                                {{ $contract->contract_number }} - {{ $contract->contract_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Method Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phương thức</label>
                    <select name="method" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả phương thức</option>
                        <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Tiền mặt</option>
                        <option value="bank_transfer" {{ request('method') == 'bank_transfer' ? 'selected' : '' }}>Chuyển khoản</option>
                        <option value="credit_card" {{ request('method') == 'credit_card' ? 'selected' : '' }}>Thẻ tín dụng</option>
                        <option value="other" {{ request('method') == 'other' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tháng</label>
                    <input type="month" 
                           name="month" 
                           value="{{ request('month') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                <div class="text-sm text-gray-500">
                    {{ $payments->total() }} thanh toán được tìm thấy
                </div>
                <div class="flex space-x-2">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Lọc
                    </button>
                    @if(request()->anyFilled(['search', 'contract_id', 'method', 'month']))
                        <a href="{{ route('client.payments.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            <i class="fas fa-times mr-2"></i>Xóa lọc
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <div class="text-lg font-bold text-gray-900">{{ number_format($stats['total_amount'] ?? 0) }} VND</div>
                    <div class="text-sm text-gray-600">Tổng thanh toán</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-receipt text-green-600"></i>
                </div>
                <div class="ml-4">
                    <div class="text-lg font-bold text-gray-900">{{ $stats['total_count'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Số giao dịch</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hand-holding-usd text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <div class="text-lg font-bold text-gray-900">{{ number_format($stats['cash_amount'] ?? 0) }} VND</div>
                    <div class="text-sm text-gray-600">Tiền mặt</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-university text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <div class="text-lg font-bold text-gray-900">{{ number_format($stats['bank_amount'] ?? 0) }} VND</div>
                    <div class="text-sm text-gray-600">Chuyển khoản</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mã thanh toán
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Hợp đồng
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Số tiền
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ngày thanh toán
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Phương thức
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-money-check-alt text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        #PMT-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $payment->created_at ? $payment->created_at->format('d/m/Y H:i') : 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                @if($payment->contract)
                                    <a href="{{ route('client.contracts.show', $payment->contract) }}" class="hover:text-blue-600">
                                        {{ $payment->contract->contract_number ?? 'N/A' }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $payment->contract->contract_name ?? '' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                @if($payment->contract)
                                    @if(auth()->user()->user_type === 'owner')
                                        <i class="fas fa-hard-hat mr-1"></i> {{ $payment->contract->contractor->username ?? 'N/A' }}
                                    @else
                                        <i class="fas fa-user-tie mr-1"></i> {{ $payment->contract->owner->username ?? 'N/A' }}
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-lg font-bold text-green-600">
                                {{ number_format($payment->amount) }} VND
                            </div>
                            @if($payment->reference_number)
                                <div class="text-xs text-gray-500 mt-1">
                                    Mã tham chiếu: {{ $payment->reference_number }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $payment->pay_date ? $payment->pay_date->format('d/m/Y') : 'N/A' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $payment->pay_date ? $payment->pay_date->format('H:i') : '' }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $methodColors = [
                                    'cash' => 'bg-green-100 text-green-800 border border-green-200',
                                    'bank_transfer' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                    'credit_card' => 'bg-purple-100 text-purple-800 border border-purple-200',
                                    'other' => 'bg-gray-100 text-gray-800 border border-gray-200'
                                ];
                                $methodIcons = [
                                    'cash' => 'fas fa-money-bill-wave',
                                    'bank_transfer' => 'fas fa-university',
                                    'credit_card' => 'fas fa-credit-card',
                                    'other' => 'fas fa-wallet'
                                ];
                                $methodLabels = [
                                    'cash' => 'Tiền mặt',
                                    'bank_transfer' => 'Chuyển khoản',
                                    'credit_card' => 'Thẻ tín dụng',
                                    'other' => 'Khác'
                                ];
                                $color = $methodColors[$payment->method] ?? 'bg-gray-100 text-gray-800';
                                $icon = $methodIcons[$payment->method] ?? 'fas fa-wallet';
                                $label = $methodLabels[$payment->method] ?? $payment->method;
                            @endphp
                            <div class="flex items-center">
                                <i class="{{ $icon }} mr-2 text-sm"></i>
                                <span class="text-xs font-medium {{ $color }} px-2 py-1 rounded">
                                    {{ $label }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                @if($payment->id)
                                <a href="{{ route('client.payments.show', $payment) }}" 
                                   class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-600 rounded-md hover:bg-blue-100 transition-colors text-sm"
                                   title="Xem chi tiết">
                                    <i class="fas fa-eye mr-1"></i> Xem
                                </a>
                                @endif
                                
                                {{-- Nút tải biên lai (nếu có) --}}
                                @if($payment->receipt_path && $payment->id)
                                <form action="{{ route('client.payments.download-receipt', $payment) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-1 bg-green-50 text-green-600 rounded-md hover:bg-green-100 transition-colors text-sm"
                                            title="Tải biên lai">
                                        <i class="fas fa-download mr-1"></i> Biên lai
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-money-bill-wave text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500 mb-2">Chưa có thanh toán nào</p>
                                <p class="text-gray-400 text-sm">Hãy kiểm tra lại sau hoặc liên hệ quản trị viên</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($payments->hasPages())
        <div class="bg-white px-6 py-4 border-t border-gray-200">
            {{ $payments->withQueryString()->links() }}
        </div>
        @endif
    </div>

    <!-- Summary -->
    <div class="mt-6 text-sm text-gray-500">
        Hiển thị {{ $payments->count() }} thanh toán trên tổng số {{ $payments->total() }}
    </div>
</div>
@endsection

@push('styles')
<style>
    .method-badge {
        transition: all 0.2s ease;
    }
    
    .method-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>
@endpush