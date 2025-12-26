@extends('layouts.app')

@section('title', $project->project_name . ' - Chi tiết dự án')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-start">
        <div>
            <nav class="mb-4">
                <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800">Tên dự án: {{ $project->project_name }}</h1>
            @if($project->location)
                <p class="text-xl text-gray-600 mt-2">Vị trí: {{ $project->location }}</p>
            @endif
        </div>
        @if($project->status !== 'cancelled')
            <div class="flex gap-2">
                <a href="{{ route('admin.projects.edit', $project) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Chỉnh sửa
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Thông báo -->
@include('components.alert')

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Project Information -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-info-circle mr-2"></i>Thông tin dự án
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Tên dự án:</span>
                    <span class="font-medium text-gray-800">{{ $project->project_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Địa điểm:</span>
                    <span class="font-medium text-gray-800">{{ $project->location }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngày bắt đầu:</span>
                    <span class="font-medium text-gray-800">
                        {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') : 'Chưa xác định' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngày kết thúc:</span>
                    <span class="font-medium text-gray-800">
                        {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') : 'Chưa xác định' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngân sách (tổng hợp đồng):</span>
                    <span class="font-medium text-green-600">
                        {{ number_format($project->total_budget) }} VNĐ
                        <span class="text-sm text-gray-500 ml-2">
                            ({{ $project->contracts->count() }} hợp đồng)
                        </span>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Trạng thái:</span>
                    <span class="font-medium text-gray-800">
                        @if($project->status == 'draft') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800">
                                Bản nháp
                            </span>
                        @elseif($project->status == 'pending_contract') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                Chờ hợp đồng
                            </span>
                        @elseif($project->status == 'in_progress') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Đang thi công
                            </span>
                        @elseif($project->status == 'completed') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Hoàn thành
                            </span>
                        @elseif($project->status == 'on_hold') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Tạm dừng
                            </span>
                        @elseif($project->status == 'cancelled') 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Đã hủy
                            </span>
                        @endif
                    </span>
                </div>
                                
                <!-- Statistics Box -->
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Công trường</span>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-bold text-blue-600">{{ $project->sites->count() }}</span>
                        <p class="text-xs text-gray-500">Tổng số công trường</p>
                    </div>
                </div>
            </div>
            
            @if($project->description)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-2">Mô tả:</h4>
                <p class="text-gray-600">{{ $project->description }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Project Team -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-users mr-2"></i>Đội ngũ dự án
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <!-- Owner -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">{{ $project->owner->username }}</div>
                            <div class="text-sm text-gray-500">Chủ đầu tư</div>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ $project->owner->email }}</span>
                </div>

                <!-- Contractor -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-hard-hat"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">{{ $project->contractor->username }}</div>
                            <div class="text-sm text-gray-500">Nhà thầu</div>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ $project->contractor->email }}</span>
                </div>

                <!-- Engineer -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">{{ $project->engineer->username }}</div>
                            <div class="text-sm text-gray-500">Kỹ sư chính</div>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ $project->engineer->email }}</span>
                </div>
            </div>
            
            <!-- Statistics Grid -->
            <div class="mt-6 grid grid-cols-2 gap-4">
                <div class="text-center p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-600">Công trường</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $project->sites->count() }}</p>
                </div>
                <div class="text-center p-3 bg-green-50 rounded-lg">
                    <p class="text-sm text-gray-600">Mốc quan trọng</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{-- {{ $project->milestones->count() }} --}}
                    </p>
                </div>
                <div class="text-center p-3 bg-yellow-50 rounded-lg">
                    <p class="text-sm text-gray-600">Hợp đồng</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $project->contracts->count() }}
                    </p>
                </div>
                <div class="text-center p-3 bg-red-50 rounded-lg">
                    <p class="text-sm text-gray-600">Tài liệu</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $project->documents->count() ?? 0 }}
                    </p>
                </div>
            </div>
            
            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <div class="font-medium text-gray-800">Ngày tạo</div>
                        <div class="text-sm text-gray-500">{{ $project->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <div class="font-medium text-gray-800">Cập nhật lần cuối</div>
                        <div class="text-sm text-gray-500">{{ $project->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>      
</div>

<!-- Danh sách công trường của dự án -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-map-marker-alt mr-2"></i>Danh sách công trường
            <span class="text-sm font-normal text-gray-500 ml-2">({{ $project->sites->count() }} công trường)</span>
        </h2>
        @if($project->status !== 'cancelled')
            @if($project->contracts->count() > 0)
                <a href="{{ route('admin.sites.create', ['project_id' => $project->id]) }}" 
                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-plus mr-2"></i>Thêm công trường
                </a>
            @endif
        @endif
        
    </div>
    
    <div class="p-6">
        @if($project->sites && $project->sites->count() > 0)
            <!-- Sites Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên công trường</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiến độ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Công việc</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($project->sites as $index => $site)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-map-marker-alt text-blue-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('admin.sites.show', $site) }}" class="hover:text-blue-600">
                                                {{ $site->site_name }}
                                            </a>
                                        </div>
                                        @if($site->location)
                                        <div class="text-sm text-gray-500 max-w-xs truncate">
                                            {{ $site->location }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'planned' => 'bg-blue-100 text-blue-800',
                                        'in_progress' => 'bg-green-100 text-green-800',
                                        'completed' => 'bg-gray-100 text-gray-800',
                                        'on_hold' => 'bg-yellow-100 text-yellow-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                    $statusTexts = [
                                        'planned' => 'Lập kế hoạch',
                                        'in_progress' => 'Đang thi công',
                                        'completed' => 'Hoàn thành',
                                        'on_hold' => 'Tạm dừng',
                                        'cancelled' => 'Đã hủy',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$site->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusTexts[$site->status] ?? $site->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        @php
                                            $totalTasks = $site->tasks->count();
                                            $totalProgress = 0;
                                            $siteProgress = 0;
                                            
                                            if($totalTasks > 0) {
                                                foreach($site->tasks as $task) {
                                                    $totalProgress += $task->progress_percent ?? 0;
                                                }
                                                $siteProgress = round($totalProgress / $totalTasks, 1);
                                            }
                                        @endphp
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                             style="width: {{ $siteProgress }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $siteProgress }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-800 font-medium">
                                        {{ $site->tasks->count() }}
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">
                                        @php
                                            $completedTasks = $site->tasks->where('status', 'completed')->count();
                                        @endphp
                                        {{ $completedTasks }} hoàn thành
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>{{ $site->start_date ? $site->start_date->format('d/m/Y') : 'N/A' }}</div>
                                @if($site->end_date)
                                <div class="text-gray-500 text-xs">→ {{ $site->end_date->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.sites.show', $site) }}" 
                                       class="text-blue-600 hover:text-blue-900" 
                                       title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.sites.edit', $site) }}" 
                                       class="text-yellow-600 hover:text-yellow-900" 
                                       title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.sites.destroy', $site) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Bạn có chắc muốn xóa công trường này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900" 
                                                title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
        @else
            <!-- Empty state -->
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-map-marker-alt text-3xl text-gray-400"></i>
                </div>
                @if($project->status !== 'cancelled')
                    @if($project->contracts->count() > 0)
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Chưa có công trường nào</h3>
                        <p class="text-gray-500 mb-6">Dự án chưa được gán công trường</p>
                        <a href="{{ route('admin.sites.create', ['project_id' => $project->id]) }}" 
                            class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-white hover:bg-green-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Tạo công trường đầu tiên
                    </a>
                    @else
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Dự án chưa có hợp đồng</h3>
                        <p class="text-gray-500 mb-6">Không thể thêm công trường vào dự án chưa có hợp đồng</p>
                    @endif
                @else
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Dự án đã bị hủy</h3>
                    <p class="text-gray-500 mb-6">Không thể thêm công trường vào dự án đã hủy</p>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Tabs Section cho Contracts và Documents -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="border-b border-gray-200">
        <nav class="flex -mb-px">
            <button class="tab-button active py-4 px-6 text-sm font-medium border-b-2 border-blue-500 text-blue-600" data-tab="contracts">
                <i class="fas fa-file-contract mr-2"></i>Hợp đồng ({{ $project->contracts->count() }})
            </button>
            <button class="tab-button py-4 px-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="documents">
                <i class="fas fa-file-alt mr-2"></i>Tài liệu ({{ $project->documents->count() ?? 0 }})
            </button>
        </nav>
    </div>

    <div class="p-6">
        <!-- Contracts Tab -->
        <div id="tab-contracts" class="tab-content active">
            @if($project->contracts->count() > 0)
                <div class="space-y-4">
                    @foreach($project->contracts as $contract)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="font-semibold text-lg text-gray-800">Hợp đồng #{{ $contract->id }}</h4>
                                <p class="text-gray-600 mt-1">Nhà thầu: {{ $contract->contractor->username }}</p>
                                <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                    <span>Ký ngày: {{ $contract->signed_date->format('d/m/Y') }}</span>
                                    <span>• Hạn: {{ $contract->due_date->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <div class="text-lg font-bold text-green-600">
                                    {{ number_format($contract->contract_value) }} VNĐ
                                </div>
                                @php
                                    $contractStatusColors = [
                                        'active' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'terminated' => 'bg-red-100 text-red-800',
                                        'expired' => 'bg-yellow-100 text-yellow-800',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $contractStatusColors[$contract->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ \App\Models\Project::getStatuses()[$contract->status] ?? $contract->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-file-contract text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Chưa có hợp đồng nào</h3>
                    <p class="text-gray-500 mb-6">Dự án chưa được thiết lập hợp đồng</p>
                    <a href="{{ route('admin.contracts.create', ['project_id' => $project->id]) }}" 
                       class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-white hover:bg-green-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Tạo hợp đồng
                    </a>
                </div>
            @endif
        </div>

        <!-- Documents Tab -->
        <div id="tab-documents" class="tab-content hidden">
            @if($project->documents && $project->documents->count() > 0)
                <div class="space-y-4">
                    @foreach($project->documents as $document)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-alt text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-semibold text-gray-800">{{ $document->document_name }}</h4>
                                    @if($document->description)
                                    <p class="text-gray-600 text-sm mt-1">{{ $document->description }}</p>
                                    @endif
                                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                        <span>Loại: {{ $document->document_type }}</span>
                                        <span>• Kích thước: {{ round($document->file_size / 1024, 2) }} KB</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($document->file_path)
                                {{-- <a href="{{ route('admin.documents.download', $document) }}"  --}}
                                    <a href="#" 
                                   class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-download mr-1"></i>Tải xuống
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-file-alt text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Chưa có tài liệu nào</h3>
                    <p class="text-gray-500 mb-6">Dự án chưa được tải lên tài liệu</p>
                    <a href="{{ route('admin.documents.create', ['project_id' => $project->id]) }}" 
                       class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-white hover:bg-green-700 transition-colors">
                        <i class="fas fa-upload mr-2"></i>Tải lên tài liệu
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
    <div class="text-sm text-gray-500">
        Tạo ngày: {{ $project->created_at->format('d/m/Y H:i') }}
        • Cập nhật: {{ $project->updated_at->format('d/m/Y H:i') }}
        • Tổng công trường: {{ $project->sites->count() }}
    </div>
    @if($project->status !== 'cancelled')
    <div class="flex gap-2">
        <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 transition-colors"
                    onclick="return confirm('Bạn có chắc chắn muốn xóa dự án này?')">
                <i class="fas fa-trash mr-2"></i>Xóa dự án
            </button>
        </form>
        <a href="{{ route('admin.projects.edit', $project) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
            <i class="fas fa-edit mr-2"></i>Chỉnh sửa
        </a>
        <a href="#" 
        class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-white hover:bg-purple-700 transition-colors">
            <i class="fas fa-file-pdf mr-2"></i>Xuất báo cáo
        </a>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            // Remove active classes from all tabs
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
                content.classList.add('hidden');
            });
            
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Add active class to clicked tab
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('active', 'border-blue-500', 'text-blue-600');
            
            // Show corresponding content
            const target = document.getElementById('tab-' + tabName);
            if (target) {
                target.classList.remove('hidden');
                target.classList.add('active');
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.hover\:bg-gray-50:hover {
    transition: background-color 0.2s ease;
}

.progress-bar {
    transition: width 1s ease-in-out;
}
</style>
@endpush