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
            <a href="{{ route('admin.contracts.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition shadow-sm font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2"/></svg>
                Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="max-w-6xl mx-auto">
        <form action="{{ route('admin.contracts.store') }}" method="POST" class="space-y-8">
            @csrf

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-4 bg-gray-50/50 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-info-circle mr-3 text-blue-600"></i>Thông tin cơ bản & Đối tác
                    </h2>
                </div>
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Dự án <span class="text-red-500">*</span></label>
                        <select name="project_id" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all @error('project_id') border-red-300 @enderror" required>
                            <option value="">-- Chọn dự án --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->project_name }} ({{ $project->project_code ?? '#' . $project->id }})
                                </option>
                            @endforeach
                        </select>
                        @error('project_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Số hợp đồng</label>
                        <input type="text" name="contract_number" value="{{ old('contract_number') }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all" placeholder="VD: HD/2024/001">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tên hợp đồng</label>
                        <input type="text" name="contract_name" value="{{ old('contract_name') }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all" placeholder="Nhập tên hợp đồng...">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nhà thầu <span class="text-red-500">*</span></label>
                        <select name="contractor_id" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all" required>
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
                        <select name="owner_id" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all" required>
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
                            <input type="number" name="contract_value" value="{{ old('contract_value') }}" class="w-full px-4 py-3 pl-12 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-green-100 focus:border-green-500 transition-all font-bold text-green-700" required min="0">
                            <span class="absolute left-4 top-3.5 text-gray-400 font-bold">đ</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tạm ứng ban đầu (VNĐ)</label>
                        <div class="relative">
                            <input type="number" name="advance_payment" value="{{ old('advance_payment', 0) }}" class="w-full px-4 py-3 pl-12 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all text-blue-700 font-bold" min="0">
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
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Mô tả ngắn</label>
                        <textarea name="description" rows="2" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-100 focus:border-purple-500 transition-all" placeholder="Tóm tắt nội dung hợp đồng...">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Điều khoản chi tiết</label>
                        <textarea name="terms" rows="6" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-100 focus:border-purple-500 transition-all" placeholder="Các điều khoản ràng buộc...">{{ old('terms') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between bg-gray-50 p-6 rounded-2xl border-2 border-dashed border-gray-200">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i> Kiểm tra kỹ thông tin trước khi lưu.
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('admin.contracts.index') }}" class="px-6 py-3 text-gray-600 font-bold hover:text-gray-800 transition">Hủy bỏ</a>
                    <button type="submit" class="px-10 py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg hover:bg-blue-700 hover:-translate-y-1 transition-all active:translate-y-0">
                        <i class="fas fa-save mr-2"></i> Lưu hợp đồng
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Giữ nguyên phần script của bạn, nó đã hoạt động rất tốt.
</script>
@endsection