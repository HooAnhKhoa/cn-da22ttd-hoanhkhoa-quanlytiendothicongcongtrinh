@auth
<aside class="w-64 bg-white shadow-lg min-h-screen">
    <nav class="p-4">
        <ul class="space-y-2">
            <li>
                <a href="{{ route('home') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-tachometer-alt w-6"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Quản lý dự án -->
            <li class="mt-6">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase">Quản lý dự án</p>
            </li>
            <li>
                <a href="{{ route('projects.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-project-diagram w-6"></i>
                    <span>Dự án</span>
                </a>
            </li>
            <li>
                <a href="{{ route('sites.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-map-marker-alt w-6"></i>
                    <span>Công trường</span>
                </a>
            </li>
            <li>
                <a href="{{ route('tasks.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-tasks w-6"></i>
                    <span>Công việc</span>
                </a>
            </li>
            
            <!-- Tiến độ & Vật tư -->
            <li class="mt-6">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase">Tiến độ & Vật tư</p>
            </li>
            <li>
                <a href="{{ route('progress_updates.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-chart-line w-6"></i>
                    <span>Cập nhật tiến độ</span>
                </a>
            </li>
            <li>
                <a href="{{ route('materials.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-boxes w-6"></i>
                    <span>Vật tư</span>
                </a>
            </li>
            
            <!-- Quản lý chất lượng -->
            {{-- <li class="mt-6">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase">Quản lý chất lượng</p>
            </li>
            <li>
                <a href="{{ route('inspections.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-clipboard-check w-6"></i>
                    <span>Kiểm tra</span>
                </a>
            </li>
            <li>
                <a href="{{ route('issues.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-exclamation-triangle w-6"></i>
                    <span>Sự cố</span>
                </a>
            </li>
            <li>
                <a href="{{ route('delays.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-clock w-6"></i>
                    <span>Độ trễ</span>
                </a>
            </li> --}}
            
            <!-- Hợp đồng & Tài chính -->
            <li class="mt-6">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase">Hợp đồng & Tài chính</p>
            </li>
            <li>
                <a href="{{ route('contracts.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-file-contract w-6"></i>
                    <span>Hợp đồng</span>
                </a>
            </li>
            <li>
                <a href="{{ route('payments.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-money-bill-wave w-6"></i>
                    <span>Thanh toán</span>
                </a>
            </li>
            
            <!-- Tài liệu -->
            {{-- <li class="mt-6">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase">Tài liệu</p>
            </li>
            <li>
                <a href="{{ route('documents.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-file-alt w-6"></i>
                    <span>Tài liệu</span>
                </a>
            </li>
            <li>
                <a href="{{ route('drawings.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-drafting-compass w-6"></i>
                    <span>Bản vẽ</span>
                </a>
            </li> --}}
            
            <!-- Quản lý hệ thống -->
            @if(auth()->user()->user_type === 'admin')
            <li class="mt-6">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase">Quản lý hệ thống</p>
            </li>
            <li>
                <a href="{{ route('users.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-users w-6"></i>
                    <span>Người dùng</span>
                </a>
            </li>
            @endif
        </ul>
    </nav>
</aside>
@endauth