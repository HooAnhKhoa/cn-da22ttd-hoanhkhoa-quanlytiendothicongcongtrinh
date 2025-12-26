@extends('layouts.app')

@section('title', 'Tạo Hợp đồng Mới')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tạo hợp đồng mới</h1>
                <p class="text-gray-500 mt-1">Thiết lập thông tin hợp đồng xây dựng cho dự án.</p>
            </div>
            @if(isset($selectedProjectId))
                <a href="{{ route('client.projects.show', $selectedProjectId) }}" 
                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-medium">
                    <i class="fas fa-arrow-left mr-2"></i> Quay lại dự án
                </a>
            @else
                <a href="{{ route('client.contracts.index') }}" 
                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-medium">
                    <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
                </a>
            @endif
        </div>
    </div>

    <div class="max-w-6xl mx-auto">
        <form action="{{ route('client.contracts.store') }}" method="POST" class="space-y-8">
            @csrf

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Vui lòng kiểm tra các lỗi sau:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc pl-5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-4 bg-gray-50/50 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-info-circle mr-3 text-blue-600"></i>Thông tin cơ bản & Đối tác
                    </h2>
                </div>
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Dự án <span class="text-red-500">*</span></label>
                        <select name="project_id" id="project_select" 
                                class="w-full @error('project_id') border-red-300 @enderror" required>
                            <option value="">-- Tìm kiếm hoặc chọn dự án --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}"
                                    data-contractor="{{ $project->contractor_id ?? '' }}" 
                                    data-owner="{{ $project->owner_id ?? '' }}"
                                    {{ (old('project_id', $selectedProjectId ?? '') == $project->id) ? 'selected' : '' }}>
                                    {{ $project->project_name }} ({{ $project->project_code ?? '#' . $project->id }}) - {{ $project->location ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Số hợp đồng</label>
                        <input type="text" name="contract_number" id="contract_number" value="{{ old('contract_number', $autoContractNumber ?? '') }}" 
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all" 
                               placeholder="Hệ thống tự động tạo nếu để trống">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tên hợp đồng</label>
                        <input type="text" name="contract_name" value="{{ old('contract_name') }}" 
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all" 
                               placeholder="Nhập tên hợp đồng...">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nhà thầu <span class="text-red-500">*</span></label>
                        <select name="contractor_id" id="contractor_id" 
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all" 
                                required>
                            <option value="">-- Chọn nhà thầu --</option>
                            @foreach($contractors as $contractor)
                                <option value="{{ $contractor->id }}" {{ old('contractor_id') == $contractor->id ? 'selected' : '' }}>
                                    {{ $contractor->name }} ({{ $contractor->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Chủ đầu tư <span class="text-red-500">*</span></label>
                        <select name="owner_id" id="owner_id" 
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all" 
                                required>
                            <option value="">-- Chọn chủ đầu tư --</option>
                            @foreach($owners as $owner)
                                <option value="{{ $owner->id }}" {{ old('owner_id') == $owner->id ? 'selected' : '' }}>
                                    {{ $owner->name }} ({{ $owner->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-2 flex items-center">
                        <i class="fas fa-money-bill-wave mr-3 text-green-600"></i>Thông tin tài chính
                    </h2>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Giá trị hợp đồng (VNĐ) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="contract_value" value="{{ old('contract_value') }}" 
                                   class="w-full px-4 py-3 pl-12 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-green-100 focus:border-green-500 transition-all font-bold text-green-700" required min="0">
                            <span class="absolute left-4 top-3.5 text-gray-400 font-bold">đ</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tạm ứng ban đầu (VNĐ)</label>
                        <div class="relative">
                            <input type="number" name="advance_payment" value="{{ old('advance_payment', 0) }}" 
                                   class="w-full px-4 py-3 pl-12 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all text-blue-700 font-bold" min="0">
                            <span class="absolute left-4 top-3.5 text-gray-400 font-bold">đ</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Trạng thái thanh toán <span class="text-red-500">*</span></label>
                        <select name="payment_status" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all" required>
                            @foreach($paymentStatuses as $key => $label)
                                <option value="{{ $key }}" {{ old('payment_status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-2 flex items-center">
                        <i class="fas fa-calendar-alt mr-3 text-orange-600"></i>Thời hạn & Trạng thái
                    </h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Ngày ký <span class="text-red-500">*</span></label>
                            <input type="date" name="signed_date" id="signed_date" value="{{ old('signed_date') }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-orange-100 focus:border-orange-500 transition-all" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Ngày hết hạn <span class="text-red-500">*</span></label>
                            <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-orange-100 focus:border-orange-500 transition-all" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Trạng thái hợp đồng <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-orange-100 focus:border-orange-500 transition-all font-medium" required>
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}" {{ old('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-file-alt mr-3 text-purple-600"></i>Mô tả & Điều khoản
                </h2>
                <div class="space-y-6">
                    <textarea name="description" rows="2" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-100" placeholder="Tóm tắt nội dung...">{{ old('description') }}</textarea>
                    <textarea name="terms" rows="4" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-100" placeholder="Điều khoản chi tiết...">{{ old('terms') }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 bg-gray-50 p-6 rounded-2xl border-2 border-dashed border-gray-200">
                <a href="{{ route('client.contracts.index') }}" class="px-6 py-3 text-gray-600 font-bold hover:text-gray-800 transition">Hủy bỏ</a>
                <button type="submit" class="px-10 py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg hover:bg-blue-700 hover:-translate-y-1 transition-all active:translate-y-0">
                    <i class="fas fa-save mr-2"></i> Lưu hợp đồng
                </button>
            </div>
        </form>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Custom Select2 để khớp với UI Tailwind - ĐÃ SỬA */
    .select2-container {
        width: 100% !important;
    }
    
    .select2-container .select2-selection--single {
        height: 52px !important;
        border: 2px solid #e5e7eb !important;
        border-radius: 12px !important;
        background-color: white;
        transition: all 0.3s ease;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #374151 !important;
        line-height: 48px !important;
        padding-left: 16px !important;
        font-size: 16px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #9ca3af !important;
    }
    
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 50px !important;
        width: 40px !important;
        right: 8px !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #6b7280 transparent transparent transparent !important;
        border-width: 6px 6px 0 6px !important;
        margin-top: -3px !important;
    }
    
    /* Dropdown styling */
    .select2-dropdown {
        border: 2px solid #e5e7eb !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        margin-top: 4px !important;
        overflow: hidden;
    }
    
    .select2-container--default .select2-results > .select2-results__options {
        max-height: 300px;
    }
    
    .select2-container--default .select2-results__option {
        padding: 12px 16px !important;
        font-size: 15px;
        color: #374151;
        border-bottom: 1px solid #f3f4f6;
        transition: background-color 0.2s;
    }
    
    .select2-container--default .select2-results__option:last-child {
        border-bottom: none;
    }
    
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6 !important;
        color: white !important;
    }
    
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #dbeafe !important;
        color: #1e40af !important;
        font-weight: 600;
    }
    
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 2px solid #e5e7eb !important;
        border-radius: 8px !important;
        padding: 10px 12px !important;
        margin: 8px !important;
        width: calc(100% - 16px) !important;
        font-size: 15px;
    }
    
    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: #3b82f6 !important;
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    /* Error state */
    @error('project_id')
        .select2-container--default .select2-selection--single {
            border-color: #fca5a5 !important;
        }
        
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1) !important;
        }
    @enderror
    
    /* Clear button */
    .select2-container--default .select2-selection--single .select2-selection__clear {
        color: #9ca3af !important;
        font-size: 18px !important;
        margin-right: 30px !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__clear:hover {
        color: #ef4444 !important;
    }
    
    /* Scrollbar styling */
    .select2-results__options::-webkit-scrollbar {
        width: 8px;
    }
    
    .select2-results__options::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    
    .select2-results__options::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    
    .select2-results__options::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const $projectSelect = $('#project_select');
    const contractorSelect = document.getElementById('contractor_id');
    const ownerSelect = document.getElementById('owner_id');

    // 1. Khởi tạo Select2 cho dropdown dự án
    $projectSelect.select2({
        placeholder: "Tìm kiếm hoặc chọn dự án",
        allowClear: true,
        width: '100%'
    });

    // 2. Hàm tự động điền Nhà thầu & Chủ đầu tư khi đổi Dự án
    function updateProjectDetails() {
        const selectedData = $projectSelect.find(':selected');
        const contractorId = selectedData.data('contractor');
        const ownerId = selectedData.data('owner');

        if (contractorId) contractorSelect.value = contractorId;
        if (ownerId) ownerSelect.value = ownerId;
    }

    // Lắng nghe sự kiện change từ Select2
    $projectSelect.on('change', updateProjectDetails);

    // Chạy lần đầu nếu đã có sẵn dự án được chọn
    if ($projectSelect.val()) {
        updateProjectDetails();
    }

    // 3. Logic validation ngày tháng (giữ nguyên của bạn)
    const signedDateInput = document.getElementById('signed_date');
    const dueDateInput = document.getElementById('due_date');

    function validateDates() {
        if (signedDateInput.value && dueDateInput.value) {
            if (new Date(signedDateInput.value) > new Date(dueDateInput.value)) {
                alert('Ngày hết hạn phải sau ngày ký!');
                dueDateInput.value = '';
            }
        }
    }
    signedDateInput.addEventListener('change', validateDates);
    dueDateInput.addEventListener('change', validateDates);
});
</script>
@endsection