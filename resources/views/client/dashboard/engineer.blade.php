{{-- resources/views/client/dashboard/engineer.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Bảng Điều Khiển Kỹ Sư</h1>
        <p class="text-gray-500 mt-2 font-medium">Chào mừng trở lại, {{ auth()->user()->name }}!</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-4 bg-blue-100 rounded-2xl text-blue-600 mr-4">
                    <i class="fas fa-tasks text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Tổng công việc</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $stats['total_tasks'] }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-4 bg-green-100 rounded-2xl text-green-600 mr-4">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Đã hoàn thành</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $stats['completed_tasks'] }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-4 bg-red-100 rounded-2xl text-red-600 mr-4">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Trễ hạn</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $stats['overdue_tasks'] }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-4 bg-yellow-100 rounded-2xl text-yellow-600 mr-4">
                    <i class="fas fa-spinner text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Đang thực hiện</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $stats['in_progress_tasks'] }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Nội dung engineer dashboard --}}
    @include('client.dashboard.partials.engineer-content')
</div>
@endsection