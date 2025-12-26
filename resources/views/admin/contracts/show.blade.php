@extends('layouts.app')

@section('title', $contract->contract_number . ' - Chi tiết Hợp đồng')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-start">
        <div>
            <nav class="mb-4">
                <a href="{{ route('admin.contracts.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800">Hợp đồng: {{ $contract->contract_number ?? 'Chưa có số' }}</h1>
            @if($contract->contract_name)
                <p class="text-xl text-gray-600 mt-2">{{ $contract->contract_name }}</p>
            @endif
        </div>
        @if($contract->status !== 'cancelled' && $contract->status !== 'terminated')
            <div class="flex gap-2">
                <a href="{{ route('admin.contracts.edit', $contract) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Chỉnh sửa
                </a>
                @if(in_array($contract->status, ['draft', 'pending_signature']))
                <form action="{{ route('admin.contracts_approve', $contract) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 transition-colors">
                        <i class="fas fa-check mr-2"></i>Phê duyệt
                    </button>
                </form>
                @endif
            </div>
        @endif
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
                    <span class="font-medium text-gray-800">
                        <a href="{{ route('admin.projects.show', $contract->project) }}" class="text-blue-600 hover:underline">
                            {{ $contract->project->project_name ?? 'N/A' }}
                        </a>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngày ký:</span>
                    <span class="font-medium text-gray-800">
                        {{ $contract->signed_date ? \Carbon\Carbon::parse($contract->signed_date)->format('d/m/Y') : 'Chưa ký' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Hạn hoàn thành:</span>
                    <span class="font-medium text-gray-800">
                        {{ $contract->due_date ? \Carbon\Carbon::parse($contract->due_date)->format('d/m/Y') : 'Chưa xác định' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Giá trị hợp đồng:</span>
                    <span class="font-medium text-green-600">
                        {{ number_format($contract->contract_value) }} VNĐ
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tiền tạm ứng:</span>
                    <span class="font-medium text-blue-600">
                        {{ number_format($contract->advance_payment) }} VNĐ
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Trạng thái:</span>
                    <span class="font-medium text-gray-800">
                        @if($contract->status == 'draft') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800">
                                Bản nháp
                            </span>
                        @elseif($contract->status == 'pending_signature') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                Chờ ký
                            </span>
                        @elseif($contract->status == 'active') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Đang thực hiện
                            </span>
                        @elseif($contract->status == 'completed') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Hoàn thành
                            </span>
                        @elseif($contract->status == 'on_hold') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Tạm dừng
                            </span>
                        @elseif(in_array($contract->status, ['terminated', 'cancelled', 'expired'])) 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ App\Http\Controllers\Admin\ContractsController::getStatuses()[$contract->status] ?? $contract->status }}
                            </span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Trạng thái thanh toán:</span>
                    <span class="font-medium text-gray-800">
                        @if($contract->payment_status == 'fully_paid') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Đã thanh toán
                            </span>
                        @elseif($contract->payment_status == 'partially_paid') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Thanh toán một phần
                            </span>
                        @elseif($contract->payment_status == 'unpaid') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Chưa thanh toán
                            </span>
                        @elseif($contract->payment_status == 'overdue') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                Quá hạn
                            </span>
                        @endif
                    </span>
                </div>
                
                <!-- Statistics Box -->
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-money-bill-wave text-blue-500 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Thanh toán</span>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-bold text-blue-600">{{ number_format($totalPaid) }} VNĐ</span>
                        <p class="text-xs text-gray-500">Đã thanh toán / {{ number_format($contract->contract_value) }} VNĐ</p>
                    </div>
                </div>
            </div>
            
            @if($contract->description)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-2">Mô tả:</h4>
                <p class="text-gray-600">{{ $contract->description }}</p>
            </div>
            @endif
            
            @if($contract->terms)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-2">Điều khoản:</h4>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-600 text-sm leading-relaxed">{!! nl2br(e($contract->terms)) !!}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Contract Team & Payments -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-users mr-2"></i>Đối tác & Thanh toán
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <!-- Owner -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">{{ $contract->owner->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">Chủ đầu tư</div>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ $contract->owner->email ?? '' }}</span>
                </div>

                <!-- Contractor -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-hard-hat"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">{{ $contract->contractor->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">Nhà thầu</div>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ $contract->contractor->email ?? '' }}</span>
                </div>
            </div>
            
            <!-- Payment Progress -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-3">Tiến độ thanh toán</h4>
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm font-medium text-gray-700">
                        {{ round($progress, 1) }}% hoàn thành
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ number_format($totalPaid) }} / {{ number_format($contract->contract_value) }} VNĐ
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full transition-all duration-300" 
                         style="width: {{ min($progress, 100) }}%"></div>
                </div>
            </div>
            
            <!-- Statistics Grid -->
            <div class="mt-6 grid grid-cols-2 gap-4">
                <div class="text-center p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-600">Đã thanh toán</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalPaid) }}</p>
                    <p class="text-xs text-gray-500">VNĐ</p>
                </div>
                <div class="text-center p-3 bg-green-50 rounded-lg">
                    <p class="text-sm text-gray-600">Còn lại</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($remaining) }}</p>
                    <p class="text-xs text-gray-500">VNĐ</p>
                </div>
                <div class="text-center p-3 bg-yellow-50 rounded-lg">
                    <p class="text-sm text-gray-600">Giao dịch</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $contract->payments->count() }}
                    </p>
                </div>
                <div class="text-center p-3 bg-red-50 rounded-lg">
                    <p class="text-sm text-gray-600">Tạm ứng</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($contract->advance_payment) }}
                    </p>
                    <p class="text-xs text-gray-500">VNĐ</p>
                </div>
            </div>
            
            <!-- Timeline -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-3">Lịch sử hợp đồng</h4>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-plus text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-800 text-sm">Khởi tạo</div>
                            <div class="text-xs text-gray-500">{{ $contract->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    @if($contract->signed_date)
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-signature text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-800 text-sm">Ký kết</div>
                            <div class="text-xs text-gray-500">{{ $contract->signed_date->format('d/m/Y') }}</div>
                        </div>
                    </div>
                    @endif
                    @if($contract->due_date)
                    <div class="flex items-center">
                        <div class="w-6 h-6 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-flag text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-800 text-sm">Hạn hoàn thành</div>
                            <div class="text-xs text-gray-500">{{ $contract->due_date->format('d/m/Y') }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách thanh toán -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-money-bill-wave mr-2"></i>Lịch sử thanh toán
            <span class="text-sm font-normal text-gray-500 ml-2">({{ $contract->payments->count() }} giao dịch)</span>
        </h2>
        @if($contract->status !== 'cancelled' && $contract->status !== 'terminated')
            <a href="{{ route('admin.payments.create', ['contract_id' => $contract->id]) }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i>Thêm thanh toán
            </a>
        @endif
    </div>
    
    <div class="p-6">
        @if($contract->payments && $contract->payments->count() > 0)
            <!-- Payments Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày thanh toán</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tiền</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phương thức</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mô tả</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($contract->payments as $index => $payment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->pay_date ? $payment->pay_date->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                                {{ number_format($payment->amount) }} VNĐ
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $methodColors = [
                                        'cash' => 'bg-green-100 text-green-800',
                                        'bank_transfer' => 'bg-blue-100 text-blue-800',
                                        'credit_card' => 'bg-purple-100 text-purple-800',
                                        'other' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $methodIcons = [
                                        'cash' => 'fa-money-bill-wave',
                                        'bank_transfer' => 'fa-university',
                                        'credit_card' => 'fa-credit-card',
                                        'other' => 'fa-wallet',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $methodColors[$payment->method] ?? 'bg-gray-100 text-gray-800' }}">
                                    <i class="fas {{ $methodIcons[$payment->method] ?? 'fa-wallet' }} mr-1"></i>
                                    {{ $payment->method }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 max-w-xs truncate">
                                {{ $payment->description ?? 'Không có mô tả' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.payments.edit', $payment) }}" 
                                       class="text-yellow-600 hover:text-yellow-900" 
                                       title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.payments.destroy', $payment) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Bạn có chắc muốn xóa thanh toán này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900" 
                                                title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
        @else
            <!-- Empty state -->
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-money-bill-wave text-3xl text-gray-400"></i>
                </div>
                @if($contract->status !== 'cancelled' && $contract->status !== 'terminated')
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Chưa có thanh toán nào</h3>
                    <p class="text-gray-500 mb-6">Hợp đồng chưa được thanh toán</p>
                    <a href="{{ route('admin.payments.create', ['contract_id' => $contract->id]) }}" 
                       class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-white hover:bg-green-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Thêm thanh toán đầu tiên
                    </a>
                @else
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Hợp đồng đã kết thúc</h3>
                    <p class="text-gray-500 mb-6">Không thể thêm thanh toán vào hợp đồng đã kết thúc</p>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Action Buttons -->
<div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
    <div class="text-sm text-gray-500">
        Tạo ngày: {{ $contract->created_at->format('d/m/Y H:i') }}
        • Cập nhật: {{ $contract->updated_at->format('d/m/Y H:i') }}
        • Tổng thanh toán: {{ $contract->payments->count() }}
    </div>
    @if($contract->status !== 'cancelled' && $contract->status !== 'terminated')
    <div class="flex gap-2">
        <form action="{{ route('admin.contracts.destroy', $contract) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 transition-colors"
                    onclick="return confirm('Bạn có chắc chắn muốn xóa hợp đồng này?')">
                <i class="fas fa-trash mr-2"></i>Xóa hợp đồng
            </button>
        </form>
        <a href="{{ route('admin.contracts.edit', $contract) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
            <i class="fas fa-edit mr-2"></i>Chỉnh sửa
        </a>
        <a href="#" 
        class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-white hover:bg-purple-700 transition-colors">
            <i class="fas fa-file-pdf mr-2"></i>Xuất hợp đồng
        </a>
    </div>
    @endif
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