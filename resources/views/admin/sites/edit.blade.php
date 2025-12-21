@extends('layouts.app')

@section('title', 'Chỉnh sửa công trường - ')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-start">
        <div>
            <nav class="mb-4">
                <a href="{{ route('admin.sites.show', $site) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800">Chỉnh sửa công trường</h1>
            <p class="text-xl text-gray-600 mt-2">{{ $site->site_name }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-edit mr-2"></i>Thông tin công trường
        </h2>
    </div>
    
    <div class="p-6">
        <form action="{{ route('admin.sites.update', $site) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tên công trường -->
                <div>
                    <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Tên công trường <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="site_name" 
                           id="site_name"
                           value="{{ old('site_name', $site->site_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('site_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dự án -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Dự án <span class="text-red-500">*</span>
                    </label>
                    <select name="project_id" 
                            id="project_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            required>
                        <option value="">-- Chọn dự án --</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $site->project_id) == $project->id ? 'selected' : '' }}>
                                {{ $project->project_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ngày bắt đầu -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Ngày bắt đầu <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="start_date" 
                           id="start_date"
                           value="{{ old('start_date', $site->start_date->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ngày kết thúc -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Ngày kết thúc
                    </label>
                    <input type="date" 
                           name="end_date" 
                           id="end_date"
                           value="{{ old('end_date', $site->end_date ? $site->end_date->format('Y-m-d') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tiến độ -->
                <div>
                    <label for="progress_percent" class="block text-sm font-medium text-gray-700 mb-2">
                        Tiến độ (%) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="progress_percent" 
                           id="progress_percent"
                           value="{{ old('progress_percent', $site->progress_percent) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           required
                           min="0"
                           max="100"
                           step="1"
                           readonly
                           >
                    @error('progress_percent')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Trạng thái -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Trạng thái <span class="text-red-500">*</span>
                    </label>
                    <select name="status" 
                            id="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            required>
                        @foreach(\App\Models\Admin\Site::getStatuses() as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $site->status) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Mô tả -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Mô tả công trường
                </label>
                <textarea name="description" 
                          id="description"
                          rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('description', $site->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- <!-- Đội ngũ công trường -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-users mr-2"></i>Đội ngũ công trường
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Kỹ sư -->
                    <div>
                        <label for="engineer_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Kỹ sư
                        </label>
                        <select name="engineer_id" 
                                id="engineer_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Chọn kỹ sư --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('engineer_id', $site->engineer_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->username }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('engineer_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nhà thầu -->
                    <div>
                        <label for="contractor_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Nhà thầu
                        </label>
                        <select name="contractor_id" 
                                id="contractor_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Chọn nhà thầu --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('contractor_id', $site->contractor_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->username }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('contractor_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Giám sát -->
                    <div>
                        <label for="supervisor_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Giám sát
                        </label>
                        <select name="supervisor_id" 
                                id="supervisor_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Chọn giám sát --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('supervisor_id', $site->supervisor_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->username }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('supervisor_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div> --}}

            <!-- Nút hành động -->
            <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.sites.show', $site) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-400 transition-colors">
                    <i class="fas fa-times mr-2"></i>Hủy bỏ
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Cập nhật công trường
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Form xóa công trường -->
<div class="mt-6 bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-red-600">
            <i class="fas fa-exclamation-triangle mr-2"></i>Vùng nguy hiểm
        </h2>
    </div>
    <div class="p-6">
        <p class="text-gray-600 mb-4">Khi xóa công trường, tất cả dữ liệu liên quan sẽ bị mất vĩnh viễn. Hành động này không thể hoàn tác.</p>
        <form action="{{ route('admin.sites.destroy', $site) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa công trường này?')">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 transition-colors">
                <i class="fas fa-trash mr-2"></i>Xóa công trường
            </button>
        </form>
    </div>
</div>
@endsection