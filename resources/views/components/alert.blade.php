@if(session('success'))
<div class="alert bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
    <div class="flex justify-between items-center">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" class="text-green-700 hover:text-green-900" onclick="this.parentElement.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('error'))
<div class="alert bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
    <div class="flex justify-between items-center">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" class="text-red-700 hover:text-red-900" onclick="this.parentElement.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if($errors->any())
<div class="alert bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
    <div class="flex justify-between items-center">
        <div>
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span class="font-semibold">Có lỗi xảy ra:</span>
            </div>
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="text-red-700 hover:text-red-900" onclick="this.parentElement.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif