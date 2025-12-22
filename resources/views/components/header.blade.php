<header class="bg-gradient-to-r from-slate-800 to-slate-900 shadow-lg border-b border-slate-700">
    <div class="container mx-auto px-6 py-4">
        <div class="flex justify-between items-center">
            <!-- Phần bên trái: Logo BuildManage -->
            <div class="flex-1">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 hover:opacity-90 transition-opacity w-fit group">
                    <div class="w-10 h-10 bg-indigo-500 rounded-xl shadow-lg flex items-center justify-center transform group-hover:rotate-6 transition-transform duration-300">
                        <i class="fas fa-hard-hat text-lg text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-white tracking-tighter">
                            Build<span class="text-indigo-300">Manage</span>
                        </h1>
                        <p class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">Professional Construction</p>
                    </div>
                </a>
            </div>
            
            <!-- Phần bên phải: User menu -->
            <div class="flex-1 flex justify-end">
                @auth
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <button 
                                id="userMenuButton"
                                class="flex items-center space-x-3 bg-slate-700 hover:bg-slate-600 px-4 py-2.5 rounded-xl transition-all duration-300 border border-slate-600 shadow-md hover:shadow-lg group"
                            >
                                <div class="w-8 h-8 bg-indigo-500/20 rounded-lg flex items-center justify-center border border-indigo-400/30">
                                    <i class="fas fa-user-circle text-indigo-300 text-sm"></i>
                                </div>
                                <span class="font-bold text-white text-sm">{{ auth()->user()->name ?? auth()->user()->username }}</span>
                                <i class="fas fa-chevron-down transition-transform duration-300 text-xs text-slate-300 group-hover:text-indigo-300" id="chevronIcon"></i>
                            </button>
                            <div 
                                id="userDropdown"
                                class="absolute right-0 mt-2 w-60 bg-slate-800 rounded-2xl shadow-xl py-2 z-50 border border-slate-700 hidden transition-all duration-300 origin-top-right"
                            >
                                <div class="px-5 py-4 border-b border-slate-700 bg-slate-900/50 rounded-t-2xl">
                                    <p class="text-sm font-bold text-white">{{ auth()->user()->name ?? auth()->user()->username }}</p>
                                    <p class="text-xs text-slate-300 font-medium mt-1 capitalize">
                                        @if(auth()->user()->user_type === 'admin')
                                            <span class="bg-indigo-500/20 text-indigo-300 px-2.5 py-1 rounded-lg text-[10px] font-bold border border-indigo-500/30">Quản trị viên</span>
                                        @elseif(auth()->user()->user_type === 'client')
                                            <span class="bg-green-500/20 text-green-300 px-2.5 py-1 rounded-lg text-[10px] font-bold border border-green-500/30">Khách hàng</span>
                                        @else
                                            <span class="bg-slate-700 text-slate-300 px-2.5 py-1 rounded-lg text-[10px] font-bold border border-slate-600">{{ auth()->user()->user_type }}</span>
                                        @endif
                                    </p>
                                </div>
                                <a 
                                    href="{{ route('profile') }}" 
                                    class="flex items-center px-5 py-3 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors group"
                                >
                                    <i class="fas fa-user mr-3 w-4 text-center text-slate-400 group-hover:text-indigo-300"></i>
                                    <span class="text-sm font-semibold">Thông tin cá nhân</span>
                                </a>
                                <a 
                                    href="{{ route('profile') }}#settings" 
                                    class="flex items-center px-5 py-3 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors group"
                                >
                                    <i class="fas fa-cog mr-3 w-4 text-center text-slate-400 group-hover:text-indigo-300"></i>
                                    <span class="text-sm font-semibold">Cài đặt tài khoản</span>
                                </a>
                                <div class="border-t border-slate-700 pt-2 mt-2">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="flex items-center w-full text-left px-5 py-3 text-red-400 hover:bg-red-900/30 hover:text-red-300 transition-colors group rounded-b-2xl"
                                        >
                                            <i class="fas fa-sign-out-alt mr-3 w-4 text-center group-hover:rotate-180 transition-transform"></i>
                                            <span class="text-sm font-semibold">Đăng xuất</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}" 
                           class="bg-slate-700 hover:bg-slate-600 px-4 py-2.5 rounded-xl transition-all duration-300 border border-slate-600 shadow-md hover:shadow-lg text-slate-300 hover:text-white font-semibold text-sm">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            <span>Đăng nhập</span>
                        </a>
                        <a href="{{ route('register') }}" 
                           class="bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 px-4 py-2.5 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl text-white font-bold text-sm">
                            <i class="fas fa-user-plus mr-2"></i>
                            <span>Đăng ký</span>
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