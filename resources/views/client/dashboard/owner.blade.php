{{-- resources/views/client/dashboard/owner.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Bảng Điều Khiển Chủ Đầu Tư</h1>
        <p class="text-gray-500 mt-2 font-medium">Chào mừng trở lại, {{ auth()->user()->name }}!</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-4 bg-blue-100 rounded-2xl text-blue-600 mr-4">
                    <i class="fas fa-project-diagram text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Tổng dự án</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $stats['total_projects'] }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-4 bg-green-100 rounded-2xl text-green-600 mr-4">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Tổng đầu tư</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_investment']) }}đ</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-4 bg-purple-100 rounded-2xl text-purple-600 mr-4">
                    <i class="fas fa-file-contract text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Hợp đồng</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $contractStats['total_contracts'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-4 bg-yellow-100 rounded-2xl text-yellow-600 mr-4">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Hoàn thành</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $stats['completed_projects'] }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-800">Dự án gần đây</h2>
                    <a href="{{ route('client.owner.projects.index') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800">Xem tất cả</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tên dự án</th>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Ngày bắt đầu</th>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentProjects as $project)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-4">
                                    <div class="font-bold text-gray-800">{{ $project->project_name }}</div>
                                    <div class="text-xs text-gray-400">{{ $project->location }}</div>
                                </td>
                                <td class="p-4 text-sm text-gray-600">
                                    {{ $project->start_date->format('d/m/Y') }}
                                </td>
                                <td class="p-4">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full 
                                        @if($project->status == 'in_progress') bg-blue-50 text-blue-600 
                                        @elseif($project->status == 'completed') bg-green-50 text-green-600
                                        @else bg-gray-50 text-gray-600 @endif">
                                        {{ $project->status }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <a href="{{ route('client.owner.projects.show', $project->id) }}" class="text-blue-600 hover:underline font-bold text-sm">Chi tiết</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-6">Tổng quan tài chính</h2>
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <p class="text-sm font-medium text-gray-500 mb-1">Tổng ngân sách</p>
                            <p class="text-2xl font-bold text-gray-800">{{ number_format($financialStats['total_budget']) }}đ</p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-xl">
                            <p class="text-sm font-medium text-blue-600 mb-1">Tổng hợp đồng</p>
                            <p class="text-2xl font-bold text-blue-700">{{ number_format($financialStats['total_contracts']) }}đ</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-xl">
                            <p class="text-sm font-medium text-green-600 mb-1">Đã thanh toán</p>
                            <p class="text-2xl font-bold text-green-700">{{ number_format($financialStats['total_paid']) }}đ</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-xl">
                            <p class="text-sm font-medium text-yellow-600 mb-1">Còn lại</p>
                            <p class="text-2xl font-bold text-yellow-700">{{ number_format($financialStats['remaining']) }}đ</p>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="font-medium text-gray-700">Tỷ lệ sử dụng ngân sách</span>
                            <span class="font-bold text-blue-600">{{ $financialStats['utilization_percent'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $financialStats['utilization_percent'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="space-y-8">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-6">Thống kê hợp đồng</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Tổng hợp đồng</span>
                        <span class="font-bold text-gray-800">{{ $contractStats['total_contracts'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Đang hoạt động</span>
                        <span class="font-bold text-green-600">{{ $contractStats['active_contracts'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Đã hoàn thành</span>
                        <span class="font-bold text-blue-600">{{ $contractStats['completed_contracts'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Chờ ký</span>
                        <span class="font-bold text-yellow-600">{{ $contractStats['pending_contracts'] }}</span>
                    </div>
                    <div class="pt-4 border-t border-gray-100">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-600">Tổng giá trị</span>
                            <span class="font-bold text-purple-600">{{ number_format($contractStats['contract_value']) }}đ</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50">
                    <h2 class="text-lg font-bold text-gray-800">Thanh toán sắp tới</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($upcomingPayments as $payment)
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $payment['contract'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $payment['project'] }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-bold rounded-full 
                                    @if($payment['days_left'] < 0) bg-red-100 text-red-600
                                    @elseif($payment['days_left'] < 7) bg-yellow-100 text-yellow-600
                                    @else bg-green-100 text-green-600 @endif">
                                    @if($payment['days_left'] < 0)
                                        Quá hạn {{ abs($payment['days_left']) }} ngày
                                    @elseif($payment['days_left'] == 0)
                                        Hôm nay
                                    @else
                                        Còn {{ $payment['days_left'] }} ngày
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">{{ $payment['contractor'] }}</span>
                                <span class="font-bold text-gray-800">{{ number_format($payment['amount']) }}đ</span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-gray-500">
                            <p>Không có thanh toán sắp tới</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection