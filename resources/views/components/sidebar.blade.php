@auth
<aside class="w-64 bg-white shadow-lg min-h-screen border-r border-gray-200">
    <div class="p-4 border-b border-gray-100">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-hard-hat text-blue-600"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">
                    {{ auth()->user()->name ?? auth()->user()->username }}
                </h3>
                <p class="text-xs text-gray-500 capitalize">
                    @if(auth()->user()->user_type === 'admin')
                        Quản trị viên
                    @elseif(auth()->user()->user_type === 'ownor')
                        Khách hàng
                    @else
                        {{ auth()->user()->user_type }}
                    @endif
                </p>
            </div>
        </div>
    </div>
    
    <nav class="p-4">
        <ul class="space-y-1">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('home') }}"
                    class="flex items-center space-x-3 p-3 rounded-lg transition-colors
                    {{ request()->routeIs('home') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-l-4 hover:border-blue-400' }}">
                    <i class="fas fa-tachometer-alt w-5 text-center {{ request()->routeIs('home') ? 'text-blue-600' : 'text-gray-500' }}"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Quản lý dự án -->
            @if(auth()->user()->user_type === 'admin')
                <li class="mt-6 mb-2">
                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Quản lý dự án</p>
                </li>
                <li>
                    <a href="{{ route('admin.projects.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-lg transition-colors
                        {{ request()->routeIs('admin.projects.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-l-4 hover:border-blue-400' }}">
                        <i class="fas fa-project-diagram w-5 text-center {{ request()->routeIs('admin.projects.*') ? 'text-blue-600' : 'text-gray-500' }}"></i>
                        <span>Dự án</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.sites.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-lg transition-colors
                        {{ request()->routeIs('admin.sites.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-l-4 hover:border-blue-400' }}">
                        <i class="fas fa-map-marker-alt w-5 text-center {{ request()->routeIs('admin.sites.*') ? 'text-blue-600' : 'text-gray-500' }}"></i>
                        <span>Công trường</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.tasks.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-lg transition-colors
                        {{ request()->routeIs('admin.tasks.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-l-4 hover:border-blue-400' }}">
                        <i class="fas fa-tasks w-5 text-center {{ request()->routeIs('admin.tasks.*') ? 'text-blue-600' : 'text-gray-500' }}"></i>
                        <span>Công việc</span>
                    </a>
                </li>
                
                <!-- Tiến độ & Vật tư -->
                <li class="mt-6 mb-2">
                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tiến độ & Vật tư</p>
                </li>
                <li>
                    <a href="{{ route('admin.progress_updates.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-lg transition-colors
                        {{ request()->routeIs('admin.progress_updates.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-l-4 hover:border-blue-400' }}">
                        <i class="fas fa-chart-line w-5 text-center {{ request()->routeIs('admin.progress_updates.*') ? 'text-blue-600' : 'text-gray-500' }}"></i>
                        <span>Cập nhật tiến độ</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.materials.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-lg transition-colors
                        {{ request()->routeIs('admin.materials.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-l-4 hover:border-blue-400' }}">
                        <i class="fas fa-boxes w-5 text-center {{ request()->routeIs('admin.materials.*') ? 'text-blue-600' : 'text-gray-500' }}"></i>
                        <span>Vật tư</span>
                    </a>
                </li>
                
                <!-- Hợp đồng & Tài chính -->
                <li class="mt-6 mb-2">
                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Hợp đồng & Tài chính</p>
                </li>
                <li>
                    <a href="{{ route('admin.contracts.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-lg transition-colors
                        {{ request()->routeIs('admin.contracts.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-l-4 hover:border-blue-400' }}">
                        <i class="fas fa-file-contract w-5 text-center {{ request()->routeIs('admin.contracts.*') ? 'text-blue-600' : 'text-gray-500' }}"></i>
                        <span>Hợp đồng</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.payments.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-lg transition-colors
                        {{ request()->routeIs('admin.payments.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-l-4 hover:border-blue-400' }}">
                        <i class="fas fa-money-bill-wave w-5 text-center {{ request()->routeIs('admin.payments.*') ? 'text-blue-600' : 'text-gray-500' }}"></i>
                        <span>Thanh toán</span>
                    </a>
                </li>
                
                <!-- Quản lý hệ thống -->
                <li class="mt-6 mb-2">
                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Quản lý hệ thống</p>
                </li>
                <li>
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-lg transition-colors
                        {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-l-4 hover:border-blue-400' }}">
                        <i class="fas fa-users w-5 text-center {{ request()->routeIs('admin.users.*') ? 'text-blue-600' : 'text-gray-500' }}"></i>
                        <span>Người dùng</span>
                    </a>
                </li>
                
            @elseif(auth()->user()->user_type === 'client')
                <!-- Menu dành cho Client -->
                <li class="mt-6 mb-2">
                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Dự án của tôi</p>
                </li>
                <li>
                    <a href="{{ route('client.projects.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-lg transition-colors
                        {{ request()->routeIs('client.projects.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-l-4 hover:border-blue-400' }}">
                        <i class="fas fa-project-diagram w-5 text-center {{ request()->routeIs('client.projects.*') ? 'text-blue-600' : 'text-gray-500' }}"></i>
                        <span>Dự án</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('client.progress.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-lg transition-colors
                        {{ request()->routeIs('client.progress.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-l-4 hover:border-blue-400' }}">
                        <i class="fas fa-chart-line w-5 text-center {{ request()->routeIs('client.progress.*') ? 'text-blue-600' : 'text-gray-500' }}"></i>
                        <span>Tiến độ</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('client.contracts.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-lg transition-colors
                        {{ request()->routeIs('client.contracts.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-l-4 hover:border-blue-400' }}">
                        <i class="fas fa-file-contract w-5 text-center {{ request()->routeIs('client.contracts.*') ? 'text-blue-600' : 'text-gray-500' }}"></i>
                        <span>Hợp đồng</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('client.payments.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-lg transition-colors
                        {{ request()->routeIs('client.payments.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-l-4 hover:border-blue-400' }}">
                        <i class="fas fa-money-bill-wave w-5 text-center {{ request()->routeIs('client.payments.*') ? 'text-blue-600' : 'text-gray-500' }}"></i>
                        <span>Thanh toán</span>
                    </a>
                </li>
            @endif
            
            <!-- Profile & Settings -->
            <li class="mt-6 mb-2">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tài khoản</p>
            </li>
            <li>
                <a href="{{ route('profile') }}"
                    class="flex items-center space-x-3 p-3 rounded-lg transition-colors
                    {{ request()->routeIs('profile') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-l-4 hover:border-blue-400' }}">
                    <i class="fas fa-user-circle w-5 text-center {{ request()->routeIs('profile') ? 'text-blue-600' : 'text-gray-500' }}"></i>
                    <span>Thông tin cá nhân</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
@endauth