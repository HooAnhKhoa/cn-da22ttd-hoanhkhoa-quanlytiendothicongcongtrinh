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
                    <span class="text-blue-100">Xin chào, {{ auth()->user()->username }}</span>
                    <div class="relative group">
                        <button class="flex items-center space-x-2 bg-blue-700 px-4 py-2 rounded-lg hover:bg-blue-800">
                            <i class="fas fa-user-circle"></i>
                            <span>{{ auth()->user()->user_type }}</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden group-hover:block z-50">
                            <a href="{{ route('users.show', auth()->id()) }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>Thông tin cá nhân
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="bg-blue-700 px-4 py-2 rounded-lg hover:bg-blue-800">
                        <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="bg-green-600 px-4 py-2 rounded-lg hover:bg-green-700">
                        <i class="fas fa-user-plus mr-2"></i>Đăng ký
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>