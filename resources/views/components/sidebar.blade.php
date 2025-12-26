@auth
<aside class="w-64 bg-white shadow-xl min-h-screen border-r border-slate-200">
    <nav class="p-4">
        <ul class="space-y-1">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('home') }}"
                    class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-300 group
                    {{ request()->routeIs('home') ? 'bg-indigo-50 text-indigo-700 font-bold border-r-4 border-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 hover:border-r-4 hover:border-indigo-400' }}">
                    <div class="w-8 h-8 flex items-center justify-center">
                        <i class="fas fa-tachometer-alt text-sm {{ request()->routeIs('home') ? 'text-indigo-600' : 'text-slate-500 group-hover:text-indigo-600' }}"></i>
                    </div>
                    <span class="text-sm font-semibold">Dashboard</span>
                </a>
            </li>
            
            <!-- Quản lý dự án -->
            @if(auth()->user()->user_type === 'admin')
                <li class="mt-8 mb-3">
                    <p class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Quản lý dự án</p>
                </li>
                <li>
                    <a href="{{ route('admin.projects.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-300 group
                        {{ request()->routeIs('admin.projects.*') ? 'bg-indigo-50 text-indigo-700 font-bold border-r-4 border-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 hover:border-r-4 hover:border-indigo-400' }}">
                        <div class="w-8 h-8 flex items-center justify-center">
                            <i class="fas fa-project-diagram text-sm {{ request()->routeIs('admin.projects.*') ? 'text-indigo-600' : 'text-slate-500 group-hover:text-indigo-600' }}"></i>
                        </div>
                        <span class="text-sm font-semibold">Dự án</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.sites.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-300 group
                        {{ request()->routeIs('admin.sites.*') ? 'bg-indigo-50 text-indigo-700 font-bold border-r-4 border-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 hover:border-r-4 hover:border-indigo-400' }}">
                        <div class="w-8 h-8 flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-sm {{ request()->routeIs('admin.sites.*') ? 'text-indigo-600' : 'text-slate-500 group-hover:text-indigo-600' }}"></i>
                        </div>
                        <span class="text-sm font-semibold">Công trường</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.tasks.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-300 group
                        {{ request()->routeIs('admin.tasks.*') ? 'bg-indigo-50 text-indigo-700 font-bold border-r-4 border-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 hover:border-r-4 hover:border-indigo-400' }}">
                        <div class="w-8 h-8 flex items-center justify-center">
                            <i class="fas fa-tasks text-sm {{ request()->routeIs('admin.tasks.*') ? 'text-indigo-600' : 'text-slate-500 group-hover:text-indigo-600' }}"></i>
                        </div>
                        <span class="text-sm font-semibold">Công việc</span>
                    </a>
                </li>
                
                <!-- Tiến độ & Vật tư -->
                <li class="mt-8 mb-3">
                    <p class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Tiến độ & Vật tư</p>
                </li>
                <li>
                    <a href="{{ route('admin.progress_updates.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-300 group
                        {{ request()->routeIs('admin.progress_updates.*') ? 'bg-indigo-50 text-indigo-700 font-bold border-r-4 border-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 hover:border-r-4 hover:border-indigo-400' }}">
                        <div class="w-8 h-8 flex items-center justify-center">
                            <i class="fas fa-chart-line text-sm {{ request()->routeIs('admin.progress_updates.*') ? 'text-indigo-600' : 'text-slate-500 group-hover:text-indigo-600' }}"></i>
                        </div>
                        <span class="text-sm font-semibold">Cập nhật tiến độ</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.materials.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-300 group
                        {{ request()->routeIs('admin.materials.*') ? 'bg-indigo-50 text-indigo-700 font-bold border-r-4 border-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 hover:border-r-4 hover:border-indigo-400' }}">
                        <div class="w-8 h-8 flex items-center justify-center">
                            <i class="fas fa-boxes text-sm {{ request()->routeIs('admin.materials.*') ? 'text-indigo-600' : 'text-slate-500 group-hover:text-indigo-600' }}"></i>
                        </div>
                        <span class="text-sm font-semibold">Vật tư</span>
                    </a>
                </li>
                
                <!-- Hợp đồng & Tài chính -->
                <li class="mt-8 mb-3">
                    <p class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Hợp đồng & Tài chính</p>
                </li>
                <li>
                    <a href="{{ route('admin.contracts.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-300 group
                        {{ request()->routeIs('admin.contracts.*') ? 'bg-indigo-50 text-indigo-700 font-bold border-r-4 border-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 hover:border-r-4 hover:border-indigo-400' }}">
                        <div class="w-8 h-8 flex items-center justify-center">
                            <i class="fas fa-file-contract text-sm {{ request()->routeIs('admin.contracts.*') ? 'text-indigo-600' : 'text-slate-500 group-hover:text-indigo-600' }}"></i>
                        </div>
                        <span class="text-sm font-semibold">Hợp đồng</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.payments.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-300 group
                        {{ request()->routeIs('admin.payments.*') ? 'bg-indigo-50 text-indigo-700 font-bold border-r-4 border-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 hover:border-r-4 hover:border-indigo-400' }}">
                        <div class="w-8 h-8 flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-sm {{ request()->routeIs('admin.payments.*') ? 'text-indigo-600' : 'text-slate-500 group-hover:text-indigo-600' }}"></i>
                        </div>
                        <span class="text-sm font-semibold">Thanh toán</span>
                    </a>
                </li>
                
                <!-- Quản lý hệ thống -->
                <li class="mt-8 mb-3">
                    <p class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Quản lý hệ thống</p>
                </li>
                <li>
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-300 group
                        {{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 text-indigo-700 font-bold border-r-4 border-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 hover:border-r-4 hover:border-indigo-400' }}">
                        <div class="w-8 h-8 flex items-center justify-center">
                            <i class="fas fa-users text-sm {{ request()->routeIs('admin.users.*') ? 'text-indigo-600' : 'text-slate-500 group-hover:text-indigo-600' }}"></i>
                        </div>
                        <span class="text-sm font-semibold">Người dùng</span>
                    </a>
                </li>
                
            @elseif(in_array(auth()->user()->user_type, ['client', 'owner', 'contractor', 'engineer']))                <!-- Menu dành cho Client -->
                <li class="mt-8 mb-3">
                    <p class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Dự án của tôi</p>
                </li>
                <li>
                    <a href="{{ route('client.projects.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-300 group
                        {{ request()->routeIs('client.projects.*') ? 'bg-indigo-50 text-indigo-700 font-bold border-r-4 border-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 hover:border-r-4 hover:border-indigo-400' }}">
                        <div class="w-8 h-8 flex items-center justify-center">
                            <i class="fas fa-project-diagram text-sm {{ request()->routeIs('client.projects.*') ? 'text-indigo-600' : 'text-slate-500 group-hover:text-indigo-600' }}"></i>
                        </div>
                        <span class="text-sm font-semibold">Dự án</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('client.progress_updates.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-300 group
                        {{ request()->routeIs('client.progress.*') ? 'bg-indigo-50 text-indigo-700 font-bold border-r-4 border-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 hover:border-r-4 hover:border-indigo-400' }}">
                        <div class="w-8 h-8 flex items-center justify-center">
                            <i class="fas fa-chart-line text-sm {{ request()->routeIs('client.progress_updates.*') ? 'text-indigo-600' : 'text-slate-500 group-hover:text-indigo-600' }}"></i>
                        </div>
                        <span class="text-sm font-semibold">Tiến độ</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('client.contracts.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-300 group
                        {{ request()->routeIs('client.contracts.*') ? 'bg-indigo-50 text-indigo-700 font-bold border-r-4 border-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 hover:border-r-4 hover:border-indigo-400' }}">
                        <div class="w-8 h-8 flex items-center justify-center">
                            <i class="fas fa-file-contract text-sm {{ request()->routeIs('client.contracts.*') ? 'text-indigo-600' : 'text-slate-500 group-hover:text-indigo-600' }}"></i>
                        </div>
                        <span class="text-sm font-semibold">Hợp đồng</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('client.payments.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-300 group
                        {{ request()->routeIs('client.payments.*') ? 'bg-indigo-50 text-indigo-700 font-bold border-r-4 border-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 hover:border-r-4 hover:border-indigo-400' }}">
                        <div class="w-8 h-8 flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-sm {{ request()->routeIs('client.payments.*') ? 'text-indigo-600' : 'text-slate-500 group-hover:text-indigo-600' }}"></i>
                        </div>
                        <span class="text-sm font-semibold">Thanh toán</span>
                    </a>
                </li>
            @endif
            
            <!-- Profile & Settings -->
            <li class="mt-8 mb-3">
                <p class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Tài khoản</p>
            </li>
            <li>
                <a href="{{ route('profile') }}"
                    class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-300 group
                    {{ request()->routeIs('profile') ? 'bg-indigo-50 text-indigo-700 font-bold border-r-4 border-indigo-600' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 hover:border-r-4 hover:border-indigo-400' }}">
                    <div class="w-8 h-8 flex items-center justify-center">
                        <i class="fas fa-user-circle text-sm {{ request()->routeIs('profile') ? 'text-indigo-600' : 'text-slate-500 group-hover:text-indigo-600' }}"></i>
                    </div>
                    <span class="text-sm font-semibold">Thông tin cá nhân</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
@endauth