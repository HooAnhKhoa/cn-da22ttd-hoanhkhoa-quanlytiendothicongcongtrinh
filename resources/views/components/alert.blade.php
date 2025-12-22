@if(session('success'))
<div class="mb-6 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-2xl shadow-lg animate-fade-in">
    <div class="p-5">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center shadow-sm">
                    <i class="fas fa-check text-green-600 text-lg"></i>
                </div>
            </div>
            <div class="flex-1">
                <p class="text-sm font-bold text-green-800">{{ session('success') }}</p>
            </div>
            <button type="button" 
                    class="ml-auto -mx-1.5 -my-1.5 bg-green-100 text-green-600 rounded-xl p-2 hover:bg-green-200 focus:ring-2 focus:ring-green-400 transition-all duration-300 group"
                    onclick="this.parentElement.parentElement.parentElement.remove()">
                <span class="sr-only">Đóng</span>
                <i class="fas fa-times w-3 h-3 group-hover:rotate-90 transition-transform"></i>
            </button>
        </div>
    </div>
</div>
@endif

@if(session('error') || session('danger'))
<div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-2xl shadow-lg animate-fade-in">
    <div class="p-5">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center shadow-sm">
                    <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
                </div>
            </div>
            <div class="flex-1">
                <p class="text-sm font-bold text-red-800">{{ session('error') ?? session('danger') }}</p>
            </div>
            <button type="button" 
                    class="ml-auto -mx-1.5 -my-1.5 bg-red-100 text-red-600 rounded-xl p-2 hover:bg-red-200 focus:ring-2 focus:ring-red-400 transition-all duration-300 group"
                    onclick="this.parentElement.parentElement.parentElement.remove()">
                <span class="sr-only">Đóng</span>
                <i class="fas fa-times w-3 h-3 group-hover:rotate-90 transition-transform"></i>
            </button>
        </div>
    </div>
</div>
@endif

@if(session('warning'))
<div class="mb-6 bg-gradient-to-r from-yellow-50 to-yellow-100 border border-yellow-200 rounded-2xl shadow-lg animate-fade-in">
    <div class="p-5">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center shadow-sm">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-lg"></i>
                </div>
            </div>
            <div class="flex-1">
                <p class="text-sm font-bold text-yellow-800">{{ session('warning') }}</p>
            </div>
            <button type="button" 
                    class="ml-auto -mx-1.5 -my-1.5 bg-yellow-100 text-yellow-600 rounded-xl p-2 hover:bg-yellow-200 focus:ring-2 focus:ring-yellow-400 transition-all duration-300 group"
                    onclick="this.parentElement.parentElement.parentElement.remove()">
                <span class="sr-only">Đóng</span>
                <i class="fas fa-times w-3 h-3 group-hover:rotate-90 transition-transform"></i>
            </button>
        </div>
    </div>
</div>
@endif

@if(session('info'))
<div class="mb-6 bg-gradient-to-r from-indigo-50 to-indigo-100 border border-indigo-200 rounded-2xl shadow-lg animate-fade-in">
    <div class="p-5">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center shadow-sm">
                    <i class="fas fa-info-circle text-indigo-600 text-lg"></i>
                </div>
            </div>
            <div class="flex-1">
                <p class="text-sm font-bold text-indigo-800">{{ session('info') }}</p>
            </div>
            <button type="button" 
                    class="ml-auto -mx-1.5 -my-1.5 bg-indigo-100 text-indigo-600 rounded-xl p-2 hover:bg-indigo-200 focus:ring-2 focus:ring-indigo-400 transition-all duration-300 group"
                    onclick="this.parentElement.parentElement.parentElement.remove()">
                <span class="sr-only">Đóng</span>
                <i class="fas fa-times w-3 h-3 group-hover:rotate-90 transition-transform"></i>
            </button>
        </div>
    </div>
</div>
@endif

@if($errors->any())
<div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-2xl shadow-lg animate-fade-in">
    <div class="p-5">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center shadow-sm">
                    <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-bold text-red-800 mb-2">Có lỗi xảy ra:</h3>
                <ul class="text-sm text-red-700 font-medium space-y-1.5">
                    @foreach($errors->all() as $error)
                        <li class="flex items-start gap-2">
                            <i class="fas fa-circle text-[6px] mt-2 text-red-500"></i>
                            <span>{{ $error }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <button type="button" 
                    class="ml-auto -mx-1.5 -my-1.5 bg-red-100 text-red-600 rounded-xl p-2 hover:bg-red-200 focus:ring-2 focus:ring-red-400 transition-all duration-300 group"
                    onclick="this.parentElement.parentElement.parentElement.remove()">
                <span class="sr-only">Đóng</span>
                <i class="fas fa-times w-3 h-3 group-hover:rotate-90 transition-transform"></i>
            </button>
        </div>
    </div>
</div>
@endif

<style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(-10px) scale(0.98);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    .animate-fade-in {
        animation: fade-in 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>