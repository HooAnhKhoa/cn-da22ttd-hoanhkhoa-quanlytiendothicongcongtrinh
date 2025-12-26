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
        'cash' => 'bg-green-100 text-green-800 border-green-200',
        'bank_transfer' => 'bg-blue-100 text-blue-800 border-blue-200',
        'credit_card' => 'bg-purple-100 text-purple-800 border-purple-200',
        'other' => 'bg-gray-100 text-gray-800 border-gray-200'
    ];
@endphp

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div>
                <nav class="mb-4">
                    <a href="{{ route('client.payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách
                    </a>
                </nav>
                <div class="flex items-center gap-4">
                    <h1 class="text-3xl font-bold text-gray-800">Chi tiết Thanh toán</h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $methodColors[$payment->method] ?? 'bg-gray-100 text-gray-800' }} border">
                        <i class="{{ $methodIcons[$payment->method] ?? 'fas fa-wallet' }} mr-2"></i>
                        {{ $methodLabels[$payment->method] ?? $payment->method }}
                    </span>
                </div>
                <p class="text-gray-600 mt-2">Mã thanh toán: #PMT-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
            
            @if($payment->receipt_path)
            <div>
                <form action="{{ route('client.payments.download-receipt', $payment) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>Tải biên lai
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <!-- Thông báo -->
    @include('components.alert')

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Payment Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <i class="fas fa-info-circle mr-2"></i>Thông tin thanh toán
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <!-- Amount Section -->
                        <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-6 border border-green-100">
                            <div class="text-center">
                                <div class="text-4xl font-bold text-green-600 mb-2">
                                    {{ number_format($payment->amount) }} VND
                                </div>
                                <div class="text-gray-600">Số tiền thanh toán</div>
                            </div>
                        </div>

                        <!-- Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="mb-4">
                                    <span class="block text-sm font-medium text-gray-500 uppercase mb-1">Ngày thanh toán</span>
                                    <div class="flex items-center text-gray-900">
                                        <i class="fas fa-calendar-day mr-2 text-blue-500"></i>
                                        <span class="font-medium">{{ $payment->pay_date ? $payment->pay_date->format('d/m/Y') : 'N/A' }}</span>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <span class="block text-sm font-medium text-gray-500 uppercase mb-1">Thời gian</span>
                                    <div class="flex items-center text-gray-900">
                                        <i class="fas fa-clock mr-2 text-blue-500"></i>
                                        <span class="font-medium">{{ $payment->pay_date ? $payment->pay_date->format('H:i') : 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="mb-4">
                                    <span class="block text-sm font-medium text-gray-500 uppercase mb-1">Mã tham chiếu</span>
                                    <div class="flex items-center text-gray-900">
                                        <i class="fas fa-hashtag mr-2 text-blue-500"></i>
                                        <span class="font-mono bg-gray-100 px-2 py-1 rounded">
                                            {{ $payment->reference_number ?: 'Không có' }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <span class="block text-sm font-medium text-gray-500 uppercase mb-1">Trạng thái</span>
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                                        <span class="text-gray-900 font-medium">Đã hoàn thành</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        @if($payment->notes)
                        <div class="pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Ghi chú</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-700">{{ $payment->notes }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Contract Information -->
        <div>
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <i class="fas fa-file-contract mr-2"></i>Thông tin hợp đồng
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <span class="block text-xs font-semibold text-gray-400 uppercase mb-1">Số hợp đồng</span>
                            <a href="{{ route('client.contracts.show', $payment->contract) }}" 
                               class="text-blue-600 font-medium hover:underline inline-flex items-center">
                                {{ $payment->contract->contract_number }}
                                <i class="fas fa-external-link-alt ml-2 text-sm"></i>
                            </a>
                        </div>
                        
                        <div>
                            <span class="block text-xs font-semibold text-gray-400 uppercase mb-1">Tên hợp đồng</span>
                            <p class="text-gray-900">{{ $payment->contract->contract_name }}</p>
                        </div>
                        
                        <div>
                            <span class="block text-xs font-semibold text-gray-400 uppercase mb-1">Dự án</span>
                            <a href="{{ route('client.projects.show', $payment->contract->project) }}" 
                               class="text-blue-600 hover:underline inline-flex items-center">
                                {{ $payment->contract->project->project_name }}
                                <i class="fas fa-external-link-alt ml-2 text-sm"></i>
                            </a>
                        </div>
                        
                        @if(auth()->user()->user_type === 'owner')
                        <div>
                            <span class="block text-xs font-semibold text-gray-400 uppercase mb-1">Nhà thầu</span>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-hard-hat"></i>
                                </div>
                                <span class="text-gray-900">{{ $payment->contract->contractor->username ?? 'N/A' }}</span>
                            </div>
                        </div>
                        @else
                        <div>
                            <span class="block text-xs font-semibold text-gray-400 uppercase mb-1">Chủ đầu tư</span>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <span class="text-gray-900">{{ $payment->contract->owner->username ?? 'N/A' }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <i class="fas fa-history mr-2"></i>Lịch sử
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-green-600 text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Thanh toán hoàn tất</p>
                                <p class="text-xs text-gray-500">{{ $payment->pay_date ? $payment->pay_date->format('d/m/Y H:i') : 'N/A' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-plus text-blue-600 text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Tạo thanh toán</p>
                                <p class="text-xs text-gray-500">{{ $payment->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Information -->
    <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
        <div class="text-sm text-gray-500">
            Thanh toán được tạo ngày: {{ $payment->created_at->format('d/m/Y H:i') }}
            • Cập nhật lần cuối: {{ $payment->updated_at->format('d/m/Y H:i') }}
        </div>
    </div>
</div>
@endsection