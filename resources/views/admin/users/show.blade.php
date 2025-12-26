@extends('layouts.app')

@section('title', 'Chi tiết Người dùng')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700 bg-white p-2 rounded-full shadow-sm">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Hồ sơ: {{ $user->username }}</h1>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.users.edit', $user) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-edit mr-2"></i>Chỉnh sửa
            </a>
            @if(auth()->id() !== $user->id)
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-100 text-red-700 px-4 py-2 rounded-lg hover:bg-red-200 transition flex items-center">
                    <i class="fas fa-trash mr-2"></i>Xóa
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6 text-center border-b border-gray-100">
                    <div class="relative inline-block">
                        @if($user->avatar)
                            <img class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-lg mx-auto" src="{{ asset('storage/' . $user->avatar) }}" alt="">
                        @else
                            <div class="h-32 w-32 rounded-full bg-gray-200 flex items-center justify-center mx-auto border-4 border-white shadow-lg">
                                <i class="fas fa-user text-5xl text-gray-400"></i>
                            </div>
                        @endif
                        <span class="absolute bottom-1 right-1 h-5 w-5 rounded-full border-2 border-white {{ $user->status === 'active' ? 'bg-green-500' : ($user->status === 'inactive' ? 'bg-yellow-500' : 'bg-red-500') }}"></span>
                    </div>
                    <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $user->username }}</h2>
                    <p class="text-gray-500 text-sm">{{ $user->email }}</p>
                    
                    <div class="mt-4 flex justify-center">
                        @php
                            $roles = [
                                'admin' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Quản trị viên'],
                                'owner' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'label' => 'Chủ đầu tư'],
                                'contractor' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Nhà thầu'],
                                'engineer' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Kỹ sư'],
                            ];
                            $role = $roles[$user->user_type] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => $user->user_type];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $role['bg'] }} {{ $role['text'] }}">
                            {{ $role['label'] }}
                        </span>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 text-sm">ID</span>
                        <span class="font-medium text-gray-900">#{{ $user->id }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 text-sm">Số điện thoại</span>
                        <span class="font-medium text-gray-900">{{ $user->phone ?? 'Chưa cập nhật' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 text-sm">Ngày tham gia</span>
                        <span class="font-medium text-gray-900">{{ $user->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 text-sm">Trạng thái</span>
                        @if($user->status == 'active')
                            <span class="text-green-600 text-sm font-medium"><i class="fas fa-check-circle mr-1"></i>Hoạt động</span>
                        @elseif($user->status == 'inactive')
                            <span class="text-yellow-600 text-sm font-medium"><i class="fas fa-pause-circle mr-1"></i>Tạm khóa</span>
                        @else
                            <span class="text-red-600 text-sm font-medium"><i class="fas fa-ban mr-1"></i>Bị chặn</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-gray-800 mb-4">Hoạt động</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-3 rounded-lg text-center">
                        <div class="text-2xl font-bold text-blue-600">
                            {{ $user->owned_projects_count + $user->contracted_projects_count + $user->engineered_projects_count }}
                        </div>
                        <div class="text-xs text-gray-500">Dự án</div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg text-center">
                        <div class="text-2xl font-bold text-purple-600">
                            {{ $user->contracts_count }}
                        </div>
                        <div class="text-xs text-gray-500">Hợp đồng</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            @if($user->bio || $user->address)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-gray-800 mb-4">Thông tin thêm</h3>
                @if($user->address)
                <div class="mb-4">
                    <span class="block text-sm text-gray-500 mb-1">Địa chỉ</span>
                    <p class="text-gray-900">{{ $user->address }}</p>
                </div>
                @endif
                @if($user->bio)
                <div>
                    <span class="block text-sm text-gray-500 mb-1">Giới thiệu</span>
                    <p class="text-gray-900 whitespace-pre-line">{{ $user->bio }}</p>
                </div>
                @endif
            </div>
            @endif

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Lịch sử Yêu cầu Đổi vai trò</h3>
                    @if($user->hasPendingRoleRequest())
                        <span class="animate-pulse bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-bold border border-red-200">
                            <i class="fas fa-bell mr-1"></i> Cần xử lý
                        </span>
                    @endif
                </div>
                
                @if(count($user->getRoleChangeRequestsList()) > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($user->getRoleChangeRequestsList() as $request)
                    <div class="p-6 transition {{ $request['status'] == 'pending' ? 'bg-yellow-50 border-l-4 border-yellow-400' : 'hover:bg-gray-50' }}">
                        
                        {{-- Header của Request --}}
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="font-medium text-gray-900">Yêu cầu lên: 
                                    <span class="uppercase text-blue-600 font-bold">{{ $request['requested_role'] }}</span>
                                </span>
                                <span class="text-gray-500 text-sm ml-2">
                                    • {{ \Carbon\Carbon::parse($request['created_at'])->format('H:i d/m/Y') }}
                                </span>
                            </div>
                            
                            {{-- Badge Trạng thái --}}
                            <span class="px-2 py-1 text-xs rounded-full font-medium
                                @if($request['status'] == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($request['status'] == 'approved') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($request['status']) }}
                            </span>
                        </div>
                        
                        {{-- Lý do --}}
                        <div class="bg-white/60 p-3 rounded text-sm text-gray-700 mb-3 border border-gray-200">
                            <span class="font-semibold text-gray-500 block text-xs uppercase mb-1">Lý do:</span>
                            {{ $request['reason'] }}
                        </div>

                        {{-- Hiển thị Note của Admin nếu đã xử lý --}}
                        @if(!empty($request['admin_notes']))
                        <div class="text-sm border-l-4 border-blue-200 pl-3 py-1 mb-2">
                            <span class="font-semibold text-blue-800">Admin phản hồi:</span>
                            <p class="text-gray-600 mt-1">{{ $request['admin_notes'] }}</p>
                        </div>
                        @endif

                        {{-- [QUAN TRỌNG] Nút Hành động: Chỉ hiện khi trạng thái là 'pending' --}}
                        @if($request['status'] == 'pending')
                        <div class="mt-4 pt-4 border-t border-gray-200/50 flex gap-3">
                            
                            {{-- Form Đồng ý --}}
                            <form action="{{ route('admin.users.approve-role-request', ['user' => $user->id, 'requestId' => $request['id']]) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition flex items-center shadow-sm" onclick="return confirm('Bạn chắc chắn muốn DUYỆT yêu cầu này? Vai trò người dùng sẽ thay đổi ngay lập tức.')">
                                    <i class="fas fa-check-circle mr-2"></i> Đồng ý
                                </button>
                            </form>

                            {{-- Form Từ chối --}}
                            <form action="{{ route('admin.users.reject-role-request', ['user' => $user->id, 'requestId' => $request['id']]) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-white border border-red-200 text-red-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-50 transition flex items-center" onclick="return confirm('Bạn chắc chắn muốn TỪ CHỐI yêu cầu này?')">
                                    <i class="fas fa-times-circle mr-2"></i> Từ chối
                                </button>
                            </form>

                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-history text-3xl mb-3 text-gray-300"></i>
                    <p>Chưa có yêu cầu nào.</p>
                </div>
                @endif
            </div>
            

            @php
                $projects = collect();
                if($user->user_type == 'owner') $projects = $user->ownedProjects->take(5);
                elseif($user->user_type == 'contractor') $projects = $user->contractedProjects->take(5);
                elseif($user->user_type == 'engineer') $projects = $user->engineeredProjects->take(5);
            @endphp

            @if($projects->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800">Dự án gần đây</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($projects as $project)
                    <div class="p-4 hover:bg-gray-50 transition flex justify-between items-center">
                        <div>
                            <a href="{{ route('admin.projects.show', $project) }}" class="font-medium text-blue-600 hover:underline">
                                {{ $project->project_name }}
                            </a>
                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-map-marker-alt mr-1"></i>{{ $project->location ?? 'N/A' }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs bg-gray-100 rounded text-gray-600">
                            {{ $project->status }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection