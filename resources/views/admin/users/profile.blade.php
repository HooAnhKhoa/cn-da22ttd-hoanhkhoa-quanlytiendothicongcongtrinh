@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Profile Header -->
        <div class="bg-white rounded-xl shadow-sm mb-8 overflow-hidden">
            <!-- Cover Photo -->
            <div class="h-48 bg-gradient-to-r from-blue-500 to-purple-600 relative rounded-t-xl">
                <button class="absolute top-4 right-4 bg-white/90 hover:bg-white text-gray-700 px-4 py-2 rounded-lg flex items-center gap-2 transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                    <i class="fas fa-camera"></i>
                    <span class="text-sm font-medium">Thay ảnh bìa</span>
                </button>
            </div>
            
            <!-- Avatar and Basic Info -->
            <div class="relative px-6 pb-6">
                <!-- Avatar -->
                <div class="absolute -top-16 left-1/2 transform -translate-x-1/2">
                    <div class="relative group">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                                 alt="Avatar" 
                                 class="w-32 h-32 rounded-full border-4 border-white shadow-lg">
                        @else
                            <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-user text-4xl text-gray-400"></i>
                            </div>
                        @endif
                        <button class="absolute inset-0 bg-black/50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                onclick="document.getElementById('avatar-upload').click()">
                            <i class="fas fa-camera text-white text-xl"></i>
                        </button>
                    </div>
                    <input type="file" id="avatar-upload" class="hidden" accept="image/*">
                </div>
                
                <!-- User Info -->
                <div class="pt-20 text-center">
                    <h1 class="text-3xl font-bold text-gray-900">{{ auth()->user()->username }}</h1>
                    <p class="text-gray-600 mt-2 capitalize">{{ auth()->user()->user_type }} • {{ auth()->user()->email }}</p>
                    <div class="flex justify-center gap-2 mt-3 flex-wrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
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

        <!-- Profile Tabs -->
        <div class="bg-white rounded-xl shadow-sm mb-8">
            <!-- Tab Headers -->
            <div class="border-b border-gray-200">
                <nav class="flex overflow-x-auto" id="profileTabs">
                    <button type="button" 
                            class="profile-tab active" 
                            data-tab="overview">
                        Tổng quan
                    </button>
                    <button type="button" 
                            class="profile-tab" 
                            data-tab="edit">
                        Chỉnh sửa hồ sơ
                    </button>
                    <button type="button" 
                            class="profile-tab" 
                            data-tab="security">
                        Bảo mật
                    </button>
                    <button type="button" 
                            class="profile-tab" 
                            data-tab="activity">
                        Hoạt động
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- Overview Tab -->
                <div id="tab-overview" class="tab-content">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Left Column -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- About Section -->
                            <div class="bg-white rounded-lg border border-gray-200 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Giới thiệu</h3>
                                <p class="text-gray-700">Chưa có thông tin giới thiệu. 
                                    <a href="#" class="text-blue-600 hover:text-blue-800 hover:underline">Thêm giới thiệu</a>
                                </p>
                            </div>

                            <!-- Projects Section -->
                            <div class="bg-white rounded-lg border border-gray-200 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Dự án gần đây</h3>
                                <div class="space-y-4">
                                    @foreach(auth()->user()->engineeredProjects->take(3) as $project)
                                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-blue-300 transition-colors duration-200">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ $project->project_name }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $project->location }}</p>
                                            <div class="flex items-center gap-3 mt-2">
                                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                                         style="width: {{ $project->progress_percent ?? 0 }}%"></div>
                                                </div>
                                                <span class="text-sm text-gray-600 whitespace-nowrap">{{ $project->progress_percent ?? 0 }}%</span>
                                            </div>
                                        </div>
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
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Stats -->
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-6 text-white">
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
                            </div>

                            <!-- Contact Info -->
                            <div class="bg-white rounded-lg border border-gray-200 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin liên hệ</h3>
                                <div class="space-y-3">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-envelope text-gray-400 w-5"></i>
                                        <span class="text-gray-700">{{ auth()->user()->email }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-phone text-gray-400 w-5"></i>
                                        <span class="text-gray-700">{{ auth()->user()->phone ?? 'Chưa cập nhật' }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-calendar text-gray-400 w-5"></i>
                                        <span class="text-gray-700">Tham gia: {{ auth()->user()->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Skills -->
                            <div class="bg-white rounded-lg border border-gray-200 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Kỹ năng</h3>
                                <div class="flex flex-wrap gap-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors duration-200 cursor-pointer">
                                        Quản lý dự án
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors duration-200 cursor-pointer">
                                        Xây dựng
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors duration-200 cursor-pointer">
                                        Giám sát
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors duration-200 cursor-pointer">
                                        Kế hoạch
                                    </span>
                                    <button class="inline-flex items-center px-3 py-1 rounded-full text-sm border border-dashed border-gray-300 text-gray-500 hover:border-gray-400 hover:text-gray-600 transition-colors duration-200">
                                        <i class="fas fa-plus mr-1"></i>
                                        Thêm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Profile Tab -->
                <div id="tab-edit" class="tab-content hidden">
                    <form class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tên đăng nhập <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       value="{{ auth()->user()->username }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       value="{{ auth()->user()->email }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
                                <input type="tel" 
                                       value="{{ auth()->user()->phone }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Vai trò</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-100" disabled>
                                    <option>{{ ucfirst(auth()->user()->user_type) }}</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Địa chỉ</label>
                                <textarea rows="3" 
                                          placeholder="Nhập địa chỉ của bạn"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Giới thiệu bản thân</label>
                                <textarea rows="4" 
                                          placeholder="Mô tả về bản thân và kinh nghiệm làm việc"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                                <i class="fas fa-save"></i>
                                Cập nhật hồ sơ
                            </button>
                            <button type="reset" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors duration-200 flex items-center gap-2">
                                <i class="fas fa-undo"></i>
                                Đặt lại
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Security Tab -->
                <div id="tab-security" class="tab-content hidden">
                    <div class="space-y-6">
                        <!-- Change Password -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Đổi mật khẩu</h3>
                            <form class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Mật khẩu hiện tại <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Mật khẩu mới <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           required>
                                    <p class="text-sm text-gray-500 mt-1">Mật khẩu phải có ít nhất 8 ký tự</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Xác nhận mật khẩu mới <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           required>
                                </div>

                                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                                    <i class="fas fa-key"></i>
                                    Đổi mật khẩu
                                </button>
                            </form>
                        </div>

                        <!-- Two-Factor Authentication -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Xác thực hai yếu tố</h3>
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-semibold text-gray-900">Xác thực hai yếu tố</h4>
                                    <p class="text-gray-600 text-sm mt-1">Tăng cường bảo mật cho tài khoản của bạn</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Tab -->
                <div id="tab-activity" class="tab-content hidden">
                    <div class="max-h-96 overflow-y-auto">
                        <div class="relative pl-8">
                            <!-- Timeline line -->
                            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                            
                            <!-- Activity items -->
                            <div class="space-y-6">
                                <!-- Activity Item 1 -->
                                <div class="relative">
                                    <div class="absolute -left-8 top-2 w-4 h-4 bg-blue-600 border-4 border-white rounded-full"></div>
                                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors duration-200">
                                        <div class="flex items-start gap-3">
                                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 flex-shrink-0">
                                                <i class="fas fa-project-diagram"></i>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900">Đã tạo dự án mới</h4>
                                                <p class="text-gray-600 mt-1">Dự án "Tòa nhà Sunshine" đã được tạo</p>
                                                <span class="text-sm text-gray-500 mt-2 block">2 giờ trước</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Activity Item 2 -->
                                <div class="relative">
                                    <div class="absolute -left-8 top-2 w-4 h-4 bg-green-600 border-4 border-white rounded-full"></div>
                                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors duration-200">
                                        <div class="flex items-start gap-3">
                                            <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-green-600 flex-shrink-0">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900">Cập nhật tiến độ</h4>
                                                <p class="text-gray-600 mt-1">Đã cập nhật tiến độ công việc "Xây dựng móng" lên 75%</p>
                                                <span class="text-sm text-gray-500 mt-2 block">5 giờ trước</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-tab {
    border-bottom: 2px solid transparent;
    padding: 1rem 1.5rem;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    font-weight: 500;
    background: none;
    border: none;
    outline: none;
}
.profile-tab.active {
    border-bottom-color: #3b82f6;
    color: #3b82f6;
    font-weight: 600;
}
.profile-tab:hover:not(.active) {
    color: #3b82f6;
    background-color: rgba(59, 130, 246, 0.05);
}
</style>
@endsection

@section('scripts')
<script>
// Định nghĩa hàm switchTab trước khi sử dụng
function switchTab(tabName) {
    console.log('Switching to tab:', tabName);
    
    // Ẩn tất cả tab content
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Xóa active class từ tất cả tabs
    const tabs = document.querySelectorAll('.profile-tab');
    tabs.forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Hiển thị tab content được chọn
    const selectedTabContent = document.getElementById(`tab-${tabName}`);
    if (selectedTabContent) {
        selectedTabContent.classList.remove('hidden');
        console.log('Tab content found and shown');
    } else {
        console.error('Tab content not found:', `tab-${tabName}`);
    }
    
    // Thêm active class cho tab được click
    const selectedTab = document.querySelector(`[data-tab="${tabName}"]`);
    if (selectedTab) {
        selectedTab.classList.add('active');
        console.log('Tab activated:', tabName);
    } else {
        console.error('Tab button not found:', tabName);
    }
}

// Khởi tạo khi DOM loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Profile page loaded');
    
    // Thêm event listener cho các tab
    const tabs = document.querySelectorAll('.profile-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            switchTab(tabName);
        });
    });
    
    // Avatar upload functionality
    const avatarUpload = document.getElementById('avatar-upload');
    if (avatarUpload) {
        avatarUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Vui lòng chọn file ảnh!');
                    return;
                }
                
                // Validate file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Kích thước ảnh không được vượt quá 2MB!');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    console.log('New avatar selected');
                    // Có thể thêm AJAX upload ở đây
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Đảm bảo tab overview hiển thị mặc định
    switchTab('overview');
});
</script>
@endsection