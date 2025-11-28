@extends('layouts.app')

@section('title', $project->project_name . ' - Chi tiết dự án')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-start">
        <div>
            <nav class="mb-4">
                <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
            </nav>
            <h1 class="text-3xl font-bold text-gray-800">Tên dự án: {{ $project->project_name }}</h1>
            <p class="text-xl text-gray-600 mt-2">Vị trí: {{ $project->location }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('projects.edit', $project) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
                <i class="fas fa-edit mr-2"></i>Chỉnh sửa
            </a>
        </div>
    </div>
</div>

<!-- Project Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-map-marker-alt text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-2xl font-semibold text-gray-900">{{ $project->sites->count() }}</p>
                <p class="text-sm font-medium text-gray-600">Công trường</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-flag-checkered text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-2xl font-semibold text-gray-900">{{ $project->milestones->count() }}</p>
                <p class="text-sm font-medium text-gray-600">Mốc quan trọng</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-file-contract text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-2xl font-semibold text-gray-900">{{ $project->contracts->count() }}</p>
                <p class="text-sm font-medium text-gray-600">Hợp đồng</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                <i class="fas fa-info-circle text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Trạng thái</p>
                <p class="text-lg font-semibold">
                    <span class="inline-block px-2 py-1 text-xs rounded-full 
                        @if($project->status == 'in_progress') bg-green-100 text-green-800
                        @elseif($project->status == 'completed') bg-blue-100 text-blue-800
                        @elseif($project->status == 'on_hold') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ \App\Models\Project::getStatuses()[$project->status] ?? $project->status }}
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

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
                    <span class="font-medium text-gray-800">{{ $project->start_date }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngày kết thúc:</span>
                    <span class="font-medium text-gray-800">
                        {{ $project->end_date ? $project->end_date : 'Chưa xác định' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngân sách:</span>
                    <span class="font-medium text-green-600">{{ number_format($project->total_budget) }} VNĐ</span>
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
        </div>
    </div>
</div>

<!-- Tabs Section -->
<div class="bg-white rounded-lg shadow mb-8">
     <div class="border-b border-gray-200">
        <nav class="flex -mb-px">
            <button class="tab-button active py-4 px-6 text-sm font-medium border-b-2 border-blue-500 text-blue-600" data-tab="sites">
                <i class="fas fa-map-marker-alt mr-2"></i>Công trường ({{ $project->sites->count() }})
            </button>
            <button class="tab-button py-4 px-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="milestones">
                <i class="fas fa-flag-checkered mr-2"></i>Mốc quan trọng ({{ $project->milestones->count() }})
            </button>
            <button class="tab-button py-4 px-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="contracts">
                <i class="fas fa-file-contract mr-2"></i>Hợp đồng ({{ $project->contracts->count() }})
            </button>
            <button class="tab-button py-4 px-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="documents">
                <i class="fas fa-file-alt mr-2"></i>Tài liệu
            </button>
        </nav>
    </div>

    <div class="p-6">
        <!-- Sites Tab -->
        <div id="tab-sites" class="tab-content active">
            @if($project->sites->count() > 0)
                <div class="space-y-4">
                    @foreach($project->sites as $site)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-lg text-gray-800">{{ $site->site_name }}</h4>
                                <p class="text-gray-600 mt-1">{{ $site->description }}</p>
                                <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                    <span>{{ $site->start_date }}</span>
                                    <span>→</span>
                                    <span>{{ $site->end_date ? $site->end_date : 'Đang thực hiện' }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-2 py-1 text-xs rounded-full 
                                    @if($site->status == 'completed') bg-green-100 text-green-800
                                    @elseif($site->status == 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($site->status == 'on_hold') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $site->status }}
                                </span>
                                <div class="mt-2">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: {{ $site->progress_percent }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 mt-1 block">{{ $site->progress_percent }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-map-marker-alt text-4xl mb-4 text-gray-300"></i>
                    <p class="mb-4">Chưa có công trường nào</p>
                    <a href="{{ route('sites.create', ['project_id' => $project->id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Thêm công trường
                    </a>
                </div>
            @endif
        </div>

        <!-- Milestones Tab -->
        <div id="tab-milestones" class="tab-content hidden">
            @if($project->milestones->count() > 0)
                <div class="space-y-4">
                    @foreach($project->milestones as $milestone)
                    <div class="border-l-4 border-blue-500 pl-4 py-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $milestone->milestone_name }}</h4>
                                <p class="text-gray-600 text-sm mt-1">{{ $milestone->description }}</p>
                                <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                    <span>Mục tiêu: {{ $milestone->target_date->format('d/m/Y') }}</span>
                                    @if($milestone->completed_date)
                                        <span>• Hoàn thành: {{ $milestone->completed_date->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                            </div>
                            <span class="inline-block px-2 py-1 text-xs rounded-full 
                                @if($milestone->status == 'completed') bg-green-100 text-green-800
                                @elseif($milestone->status == 'in_progress') bg-blue-100 text-blue-800
                                @elseif($milestone->status == 'on_hold') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $milestone->status }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-flag-checkered text-4xl mb-4 text-gray-300"></i>
                    <p>Chưa có mốc quan trọng nào</p>
                </div>
            @endif
        </div>

        <!-- Contracts Tab -->
        <div id="tab-contracts" class="tab-content hidden">
            @if($project->contracts->count() > 0)
                <div class="space-y-4">
                    @foreach($project->contracts as $contract)
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-gray-800">Hợp đồng #{{ $contract->id }}</h4>
                                <p class="text-gray-600 mt-1">Nhà thầu: {{ $contract->contractor->username }}</p>
                                <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                    <span>Ký ngày: {{ $contract->signed_date->format('d/m/Y') }}</span>
                                    <span>• Hạn: {{ $contract->due_date->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-green-600">
                                    {{ number_format($contract->contract_value) }} VNĐ
                                </div>
                                <span class="inline-block px-2 py-1 text-xs rounded-full 
                                    @if($contract->status == 'completed') bg-green-100 text-green-800
                                    @elseif($contract->status == 'active') bg-blue-100 text-blue-800
                                    @elseif($contract->status == 'terminated') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $contract->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-file-contract text-4xl mb-4 text-gray-300"></i>
                    <p>Chưa có hợp đồng nào</p>
                </div>
            @endif
        </div>

        <!-- Documents Tab -->
        <div id="tab-documents" class="tab-content hidden">
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-file-alt text-4xl mb-4 text-gray-300"></i>
                <p class="mb-4">Chưa có tài liệu nào</p>
                <a href="{{ route('documents.create', ['project_id' => $project->id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
                    <i class="fas fa-upload mr-2"></i>Tải lên tài liệu
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
    <div class="text-sm text-gray-500">
        Tạo ngày: {{ $project->created_at->format('d/m/Y H:i') }}
        • Cập nhật: {{ $project->updated_at->format('d/m/Y H:i') }}
    </div>
    <div class="flex gap-2">
        <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 transition-colors"
                    onclick="return confirm('Bạn có chắc chắn muốn xóa dự án này?')">
                <i class="fas fa-trash mr-2"></i>Xóa dự án
            </button>
        </form>
        <a href="{{ route('projects.edit', $project) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition-colors">
            <i class="fas fa-edit mr-2"></i>Chỉnh sửa
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>

// Test if elements exist
document.addEventListener('DOMContentLoaded', function() {
    
    const tabButtons = document.querySelectorAll('.tab-button');
    
    tabButtons.forEach((button, index) => {
        
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            // Simple tab switching
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600');
            });
            
            this.classList.add('border-blue-500', 'text-blue-600');
            const target = document.getElementById('tab-' + tabName);
            if (target) {
                target.style.display = 'block';
            }
        });
    });
});
</script>
@endpush