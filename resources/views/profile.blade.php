@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Profile --}}
        <div class="bg-white rounded-xl shadow-sm mb-8 overflow-hidden">
            <div class="h-48 bg-gradient-to-r from-blue-500 to-purple-600 relative rounded-t-xl">
                
            </div>
            
            <div class="relative px-6 pb-6">
                {{-- Avatar --}}
                <div class="absolute -top-16 left-1/2 transform -translate-x-1/2">
                    <div class="relative group">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                                 alt="Avatar" 
                                 class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover">
                        @else
                            <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-user text-4xl text-gray-400"></i>
                            </div>
                        @endif
                        {{-- <button class="absolute inset-0 bg-black/50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                onclick="document.getElementById('avatar-upload').click()">
                            <i class="fas fa-camera text-white text-xl"></i>
                        </button> --}}
                    </div>
                    <form id="avatarForm" enctype="multipart/form-data">
                        @csrf
                        <input type="file" id="avatar-upload" name="avatar" class="hidden" accept="image/*">
                    </form>
                </div>
                
                {{-- User Info Header --}}
                <div class="pt-20 text-center">
                    <h1 class="text-3xl font-bold text-gray-900">{{ auth()->user()->username }}</h1>
                    <p class="text-gray-600 mt-2 capitalize">{{ auth()->user()->user_type }} • {{ auth()->user()->email }}</p>
                    <div class="flex justify-center gap-2 mt-3 flex-wrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 capitalize">
                            {{ auth()->user()->status }}
                        </span>
                        @if(auth()->user()->phone)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            {{ auth()->user()->phone }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Main Content Area --}}
        <div class="bg-white rounded-xl shadow-sm mb-8">
            {{-- Tab Navigation --}}
            <div class="border-b border-gray-200">
                <nav class="flex overflow-x-auto space-x-8 px-6" aria-label="Tabs">
                    <button onclick="switchTab('overview')" id="tab-btn-overview"
                        class="tab-btn py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-200 border-blue-500 text-blue-600 outline-none focus:outline-none">
                        Tổng quan
                    </button>

                    <button onclick="switchTab('edit')" id="tab-btn-edit"
                        class="tab-btn py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-200 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 outline-none focus:outline-none">
                        Chỉnh sửa
                    </button>
                    
                    <button onclick="switchTab('security')" id="tab-btn-security"
                        class="tab-btn py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-200 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 outline-none focus:outline-none">
                        Bảo mật
                    </button>
                </nav>
            </div>

            <div class="p-6">
                {{-- TAB 1: OVERVIEW --}}
                <div id="overview-content" class="tab-content">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2 space-y-6">
                            {{-- Info Card --}}
                            <div class="bg-white rounded-lg border border-gray-200 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin cơ bản</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-user text-gray-400 w-5"></i>
                                        <div>
                                            <div class="text-sm text-gray-500">Họ và Tên</div>
                                            <div class="font-medium">{{ auth()->user()->username }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-envelope text-gray-400 w-5"></i>
                                        <div>
                                            <div class="text-sm text-gray-500">Email</div>
                                            <div class="font-medium">{{ auth()->user()->email }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-phone text-gray-400 w-5"></i>
                                        <div>
                                            <div class="text-sm text-gray-500">Số điện thoại</div>
                                            <div class="font-medium">{{ auth()->user()->phone ?? 'Chưa cập nhật' }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-user-tag text-gray-400 w-5"></i>
                                        <div>
                                            <div class="text-sm text-gray-500">Vai trò</div>
                                            <div class="font-medium capitalize">{{ auth()->user()->user_type }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Projects List --}}
                            @if(auth()->user()->engineeredProjects->count() > 0)
                            <div class="bg-white rounded-lg border border-gray-200 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Dự án gần đây</h3>
                                <div class="space-y-4">
                                    @foreach(auth()->user()->engineeredProjects->take(3) as $project)
                                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-blue-300 transition-colors duration-200">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ $project->project_name ?? 'Không có tên' }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $project->location ?? 'Chưa có địa điểm' }}</p>
                                            @if(isset($project->progress_percent))
                                            <div class="flex items-center gap-3 mt-2">
                                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $project->progress_percent }}%"></div>
                                                </div>
                                                <span class="text-sm text-gray-600 whitespace-nowrap">{{ $project->progress_percent }}%</span>
                                            </div>
                                            @endif
                                        </div>
                                        @if(isset($project->status))
                                            @php
                                                $statusColors = [
                                                    'completed' => 'bg-green-100 text-green-800',
                                                    'in_progress' => 'bg-blue-100 text-blue-800',
                                                    'planned' => 'bg-yellow-100 text-yellow-800',
                                                    'on_hold' => 'bg-orange-100 text-orange-800',
                                                    'cancelled' => 'bg-red-100 text-red-800'
                                                ];
                                                $statusColor = $statusColors[$project->status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                                {{ $project->status }}
                                            </span>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- Sidebar Stats --}}
                        <div class="space-y-6">
                            {{-- <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-6 text-white">
                                <div class="grid grid-cols-2 gap-4 text-center">
                                    <div>
                                        <div class="text-3xl font-bold">{{ auth()->user()->engineeredProjects->count() }}</div>
                                        <div class="text-blue-100 text-sm mt-1">Dự án</div>
                                    </div>
                                    <div>
                                        <div class="text-3xl font-bold">{{ auth()->user()->progressUpdates->count() }}</div>
                                        <div class="text-blue-100 text-sm mt-1">Cập nhật</div>
                                    </div>
                                </div>
                            </div> --}}

                            <div class="bg-white rounded-lg border border-gray-200 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin thành viên</h3>
                                <div class="space-y-3">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-calendar text-gray-400 w-5"></i>
                                        <div>
                                            <div class="text-sm text-gray-500">Ngày tham gia</div>
                                            <div class="font-medium">{{ auth()->user()->created_at->format('d/m/Y') }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-clock text-gray-400 w-5"></i>
                                        <div>
                                            <div class="text-sm text-gray-500">Trạng thái</div>
                                            <div class="font-medium capitalize">{{ auth()->user()->status }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Role Requests List --}}
                            @if(auth()->user()->getRoleChangeRequestsList()->count() > 0)
                                <div class="bg-white rounded-lg border border-gray-200 p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Lịch sử đổi vai trò</h3>
                                    <div class="space-y-3">
                                        @foreach(auth()->user()->getRoleChangeRequestsList() as $request)
                                        <div class="border border-gray-200 rounded-lg p-4">
                                            <div class="flex justify-between items-center mb-2">
                                                {{-- Sửa ['requested_role'] thành ->requested_role --}}
                                                <span class="font-medium capitalize">{{ $request->requested_role ?? 'N/A' }}</span>
                                                
                                                {{-- Sửa ['status'] thành ->status --}}
                                                <span class="px-2 py-1 text-xs rounded-full 
                                                    @if($request->status == 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($request->status == 'approved') bg-green-100 text-green-800
                                                    @else bg-red-100 text-red-800 @endif">
                                                    {{ ucfirst($request->status ?? 'unknown') }}
                                                </span>
                                            </div>
                                            
                                            {{-- Sửa ['reason'] thành ->reason --}}
                                            <div class="text-sm text-gray-600">{{ $request->reason ?? 'Không có lý do' }}</div>
                                            
                                            {{-- Sửa ['created_at'] thành ->created_at --}}
                                            <div class="text-xs text-gray-500 mt-2">
                                                {{ $request->created_at ? $request->created_at->format('d/m/Y H:i') : 'N/A' }}
                                            </div>

                                            {{-- Sửa ['admin_notes'] thành ->admin_notes --}}
                                            @if($request->admin_notes)
                                            <div class="mt-2 text-sm text-gray-700 bg-gray-50 p-2 rounded">
                                                <span class="font-medium">Phản hồi:</span> {{ $request->admin_notes }}
                                            </div>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                        </div>
                    </div>
                </div>

                {{-- TAB 2: EDIT --}}
                <div id="edit-content" class="tab-content hidden">
                    <div class="space-y-6">
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Thông tin cơ bản</h3>
                            <form id="basicInfoForm" method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Họ và Tên</label>
                                        <input type="text" name="username" value="{{ auth()->user()->username }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                        <input type="email" name="email" value="{{ auth()->user()->email }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
                                        <input type="tel" name="phone" value="{{ auth()->user()->phone ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Nhập số điện thoại">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Vai trò hiện tại</label>
                                        <div class="px-3 py-2 bg-gray-100 rounded-lg border border-gray-300">
                                            <span class="font-medium capitalize">{{ auth()->user()->user_type }}</span>
                                            @if(auth()->user()->hasPendingRoleRequest())
                                                <span class="ml-2 inline-flex items-center px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-clock mr-1"></i> Đang chờ duyệt
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex gap-3 pt-4">
                                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                                        <i class="fas fa-save"></i> Cập nhật thông tin
                                    </button>
                                    <button type="reset" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors duration-200 flex items-center gap-2">
                                        <i class="fas fa-undo"></i> Đặt lại
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Role Request Form --}}
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Yêu cầu đổi vai trò</h3>
                            @if(auth()->user()->hasPendingRoleRequest())
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-exclamation-circle text-yellow-500 mr-2"></i>
                                        <span class="text-yellow-700">Bạn đang có yêu cầu đổi vai trò đang chờ duyệt.</span>
                                    </div>
                                </div>
                            @endif
                            <form id="roleChangeForm" method="POST" action="{{ route('profile.request-role-change') }}">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Vai trò mong muốn</label>
                                    <select name="requested_role" class="w-full px-3 py-2 border rounded-lg" {{ auth()->user()->hasPendingRoleRequest() ? 'disabled' : '' }}>
                                        <option value="">Chọn vai trò</option>
                                        @php
                                            $availableRoles = ['owner' => 'Chủ đầu tư', 'contractor' => 'Nhà thầu', 'engineer' => 'Kỹ sư', 'admin' => 'Quản trị viên'];
                                            $currentRole = auth()->user()->user_type;
                                        @endphp
                                        @foreach($availableRoles as $value => $label)
                                            @if($value !== $currentRole)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Lý do yêu cầu</label>
                                    <textarea name="reason" rows="3" class="w-full px-3 py-2 border rounded-lg" placeholder="Vui lòng giải thích lý do..." {{ auth()->user()->hasPendingRoleRequest() ? 'disabled' : '' }}></textarea>
                                </div>
                                @if(!auth()->user()->hasPendingRoleRequest())
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Gửi yêu cầu</button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

                {{-- TAB 3: SECURITY --}}
                <div id="security-content" class="tab-content hidden">
                    <div class="space-y-6">
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Đổi mật khẩu</h3>
                            <form id="changePasswordForm" method="POST" action="{{ route('profile.change-password') }}" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu hiện tại</label>
                                    <input type="password" name="current_password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu mới</label>
                                    <input type="password" name="new_password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                    <p class="text-sm text-gray-500 mt-1">Mật khẩu phải có ít nhất 6 ký tự</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Xác nhận mật khẩu mới</label>
                                    <input type="password" name="new_password_confirmation" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                </div>
                                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                                    <i class="fas fa-key"></i> Đổi mật khẩu
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CONFIRMATION --}}
<div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-blue-100 rounded-full mb-4">
                <i class="fas fa-exclamation text-blue-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Xác nhận gửi yêu cầu</h3>
            <p class="text-gray-600 text-center mb-6">Bạn có chắc chắn muốn gửi yêu cầu đổi vai trò? Yêu cầu sẽ được gửi đến quản trị viên để xem xét.</p>
            <div class="flex gap-3">
                <button type="button" onclick="cancelRequest()" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors duration-200">Hủy</button>
                <button type="button" onclick="submitRoleChangeRequest()" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">Xác nhận gửi</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// --- PHẦN XỬ LÝ TAB ---
// Định nghĩa hàm switchTab và gán vào window để HTML có thể gọi
window.switchTab = function(tabName) {
    console.log("Switching to tab:", tabName);

    // 1. Ẩn tất cả nội dung tab (Thêm class hidden)
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.add('hidden');
    });

    // 2. Hiện nội dung tab được chọn (Xóa class hidden)
    const targetContent = document.getElementById(tabName + '-content');
    if (targetContent) {
        targetContent.classList.remove('hidden');
    }

    // 3. Cập nhật trạng thái nút bấm (Giao diện)
    // Reset style của tất cả các nút về trạng thái thường
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.className = 'tab-btn py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-200 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 outline-none focus:outline-none';
    });

    // Set style cho nút đang được chọn (active)
    const activeBtn = document.getElementById('tab-btn-' + tabName);
    if (activeBtn) {
        activeBtn.className = 'tab-btn py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-200 border-blue-500 text-blue-600 outline-none focus:outline-none';
    }
};

// --- PHẦN XỬ LÝ SỰ KIỆN SAU KHI LOAD TRANG ---
document.addEventListener('DOMContentLoaded', function() {
    
    // Xử lý Upload Avatar
    const avatarInput = document.getElementById('avatar-upload');
    if(avatarInput) {
        avatarInput.addEventListener('change', function () {
            const formData = new FormData(document.getElementById('avatarForm'));
            // Hiển thị loading hoặc feedback cho user (tùy chọn)
            
            fetch("{{ route('profile.avatar') }}", {
                method: "POST",
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showAlert('error', data.message || 'Upload thất bại');
                }
            })
            .catch(err => {
                console.error(err);
                showAlert('error', 'Lỗi kết nối server');
            });
        });
    }

    // Xử lý Form Thông tin cơ bản
    const basicInfoForm = document.getElementById('basicInfoForm');
    if (basicInfoForm) {
        handleFormSubmit(basicInfoForm, "Đang cập nhật...", function(data) {
             showAlert('success', 'Cập nhật thành công!');
             setTimeout(() => location.reload(), 1000);
        });
    }

    // Xử lý Form Đổi mật khẩu
    const changePasswordForm = document.getElementById('changePasswordForm');
    if (changePasswordForm) {
        handleFormSubmit(changePasswordForm, "Đang xử lý...", function(data) {
            showAlert('success', 'Đổi mật khẩu thành công!');
            changePasswordForm.reset();
        });
    }

    // Xử lý Form Yêu cầu đổi vai trò (Hiển thị Modal)
    const roleChangeForm = document.getElementById('roleChangeForm');
    if (roleChangeForm) {
        roleChangeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const requestedRole = this.querySelector('[name="requested_role"]').value;
            const reason = this.querySelector('[name="reason"]').value.trim();

            if (!requestedRole) return alert('Vui lòng chọn vai trò!');
            if (reason.length < 10) return alert('Lý do phải ít nhất 10 ký tự!');

            // Hiện modal
            const modal = document.getElementById('confirmationModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    }
});

// Hàm hỗ trợ gửi Form AJAX chung để code gọn hơn
function handleFormSubmit(form, loadingText, onSuccess) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${loadingText}`;
        btn.disabled = true;

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: new FormData(form)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                onSuccess(data);
            } else {
                showAlert('error', data.message || 'Có lỗi xảy ra');
            }
        })
        .catch(err => {
            console.error(err);
            showAlert('error', 'Lỗi hệ thống');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });
}

// Xử lý Modal Xác nhận
window.cancelRequest = function() {
    const modal = document.getElementById('confirmationModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

window.submitRoleChangeRequest = function() {
    const form = document.getElementById('roleChangeForm');
    const modalBtns = document.querySelectorAll('#confirmationModal button');
    modalBtns.forEach(b => b.disabled = true);

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: new FormData(form)
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            showAlert('success', 'Đã gửi yêu cầu!');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', data.message || 'Lỗi khi gửi yêu cầu');
            cancelRequest(); // Đóng modal để user sửa lại nếu cần
        }
    })
    .catch(err => {
        console.error(err);
        showAlert('error', 'Lỗi kết nối');
    })
    .finally(() => {
        modalBtns.forEach(b => b.disabled = false);
    });
}

// Hàm hiển thị thông báo góc màn hình
function showAlert(type, message) {
    // Xóa alert cũ nếu có
    document.querySelectorAll('.custom-alert').forEach(a => a.remove());

    const div = document.createElement('div');
    const bgClass = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

    div.className = `custom-alert fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg border flex items-center gap-3 transition-all duration-300 ${bgClass}`;
    div.innerHTML = `
        <i class="fas ${icon} text-xl"></i>
        <div class="font-medium">${message}</div>
        <button onclick="this.parentElement.remove()" class="ml-2 opacity-70 hover:opacity-100"><i class="fas fa-times"></i></button>
    `;

    document.body.appendChild(div);
    setTimeout(() => div.remove(), 5000);
}
</script>
@endpush