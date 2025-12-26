@extends('layouts.app')

@section('title', 'Tạo Thanh toán Mới')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <nav class="mb-4">
                <a href="{{ route('client.payments.index') }}" 
                class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách
                </a>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800">Tạo Thanh toán Mới</h1>
            <p class="text-gray-600 mt-2">Điền thông tin giao dịch để ghi nhận thanh toán vào hệ thống.</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-8">
                <form action="{{ route('client.payments.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hợp đồng <span class="text-red-500">*</span></label>
                        <select name="contract_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('contract_id') border-red-500 @enderror">
                            <option value="">-- Chọn hợp đồng --</option>
                            @foreach($contracts as $contract)
                                <option value="{{ $contract->id }}" 
                                    {{ (old('contract_id', $selected_contract_id) == $contract->id) ? 'selected' : '' }}>
                                    {{ $contract->contract_number }} - {{ $contract->project->name ?? 'Dự án không tên' }}
                                </option>
                            @endforeach
                        </select>
                        @error('contract_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Số tiền (VND) <span class="text-red-500">*</span></label>
                            <input type="number" name="amount" required min="0" step="1000"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Ví dụ: 50000000">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ngày thanh toán <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="pay_date" required
                                   value="{{ now()->format('Y-m-d\TH:i') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phương thức <span class="text-red-500">*</span></label>
                            <select name="method" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="bank_transfer">Chuyển khoản Ngân hàng</option>
                                <option value="cash">Tiền mặt</option>
                                <option value="credit_card">Thẻ tín dụng</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mã giao dịch / Ref No.</label>
                            <input type="text" name="transaction_code"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="VD: FT2319...">
                            <p class="text-xs text-gray-500 mt-1">Mã xác thực chuyển khoản (nếu có)</p>
                        </div>
                    </div>

                    {{-- <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh biên lai / UNC</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-3"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                        <span>Tải file lên</span>
                                        <input id="file-upload" name="receipt_image" type="file" class="sr-only" accept=".jpg,.png,.pdf,.jpeg">
                                        @error('receipt_image')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </label>
                                    <p class="pl-1">hoặc kéo thả vào đây</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, PDF tối đa 2MB</p>
                            </div>
                        </div>
                    </div> --}}

                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú thêm</label>
                        <textarea name="note" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Nội dung chi tiết..."></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('client.payments.index') }}" 
                        class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                            <i class="fas fa-times mr-2"></i>Hủy bỏ
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-check-circle mr-2"></i> Xác nhận Thanh toán
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file-upload');
    const fileUploadArea = document.querySelector('.border-dashed');
    
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Kiểm tra loại file
            const fileType = file.type;
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
            
            if (!validTypes.includes(fileType)) {
                alert('Chỉ chấp nhận file ảnh JPG, PNG hoặc PDF!');
                fileInput.value = '';
                return;
            }
            
            // Kiểm tra kích thước (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('File không được vượt quá 5MB!');
                fileInput.value = '';
                return;
            }
            
            // Hiển thị preview nếu là ảnh
            if (fileType.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    fileUploadArea.innerHTML = `
                        <div class="text-center">
                            <img src="${e.target.result}" class="max-h-48 mx-auto mb-3 rounded-lg border" alt="Preview">
                            <p class="text-sm text-gray-600">${file.name}</p>
                            <button type="button" onclick="removePreview()" class="mt-2 text-red-600 hover:text-red-800 text-sm">
                                <i class="fas fa-times mr-1"></i>Chọn file khác
                            </button>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            } else if (fileType === 'application/pdf') {
                // Hiển thị icon PDF
                fileUploadArea.innerHTML = `
                    <div class="text-center">
                        <i class="fas fa-file-pdf text-red-600 text-4xl mb-3"></i>
                        <p class="text-sm text-gray-600">${file.name}</p>
                        <button type="button" onclick="removePreview()" class="mt-2 text-red-600 hover:text-red-800 text-sm">
                            <i class="fas fa-times mr-1"></i>Chọn file khác
                        </button>
                    </div>
                `;
            }
        }
    });
});

function removePreview() {
    const fileUploadArea = document.querySelector('.border-dashed');
    fileUploadArea.innerHTML = `
        <div class="space-y-1 text-center">
            <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-3"></i>
            <div class="flex text-sm text-gray-600">
                <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                    <span>Tải file lên</span>
                    <input id="file-upload" name="receipt_image" type="file" class="sr-only" accept=".jpg,.png,.pdf,.jpeg">
                </label>
                <p class="pl-1">hoặc kéo thả vào đây</p>
            </div>
            <p class="text-xs text-gray-500">PNG, JPG, PDF tối đa 5MB</p>
        </div>
    `;
    document.getElementById('file-upload').value = '';
}
</script>
@endpush