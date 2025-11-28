<header class="bg-blue-600 text-white shadow-lg">
    <div class="container mx-auto px-6 py-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <h1 class="text-2xl font-bold">
                    <i class="fas fa-hard-hat mr-2"></i>
                    Construction Manager
                </h1>
            </div>
            
            <div class="flex items-center space-x-4">
                @auth
                    <div class="flex items-center space-x-3">
                        <span class="text-blue-100">Xin chào,</span>
                        <div class="relative">
                            <button 
                                id="userMenuButton"
                                class="flex items-center space-x-2 bg-blue-700 px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors"
                            >
                                <i class="fas fa-user-circle"></i>
                                <span>{{ auth()->user()->username }}</span>
                                <i class="fas fa-chevron-down transition-transform" id="chevronIcon"></i>
                            </button>
                            <div 
                                id="userDropdown"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50 border border-gray-200 hidden"
                            >
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->username }}</p>
                                    <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->user_type }}</p>
                                </div>
                                <a 
                                    href="{{ route('users.show', auth()->id()) }}" 
                                    class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors"
                                >
                                    <i class="fas fa-user mr-3 w-4 text-center"></i>
                                    Thông tin cá nhân
                                </a>
                                <form action="{{ route('logout') }}" method="POST" class="border-t border-gray-100">
                                    @csrf
                                    <button 
                                        type="submit" 
                                        class="flex items-center w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors"
                                    >
                                        <i class="fas fa-sign-out-alt mr-3 w-4 text-center"></i>
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('login') }}" class="bg-blue-700 px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors">
                            <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập
                        </a>
                        <a href="{{ route('register') }}" class="bg-green-600 px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-user-plus mr-2"></i>Đăng ký
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