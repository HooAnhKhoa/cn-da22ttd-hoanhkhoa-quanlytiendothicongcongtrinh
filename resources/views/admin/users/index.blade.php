@extends('layouts.app')

@section('title', 'Quản lý Người dùng')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Danh sách Người dùng</h1>
        <a href="{{ route('admin.users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Thêm mới
        </a>
    </div>

    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Tìm theo tên, email, sđt..." 
                    class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="w-48">
                <select name="role" class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Tất cả vai trò --</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                    <option value="owner" {{ request('role') == 'owner' ? 'selected' : '' }}>Chủ đầu tư</option>
                    <option value="contractor" {{ request('role') == 'contractor' ? 'selected' : '' }}>Nhà thầu</option>
                    <option value="engineer" {{ request('role') == 'engineer' ? 'selected' : '' }}>Kỹ sư</option>
                </select>
            </div>
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                <i class="fas fa-search mr-2"></i>Lọc
            </button>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                Đặt lại
            </a>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thông tin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Liên hệ</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đổi trạng thái nhanh</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 relative">
                                @if($user->avatar)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $user->avatar) }}" alt="">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                @endif

                                @if($user->hasPendingRoleRequest())
                                    <span class="absolute top-0 right-0 block h-3 w-3 rounded-full ring-2 ring-white bg-red-600" title="Có yêu cầu đổi vai trò mới"></span>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $user->username }}
                                    @if($user->hasPendingRoleRequest())
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                            Yêu cầu đổi vai trò
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">ID: #{{ $user->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $user->email }}</div>
                        <div class="text-sm text-gray-500">{{ $user->phone ?? '---' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $roles = [
                                'admin' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Admin'],
                                'owner' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'label' => 'Chủ đầu tư'],
                                'contractor' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Nhà thầu'],
                                'engineer' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Kỹ sư'],
                            ];
                            $role = $roles[$user->user_type] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => $user->user_type];
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $role['bg'] }} {{ $role['text'] }}">
                            {{ $role['label'] }}
                        </span>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <form action="{{ route('admin.users.update-status', $user) }}" method="POST" class="flex items-center">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()" 
                                class="text-xs rounded-full border-gray-300 py-1 pl-2 pr-6 focus:ring-blue-500 focus:border-blue-500 cursor-pointer
                                {{ $user->status == 'active' ? 'bg-green-50 text-green-700 border-green-200' : ($user->status == 'banned' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-gray-50 text-gray-700 border-gray-200') }}">
                                <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Tạm khóa</option>
                                <option value="banned" {{ $user->status == 'banned' ? 'selected' : '' }}>Bị chặn</option>
                            </select>
                        </form>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-900 mr-3" title="Xem chi tiết">
                            <i class="fas fa-eye"></i>
                        </a>

                        <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Chỉnh sửa">
                            <i class="fas fa-edit"></i>
                        </a>

                        @if(auth()->id() !== $user->id)
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Không tìm thấy người dùng nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection