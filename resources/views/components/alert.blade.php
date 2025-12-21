@if(session('success'))
<div class="mb-6 bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 rounded-lg shadow-sm animate-fade-in">
    <div class="p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-green-600"></i>
                </div>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
            <button type="button" 
                    class="ml-auto -mx-1.5 -my-1.5 bg-green-100 text-green-500 rounded-lg p-1.5 hover:bg-green-200 focus:ring-2 focus:ring-green-400 transition-colors"
                    onclick="this.parentElement.parentElement.parentElement.remove()">
                <span class="sr-only">Đóng</span>
                <i class="fas fa-times w-3 h-3"></i>
            </button>
        </div>
    </div>
</div>
@endif

@if(session('error') || session('danger'))
<div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 rounded-lg shadow-sm animate-fade-in">
    <div class="p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-red-600"></i>
                </div>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium text-red-800">{{ session('error') ?? session('danger') }}</p>
            </div>
            <button type="button" 
                    class="ml-auto -mx-1.5 -my-1.5 bg-red-100 text-red-500 rounded-lg p-1.5 hover:bg-red-200 focus:ring-2 focus:ring-red-400 transition-colors"
                    onclick="this.parentElement.parentElement.parentElement.remove()">
                <span class="sr-only">Đóng</span>
                <i class="fas fa-times w-3 h-3"></i>
            </button>
        </div>
    </div>
</div>
@endif

@if(session('warning'))
<div class="mb-6 bg-gradient-to-r from-yellow-50 to-yellow-100 border-l-4 border-yellow-500 rounded-lg shadow-sm animate-fade-in">
    <div class="p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                </div>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium text-yellow-800">{{ session('warning') }}</p>
            </div>
            <button type="button" 
                    class="ml-auto -mx-1.5 -my-1.5 bg-yellow-100 text-yellow-500 rounded-lg p-1.5 hover:bg-yellow-200 focus:ring-2 focus:ring-yellow-400 transition-colors"
                    onclick="this.parentElement.parentElement.parentElement.remove()">
                <span class="sr-only">Đóng</span>
                <i class="fas fa-times w-3 h-3"></i>
            </button>
        </div>
    </div>
</div>
@endif

@if(session('info'))
<div class="mb-6 bg-gradient-to-r from-blue-50 to-blue-100 border-l-4 border-blue-500 rounded-lg shadow-sm animate-fade-in">
    <div class="p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium text-blue-800">{{ session('info') }}</p>
            </div>
            <button type="button" 
                    class="ml-auto -mx-1.5 -my-1.5 bg-blue-100 text-blue-500 rounded-lg p-1.5 hover:bg-blue-200 focus:ring-2 focus:ring-blue-400 transition-colors"
                    onclick="this.parentElement.parentElement.parentElement.remove()">
                <span class="sr-only">Đóng</span>
                <i class="fas fa-times w-3 h-3"></i>
            </button>
        </div>
    </div>
</div>
@endif

@if($errors->any())
<div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 rounded-lg shadow-sm animate-fade-in">
    <div class="p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-semibold text-red-800 mb-2">Có lỗi xảy ra:</h3>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" 
                    class="ml-auto -mx-1.5 -my-1.5 bg-red-100 text-red-500 rounded-lg p-1.5 hover:bg-red-200 focus:ring-2 focus:ring-red-400 transition-colors"
                    onclick="this.parentElement.parentElement.parentElement.remove()">
                <span class="sr-only">Đóng</span>
                <i class="fas fa-times w-3 h-3"></i>
            </button>
        </div>
    </div>
</div>
@endif

<style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }
</style>