<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - BuildManage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #eef2ff 0%, #ffffff 50%),
                        radial-gradient(circle at bottom left, #f5f3ff 0%, #ffffff 50%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .input-group:focus-within .input-icon {
            color: #4f46e5;
            transform: scale(1.1);
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="max-w-[640px] w-full">
        <div class="text-center mb-10">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-4 group">
                <div class="w-12 h-12 bg-indigo-600 rounded-xl shadow-2xl shadow-indigo-200 flex items-center justify-center transform group-hover:rotate-6 transition-all duration-300">
                    <i class="fas fa-hard-hat text-xl text-white"></i>
                </div>
                <div class="text-left">
                    <h1 class="text-xl font-black text-slate-900 leading-none tracking-tighter">BuildManage</h1>
                    <p class="text-[9px] font-bold text-indigo-500 uppercase tracking-[0.2em] mt-1">Join the Platform</p>
                </div>
            </a>
        </div>

        <div class="glass-card rounded-[3rem] shadow-2xl shadow-indigo-100/50 overflow-hidden border border-white">
            <div class="p-8 sm:p-12">
                <div class="mb-10 text-center sm:text-left">
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tạo tài khoản mới</h2>
                    <p class="text-slate-500 font-medium mt-2">Bắt đầu quản lý công trình chuyên nghiệp ngay hôm nay</p>
                </div>

                @if($errors->any())
                    <div class="bg-red-50/50 border border-red-100 rounded-2xl p-5 mb-8 flex items-start gap-3">
                        <i class="fas fa-circle-exclamation text-red-500 mt-1"></i>
                        <ul class="text-xs text-red-700 font-bold space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                    @csrf
                    
                    <div class="input-group sm:col-span-2">
                        <label for="username" class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] mb-2.5 ml-1">Tên đăng nhập</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-all duration-300 input-icon text-slate-400">
                                <i class="fas fa-user text-sm"></i>
                            </div>
                            <input type="text" id="username" name="username" required 
                                class="w-full pl-12 pr-5 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-50 focus:border-indigo-500 transition-all text-slate-800 font-semibold placeholder-slate-300"
                                value="{{ old('username') }}" placeholder="Ví dụ: hoanhkhoa_cms">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="email" class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] mb-2.5 ml-1">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-all duration-300 input-icon text-slate-400">
                                <i class="fas fa-envelope text-sm"></i>
                            </div>
                            <input type="email" id="email" name="email" required 
                                class="w-full pl-12 pr-5 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-50 focus:border-indigo-500 transition-all text-slate-800 font-semibold placeholder-slate-300"
                                value="{{ old('email') }}" placeholder="email@congty.com">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="phone" class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] mb-2.5 ml-1">Số điện thoại</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-all duration-300 input-icon text-slate-400">
                                <i class="fas fa-phone text-sm"></i>
                            </div>
                            <input type="text" id="phone" name="phone" 
                                class="w-full pl-12 pr-5 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-50 focus:border-indigo-500 transition-all text-slate-800 font-semibold placeholder-slate-300"
                                value="{{ old('phone') }}" placeholder="09xx xxx xxx">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="password" class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] mb-2.5 ml-1">Mật khẩu</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-all duration-300 input-icon text-slate-400">
                                <i class="fas fa-lock text-sm"></i>
                            </div>
                            <input type="password" id="password" name="password" required
                                class="w-full pl-12 pr-5 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-50 focus:border-indigo-500 transition-all text-slate-800 font-semibold placeholder-slate-300"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="password_confirmation" class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] mb-2.5 ml-1">Xác nhận</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-all duration-300 input-icon text-slate-400">
                                <i class="fas fa-shield-alt text-sm"></i>
                            </div>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="w-full pl-12 pr-5 py-4 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-indigo-50 focus:border-indigo-500 transition-all text-slate-800 font-semibold placeholder-slate-300"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div class="sm:col-span-2 pt-6">
                        <button type="submit" 
                            class="w-full btn-gradient text-white py-5 px-6 rounded-2xl font-black text-sm uppercase tracking-widest hover:shadow-2xl hover:shadow-indigo-200 active:scale-[0.98] transition-all duration-300 flex items-center justify-center group gap-3">
                            <span>Hoàn tất đăng ký</span>
                            <i class="fas fa-chevron-right text-[10px] transition-transform group-hover:translate-x-1"></i>
                        </button>
                    </div>
                </form>

                <div class="mt-12 pt-8 border-t border-slate-100/60 text-center">
                    <p class="text-slate-500 text-sm font-medium">
                        Đã có tài khoản thành viên? 
                        <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-black ml-1 transition underline decoration-2 underline-offset-8">Đăng nhập ngay</a>
                    </p>
                </div>
            </div>
        </div>

        <p class="mt-12 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
            &copy; {{ date('Y') }} BuildManage &bull; Industrial Solutions
        </p>
    </div>
</body>
</html>