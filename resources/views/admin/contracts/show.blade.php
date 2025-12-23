@extends('layouts.app')

@section('title', 'Chi tiết Hợp đồng - ' . ($contract->contract_number ?? 'N/A'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-2xl font-bold text-gray-900">Hợp đồng: {{ $contract->contract_number ?? 'Chưa có số' }}</h1>
                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium 
                    @if($contract->status == 'active') bg-green-100 text-green-800
                    @elseif($contract->status == 'completed') bg-blue-100 text-blue-800
                    @elseif($contract->status == 'draft') bg-gray-100 text-gray-800
                    @elseif($contract->status == 'pending_signature') bg-yellow-100 text-yellow-800
                    @elseif(in_array($contract->status, ['terminated', 'expired'])) bg-red-100 text-red-800
                    @else bg-orange-100 text-orange-800 @endif">
                    {{ App\Http\Controllers\Admin\ContractsController::getStatuses()[$contract->status] ?? $contract->status }}
                </span>
            </div>
            <p class="text-gray-500 text-sm">{{ $contract->contract_name }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.contracts.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2"/></svg>
                Quay lại
            </a>
            @if(in_array($contract->status, ['draft', 'pending_signature']))
            <form action="{{ route('admin.contracts_approve', $contract) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-sm transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2"/></svg>
                    Phê duyệt
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500 font-medium">Tổng giá trị</p>
            <p class="text-xl font-bold text-gray-900">{{ number_format($contract->contract_value) }} <span class="text-xs">VND</span></p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500 font-medium">Đã thanh toán (Gồm tạm ứng)</p>
            <p class="text-xl font-bold text-blue-600">{{ number_format($totalPaid) }} <span class="text-xs">VND</span></p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500 font-medium">Còn lại</p>
            <p class="text-xl font-bold text-red-600">{{ number_format($remaining) }} <span class="text-xs">VND</span></p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500 font-medium">Trạng thái tiền</p>
            <div class="mt-1">
                <span class="px-2 py-0.5 rounded text-xs font-bold uppercase
                    @if($contract->payment_status == 'fully_paid') bg-green-100 text-green-800
                    @elseif($contract->payment_status == 'unpaid') bg-red-100 text-red-800
                    @else bg-blue-100 text-blue-800 @endif">
                    {{ App\Http\Controllers\Admin\ContractsController::getPaymentStatuses()[$contract->payment_status] ?? $contract->payment_status }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Tiến độ thanh toán hợp đồng</h3>
                <div class="relative pt-1">
                    <div class="flex mb-2 items-center justify-between">
                        <div><span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-green-600 bg-green-200">Hoàn thành</span></div>
                        <div class="text-right"><span class="text-sm font-bold inline-block text-green-600">{{ round($progress, 1) }}%</span></div>
                    </div>
                    <div class="overflow-hidden h-3 mb-4 text-xs flex rounded-full bg-gray-100">
                        <div style="width:{{ min($progress, 100) }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500 transition-all duration-500"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                    <h3 class="font-bold text-gray-900">Thông tin chi tiết</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                    <div>
                        <span class="block text-xs font-semibold text-gray-400 uppercase">Dự án</span>
                        <a href="{{ route('admin.projects.show', $contract->project) }}" class="text-blue-600 font-medium hover:underline">
                            {{ $contract->project->project_name ?? 'N/A' }}
                        </a>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-gray-400 uppercase">Nhà thầu</span>
                        <span class="text-gray-900 font-medium">{{ $contract->contractor->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-gray-400 uppercase">Ngày ký</span>
                        <span class="text-gray-900">{{ $contract->signed_date ? $contract->signed_date->format('d/m/Y') : '---' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-gray-400 uppercase">Hạn hoàn thành</span>
                        <span class="text-gray-900 @if($contract->due_date && $contract->due_date->isPast() && $contract->status == 'active') text-red-600 font-bold @endif">
                            {{ $contract->due_date ? $contract->due_date->format('d/m/Y') : '---' }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-gray-400 uppercase">Tiền tạm ứng</span>
                        <span class="text-gray-900 font-medium">{{ number_format($contract->advance_payment) }} VND</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-gray-400 uppercase">Chủ đầu tư</span>
                        <span class="text-gray-900">{{ $contract->owner->name ?? 'N/A' }}</span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="block text-xs font-semibold text-gray-400 uppercase mb-1">Mô tả / Ghi chú</span>
                        <p class="text-gray-700 text-sm leading-relaxed">{{ $contract->description ?: 'Không có mô tả.' }}</p>
                    </div>
                </div>
            </div>

            @if($contract->terms)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"/></svg>
                    Điều khoản hợp đồng
                </h3>
                <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 leading-loose">
                    {!! nl2br(e($contract->terms)) !!}
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-900">Thanh toán</h3>
                    <a href="{{ route('admin.payments.create', ['contract_id' => $contract->id]) }}" class="text-xs font-bold text-blue-600 hover:underline">+ THÊM</a>
                </div>
                <div class="p-5">
                    <div class="flow-root">
                        <ul role="list" class="-my-5 divide-y divide-gray-100">
                            @forelse($contract->payments->take(5) as $payment)
                            <li class="py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate">
                                            {{ number_format($payment->amount) }} VND
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $payment->pay_date ? $payment->pay_date->format('d/m/Y') : 'N/A' }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-100 uppercase">
                                            {{ $payment->method }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                            @empty
                            <li class="py-8 text-center text-gray-400 text-sm">Chưa có giao dịch</li>
                            @endforelse
                        </ul>
                    </div>
                    @if($contract->payments->count() > 5)
                    <a href="{{ route('admin.payments.index', ['contract_id' => $contract->id]) }}" class="block text-center mt-4 text-xs font-bold text-gray-500 hover:text-blue-600 uppercase tracking-widest">Xem tất cả</a>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-5">Mốc thời gian</h3>
                <div class="relative">
                    <div class="absolute left-3 top-0 h-full w-0.5 bg-gray-100"></div>
                    <div class="space-y-6 relative">
                        <div class="flex items-center">
                            <div class="z-10 w-6 h-6 bg-blue-500 rounded-full border-4 border-white shadow-sm"></div>
                            <div class="ml-4">
                                <p class="text-xs font-bold text-gray-900">Khởi tạo</p>
                                <p class="text-[10px] text-gray-500">{{ $contract->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="z-10 w-6 h-6 @if($contract->signed_date) bg-green-500 @else bg-gray-300 @endif rounded-full border-4 border-white shadow-sm"></div>
                            <div class="ml-4">
                                <p class="text-xs font-bold text-gray-900">Ký kết</p>
                                <p class="text-[10px] text-gray-500">{{ $contract->signed_date ? $contract->signed_date->format('d/m/Y') : 'Chờ cập nhật' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <a href="{{ route('admin.contracts.edit', $contract) }}" class="w-full flex items-center justify-center px-4 py-2 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg hover:bg-blue-100 transition font-medium">
                    Sửa hợp đồng
                </a>
                <form action="{{ route('admin.contracts.destroy', $contract) }}" method="POST" onsubmit="return confirm('Xóa hợp đồng này?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 text-red-600 bg-red-50 border border-red-100 rounded-lg hover:bg-red-100 transition text-sm font-medium">
                        Xóa dữ liệu
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection