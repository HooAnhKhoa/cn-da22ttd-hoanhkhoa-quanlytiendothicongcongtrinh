@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
    
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #1e293b;
    }

    .glass-morphism {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .text-gradient {
        background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-bg {
        background: radial-gradient(circle at top right, #e0e7ff 0%, #ffffff 50%),
                    radial-gradient(circle at bottom left, #f5f3ff 0%, #ffffff 50%);
    }

    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
    }
</style>

<div class="hero-bg min-h-screen">
    <section class="relative pt-20 pb-32 overflow-hidden">
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <span class="inline-block py-1 px-4 rounded-full bg-blue-50 text-blue-600 text-sm font-bold mb-6 tracking-wide uppercase">
                    Platform Xây dựng thế hệ mới 
                </span>
                <h1 class="text-5xl md:text-7xl font-extrabold mb-8 leading-[1.1] tracking-tight text-slate-900">
                    Nâng tầm quản lý <br>
                    <span class="text-gradient">Công trình của bạn</span>
                </h1>
                <p class="text-lg md:text-xl text-slate-600 mb-12 max-w-2xl mx-auto leading-relaxed">
                    Hệ thống quản lý thông minh giúp tối ưu hóa tiến độ, kiểm soát vật tư và kết nối đội ngũ thi công trên một nền tảng duy nhất.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-10 py-4 bg-blue-600 text-white font-bold rounded-2xl shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all transform active:scale-95 text-lg">
                        Bắt đầu ngay miễn phí
                    </a>
                    <a href="#features" class="w-full sm:w-auto px-10 py-4 bg-white text-slate-700 font-bold rounded-2xl border border-slate-200 hover:bg-slate-50 transition-all text-lg flex items-center justify-center">
                        <i class="fas fa-play-circle mr-2 text-blue-600"></i> Xem giới thiệu
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="container mx-auto px-6 -mt-20">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="glass-morphism p-8 rounded-[2rem] shadow-sm flex flex-col items-center justify-center card-hover">
                <div class="text-3xl font-black text-blue-600 mb-1">500+</div>
                <div class="text-slate-500 font-bold text-xs uppercase tracking-widest">Dự án hoàn tất</div>
            </div>
            <div class="glass-morphism p-8 rounded-[2rem] shadow-sm flex flex-col items-center justify-center card-hover border-b-4 border-b-green-400">
                <div class="text-3xl font-black text-green-600 mb-1">99.2%</div>
                <div class="text-slate-500 font-bold text-xs uppercase tracking-widest">Đúng tiến độ</div>
            </div>
            <div class="glass-morphism p-8 rounded-[2rem] shadow-sm flex flex-col items-center justify-center card-hover border-b-4 border-b-purple-400">
                <div class="text-3xl font-black text-purple-600 mb-1">10k+</div>
                <div class="text-slate-500 font-bold text-xs uppercase tracking-widest">Kỹ sư tin dùng</div>
            </div>
            <div class="glass-morphism p-8 rounded-[2rem] shadow-sm flex flex-col items-center justify-center card-hover border-b-4 border-b-orange-400">
                <div class="text-3xl font-black text-orange-600 mb-1">24/7</div>
                <div class="text-slate-500 font-bold text-xs uppercase tracking-widest">Hỗ trợ kỹ thuật</div>
            </div>
        </div>
    </section>

    <section id="features" class="py-32 container mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <div class="max-w-2xl">
                <h2 class="text-4xl font-extrabold text-slate-900 mb-4">Giải pháp toàn diện cho <br>mọi quy mô dự án</h2>
                <p class="text-slate-500 font-medium">Chúng tôi cung cấp mọi công cụ cần thiết để bạn quản lý công trường mà không cần có mặt trực tiếp.</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-hard-hat text-8xl text-slate-100"></i>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-10 rounded-[2.5rem] bg-blue-50/50 border border-blue-100 card-hover group">
                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-8 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500">
                    <i class="fas fa-project-diagram text-xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-slate-800 tracking-tight">Quản lý Dự án</h3>
                <p class="text-slate-600 leading-relaxed font-medium">Lập kế hoạch thi công trực quan, phân quyền chặt chẽ giữa Chủ đầu tư - Nhà thầu - Kỹ sư.</p>
            </div>

            <div class="p-10 rounded-[2.5rem] bg-purple-50/50 border border-purple-100 card-hover group">
                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-8 group-hover:bg-purple-600 group-hover:text-white transition-all duration-500">
                    <i class="fas fa-boxes text-xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-slate-800 tracking-tight">Kho & Vật tư</h3>
                <p class="text-slate-600 leading-relaxed font-medium">Kiểm soát xuất nhập kho theo thời gian thực. Cảnh báo tự động khi vật tư sắp hết hoặc lãng phí.</p>
            </div>

            <div class="p-10 rounded-[2.5rem] bg-orange-50/50 border border-orange-100 card-hover group">
                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-8 group-hover:bg-orange-600 group-hover:text-white transition-all duration-500">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-slate-800 tracking-tight">Báo cáo Tiến độ</h3>
                <p class="text-slate-600 leading-relaxed font-medium">Theo dõi biểu đồ tiến độ thực tế so với kế hoạch. Cập nhật hình ảnh từ công trường mỗi ngày.</p>
            </div>
        </div>
    </section>

    <section class="pb-32 container mx-auto px-6">
        <div class="bg-slate-900 rounded-[3rem] p-12 relative overflow-hidden shadow-2xl">
            <div class="absolute top-0 right-0 p-12 opacity-10">
                <i class="fas fa-cloud-sun-rain text-[15rem] text-white"></i>
            </div>
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-12">
                <div class="text-center md:text-left">
                    <h3 class="text-white text-3xl font-bold mb-4">Tình trạng công trường</h3>
                    <p class="text-slate-400 font-medium mb-8 max-w-md">Theo dõi điều kiện thời tiết thực tế để điều chỉnh kế hoạch thi công phù hợp, đảm bảo an toàn lao động.</p>
                    <div class="inline-flex items-center gap-6 px-8 py-4 bg-white/10 rounded-3xl backdrop-blur-md border border-white/10">
                        <div class="text-5xl font-black text-orange-400">30°C</div>
                        <div class="h-10 w-px bg-white/20"></div>
                        <div class="text-left">
                            <div class="text-white font-bold uppercase text-xs tracking-widest">Thời tiết hiện tại</div>
                            <div class="text-slate-300 font-medium italic">Nắng đẹp - Thích hợp đổ bê tông</div>
                        </div>
                    </div>
                </div>
                <button class="px-8 py-5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-2xl shadow-xl transition-all active:scale-95 flex items-center gap-3">
                    <i class="fas fa-sync-alt"></i> Cập nhật ngay
                </button>
            </div>
        </div>
    </section>
</div>

<footer class="bg-white py-12 border-t border-slate-100">
    <div class="container mx-auto px-6 text-center">
        <div class="flex items-center justify-center mb-8">
            <div class="bg-blue-600 p-2 rounded-lg mr-3">
                <i class="fas fa-hard-hat text-white"></i>
            </div>
            <span class="text-xl font-black text-slate-900 tracking-tight italic">BuildManage</span>
        </div>
        <p class="text-slate-400 font-medium">&copy; 2025 Construction Management System. All rights reserved.</p>
    </div>
</footer>
@endsection