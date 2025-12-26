@extends('layouts.app')

@section('title', 'Chi tiết Hợp đồng - ' . ($contract->contract_number ?? 'N/A'))

@section('content')
{{-- Định nghĩa Map trạng thái và màu sắc ngay tại View để không phụ thuộc Controller Admin --}}
@php
    $statuses = [
        'draft' => 'Bản nháp',
        'pending_signature' => 'Chờ ký',
        'active' => 'Đang hiệu lực',
        'completed' => 'Hoàn thành',
        'terminated' => 'Đã chấm dứt',
        'expired' => 'Đã hết hạn',
    ];
    
    $paymentStatuses = [
        'unpaid' => 'Chưa thanh toán',
        'partially_paid' => 'Thanh toán một phần',
        'fully_paid' => 'Đã thanh toán đủ',
        'overdue' => 'Quá hạn',
    ];

    $isOwner = auth()->user()->user_type === 'owner';
@endphp

<div class="mb-6">
    <div class="flex justify-between items-start">
        <div>
            <nav class="mb-4">
                <a href="{{ route('client.contracts.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
            </nav>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-3xl font-bold text-gray-800">Hợp đồng: {{ $contract->contract_number ?? 'Chưa có số' }}</h1>
                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium 
                    @if($contract->status == 'active') bg-green-100 text-green-800
                    @elseif($contract->status == 'completed') bg-blue-100 text-blue-800
                    @elseif($contract->status == 'draft') bg-gray-100 text-gray-800
                    @elseif($contract->status == 'pending_signature') bg-yellow-100 text-yellow-800
                    @elseif(in_array($contract->status, ['terminated', 'expired'])) bg-red-100 text-red-800
                    @else bg-orange-100 text-orange-800 @endif">
                    {{ $statuses[$contract->status] ?? $contract->status }}
                </span>
            </div>
            <p class="text-xl text-gray-600 mt-2">{{ $contract->contract_name }}</p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            {{-- Chỉ hiện nút Phê duyệt nếu là Owner và trạng thái là Chờ ký --}}
            @if($isOwner && in_array($contract->status, ['pending_signature', 'draft']))
            <form action="{{ route('client.contracts_approve', $contract) }}" method="POST" onsubmit="return confirm('Xác nhận phê duyệt hợp đồng này?');">
                @csrf 
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                    <i class="fas fa-check mr-2"></i> Phê duyệt
                </button>
            </form>
            @endif
            
            {{-- Nút tải xuống PDF --}}
            <button class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                <i class="fas fa-download mr-2"></i> Tải PDF
            </button>
        </div>
    </div>
</div>

<!-- Thông báo -->
@include('components.alert')

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Contract Information -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-info-circle mr-2"></i>Thông tin hợp đồng
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Số hợp đồng:</span>
                    <span class="font-medium text-gray-800">{{ $contract->contract_number ?? 'Chưa có số' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tên hợp đồng:</span>
                    <span class="font-medium text-gray-800">{{ $contract->contract_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Dự án:</span>
                    <a href="{{ route('client.projects.show', $contract->project) }}" class="font-medium text-blue-600 hover:underline">
                        {{ $contract->project->project_name ?? 'N/A' }}
                    </a>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Nhà thầu:</span>
                    <span class="font-medium text-gray-800">{{ $contract->contractor->username ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Chủ đầu tư:</span>
                    <span class="font-medium text-gray-800">{{ $contract->owner->username ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngày ký:</span>
                    <span class="font-medium text-gray-800">
                        {{ $contract->signed_date ? $contract->signed_date->format('d/m/Y') : '---' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Hạn hoàn thành:</span>
                    <span class="font-medium text-gray-800 @if($contract->due_date && $contract->due_date->isPast() && $contract->status == 'active') text-red-600 @endif">
                        {{ $contract->due_date ? $contract->due_date->format('d/m/Y') : '---' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tiền tạm ứng:</span>
                    <span class="font-medium text-gray-800">{{ number_format($contract->advance_payment) }} VND</span>
                </div>
                
                <!-- Statistics Box -->
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div class="p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>
                            <span class="text-sm font-medium text-gray-700">Đã thanh toán</span>
                        </div>
                        <div class="mt-2">
                            <span class="text-lg font-bold text-green-600">{{ number_format($totalPaid) }} VND</span>
                            <p class="text-xs text-gray-500">{{ round($progress, 1) }}% tổng giá trị</p>
                        </div>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-blue-500 mr-2"></i>
                            <span class="text-sm font-medium text-gray-700">Còn lại</span>
                        </div>
                        <div class="mt-2">
                            <span class="text-lg font-bold text-blue-600">{{ number_format($remaining) }} VND</span>
                            <p class="text-xs text-gray-500">Chưa thanh toán</p>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($contract->description)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-2">Mô tả:</h4>
                <p class="text-gray-600">{{ $contract->description }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Contract Financial & Status -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-chart-line mr-2"></i>Tài chính & Trạng thái
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <!-- Tổng giá trị hợp đồng -->
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600">Tổng giá trị hợp đồng</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($contract->contract_value) }} VND</p>
                </div>

                <!-- Tiến độ thanh toán -->
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Tiến độ thanh toán</span>
                        <span class="text-sm font-bold text-green-600">{{ round($progress, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="bg-green-500 h-4 rounded-full transition-all duration-500" 
                             style="width: {{ min($progress, 100) }}%"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-xs text-gray-500">
                        <span>0 VND</span>
                        <span>{{ number_format($contract->contract_value) }} VND</span>
                    </div>
                </div>

                <!-- Trạng thái thanh toán -->
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-credit-card text-blue-500 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Trạng thái tiền</span>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase
                            @if($contract->payment_status == 'fully_paid') bg-green-100 text-green-800
                            @elseif($contract->payment_status == 'unpaid') bg-red-100 text-red-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ $paymentStatuses[$contract->payment_status] ?? $contract->payment_status }}
                        </span>
                    </div>
                </div>

                <!-- Mốc thời gian -->
                <div class="pt-4 border-t border-gray-200">
                    <h4 class="font-semibold text-gray-700 mb-4">Mốc thời gian</h4>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar-plus text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <div class="font-medium text-gray-800">Khởi tạo</div>
                                <div class="text-sm text-gray-500">{{ $contract->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 @if($contract->signed_date) bg-green-100 text-green-600 @else bg-gray-100 text-gray-400 @endif rounded-full flex items-center justify-center">
                                <i class="fas fa-signature text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <div class="font-medium text-gray-800">Ký kết</div>
                                <div class="text-sm text-gray-500">{{ $contract->signed_date ? $contract->signed_date->format('d/m/Y') : 'Chờ cập nhật' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>      
</div>

<!-- Danh sách thanh toán -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-history mr-2"></i>Lịch sử thanh toán
            <span class="text-sm font-normal text-gray-500 ml-2">({{ $contract->payments->count() }} giao dịch)</span>
        </h2>
    </div>
    
    <div class="p-6">
        @if($contract->payments && $contract->payments->count() > 0)
            <!-- Payments Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tiền</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày thanh toán</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phương thức</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ghi chú</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($contract->payments as $index => $payment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">
                                    {{ number_format($payment->amount) }} VND
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->pay_date ? $payment->pay_date->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $payment->method }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 max-w-xs truncate">
                                    {{ $payment->notes ?: '---' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $paymentStatusColors = [
                                        'completed' => 'bg-green-100 text-green-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'failed' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $paymentStatusColors[$payment->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $payment->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Tổng kết thanh toán -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Tổng đã thanh toán</p>
                        <p class="text-lg font-bold text-green-600">{{ number_format($totalPaid) }} VND</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Còn lại</p>
                        <p class="text-lg font-bold text-red-600">{{ number_format($remaining) }} VND</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Số giao dịch</p>
                        <p class="text-lg font-bold text-gray-900">{{ $contract->payments->count() }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Tiến độ</p>
                        <p class="text-lg font-bold text-blue-600">{{ round($progress, 1) }}%</p>
                    </div>
                </div>
            </div>
            
        @else
            <!-- Empty state -->
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-money-bill-wave text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Chưa có thanh toán nào</h3>
                <p class="text-gray-500 mb-6">Hợp đồng chưa có giao dịch thanh toán</p>
            </div>
        @endif
    </div>
</div>

<!-- Tabs Section cho Điều khoản và Files đính kèm -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="border-b border-gray-200">
        <nav class="flex -mb-px">
            <button class="tab-button active py-4 px-6 text-sm font-medium border-b-2 border-blue-500 text-blue-600" data-tab="terms">
                <i class="fas fa-file-signature mr-2"></i>Điều khoản hợp đồng
            </button>
            <button class="tab-button py-4 px-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="attachments">
                <i class="fas fa-paperclip mr-2"></i>File đính kèm ({{ optional($contract->documents)->count() ?? 0 }})
            </button>
        </nav>
    </div>

    <div class="p-6">
        <!-- Terms Tab -->
        <div id="tab-terms" class="tab-content active">
            @if($contract->terms)
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="prose max-w-none">
                        {!! nl2br(e($contract->terms)) !!}
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-file-signature text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Chưa có điều khoản</h3>
                    <p class="text-gray-500 mb-6">Hợp đồng chưa được thiết lập điều khoản</p>
                </div>
            @endif
        </div>

        <!-- Attachments Tab -->
        <div id="tab-attachments" class="tab-content hidden">
            @if($contract->documents && $contract->documents->count() > 0)
                <div class="space-y-4">
                    @foreach($contract->documents as $document)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-alt text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-semibold text-gray-800">{{ $document->document_name }}</h4>
                                    @if($document->description)
                                    <p class="text-gray-600 text-sm mt-1">{{ $document->description }}</p>
                                    @endif
                                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                        <span>Loại: {{ $document->document_type }}</span>
                                        <span>• Kích thước: {{ round($document->file_size / 1024, 2) }} KB</span>
                                        <span>• Ngày tải: {{ $document->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($document->file_path)
                                <a href="#" 
                                   class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-download mr-1"></i>Tải xuống
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-paperclip text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Chưa có file đính kèm</h3>
                    <p class="text-gray-500 mb-6">Hợp đồng chưa được tải lên file đính kèm</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
    <div class="text-sm text-gray-500">
        Tạo ngày: {{ $contract->created_at->format('d/m/Y H:i') }}
        • Cập nhật: {{ $contract->updated_at->format('d/m/Y H:i') }}
        • Tổng thanh toán: {{ $contract->payments->count() }} giao dịch
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

.prose {
    color: #374151;
    line-height: 1.75;
}

.prose p {
    margin-bottom: 1rem;
}
</style>
@endpush