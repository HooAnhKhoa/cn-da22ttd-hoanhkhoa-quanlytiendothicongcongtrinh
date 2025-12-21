<header class="bg-gradient-to-r from-blue-700 to-blue-600 text-white shadow-lg">
    <div class="container mx-auto px-6 py-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}" class="flex items-center space-x-2 hover:opacity-90 transition-opacity">
                    <i class="fas fa-hard-hat text-2xl"></i>
                    <h1 class="text-2xl font-bold">
                        Build<span class="text-blue-200">Manage</span>
                    </h1>
                </a>
            </div>
            
            <div class="flex items-center space-x-4">
                @auth
                    <div class="flex items-center space-x-3">
                        <span class="text-blue-100 text-sm hidden md:block">Xin chào,</span>
                        <div class="relative">
                            <button 
                                id="userMenuButton"
                                class="flex items-center space-x-2 bg-blue-800/50 hover:bg-blue-800 px-4 py-2 rounded-lg transition-colors border border-blue-500/30"
                            >
                                <i class="fas fa-user-circle"></i>
                                <span class="font-medium">{{ auth()->user()->name ?? auth()->user()->username }}</span>
                                <i class="fas fa-chevron-down transition-transform duration-200 text-sm" id="chevronIcon"></i>
                            </button>
                            <div 
                                id="userDropdown"
                                class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl py-2 z-50 border border-gray-200 hidden transition-all duration-200"
                            >
                                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 rounded-t-lg">
                                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? auth()->user()->username }}</p>
                                    <p class="text-xs text-gray-600 capitalize mt-1">
                                        @if(auth()->user()->role === 'admin')
                                            <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-xs">Quản trị viên</span>
                                        @elseif(auth()->user()->role === 'client')
                                            <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs">Khách hàng</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-800 px-2 py-0.5 rounded text-xs">{{ auth()->user()->role }}</span>
                                        @endif
                                    </p>
                                </div>
                                <a 
                                    href="{{ route('profile') }}" 
                                    class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors"
                                >
                                    <i class="fas fa-user mr-3 w-4 text-center text-gray-500"></i>
                                    <span class="text-sm font-medium">Thông tin cá nhân</span>
                                </a>
                                <a 
                                    href="{{ route('profile') }}#settings" 
                                    class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors"
                                >
                                    <i class="fas fa-cog mr-3 w-4 text-center text-gray-500"></i>
                                    <span class="text-sm font-medium">Cài đặt</span>
                                </a>
                                <div class="border-t border-gray-100 pt-2 mt-2">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="flex items-center w-full text-left px-4 py-2.5 text-red-600 hover:bg-red-50 transition-colors"
                                        >
                                            <i class="fas fa-sign-out-alt mr-3 w-4 text-center"></i>
                                            <span class="text-sm font-medium">Đăng xuất</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('login') }}" 
                           class="bg-blue-800/50 hover:bg-blue-800 px-4 py-2 rounded-lg transition-colors border border-blue-500/30">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            <span class="font-medium">Đăng nhập</span>
                        </a>
                        <a href="{{ route('register') }}" 
                           class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg transition-colors shadow-md">
                            <i class="fas fa-user-plus mr-2"></i>
                            <span class="font-medium">Đăng ký</span>
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userMenuButton = document.getElementById('userMenuButton');
    const userDropdown = document.getElementById('userDropdown');
    const chevronIcon = document.getElementById('chevronIcon');

    if (userMenuButton && userDropdown) {
        // Toggle dropdown menu
        userMenuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
            chevronIcon.classList.toggle('rotate-180');
        });

        // Đóng dropdown khi click bên ngoài
        document.addEventListener('click', function(e) {
            if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.add('hidden');
                chevronIcon.classList.remove('rotate-180');
            }
        });

        // Đóng dropdown khi click vào menu item
        userDropdown.addEventListener('click', function() {
            userDropdown.classList.add('hidden');
            chevronIcon.classList.remove('rotate-180');
        });
    }
});
</script>