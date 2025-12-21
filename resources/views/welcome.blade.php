@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildManage - Quản lý dự án xây dựng</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #90a8f6 0%, #3479e7 100%);
        }
        
        .stats-gradient {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }
        
        .feature-gradient {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }
        
        .nav-shadow {
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        }
        
        .card-shadow {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
    </style>
</head>
<body class="bg-gray-50">


    <!-- Hero Section -->
    <section class="hero-gradient text-white">
        <div class="container mx-auto px-4 py-24 md:py-32">
            <div class="max-w-4xl mx-auto text-center">
                <!-- Tiêu đề chính - MÀU TRẮNG TƯƠI SÁNG -->
                <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">
                    Quản lý dự án xây dựng
                    <span class="block text-white font-extrabold mt-2">Chuyên nghiệp & Hiệu quả</span>
                </h1>
                
                <!-- Mô tả - MÀU XANH NHẠT CHO DỄ ĐỌC -->
                <p class="text-xl md:text-2xl text-blue-100 mb-10 max-w-3xl mx-auto font-medium">
                    Giải pháp toàn diện cho việc quản lý dự án, vật tư, tiến độ và nhân sự trong xây dựng
                </p>
                
                <!-- CTA Button - MÀU XANH LÁ NỔI BẬT -->
                <a href="{{ route('register') }}" 
                   class="btn-secondary text-white font-bold py-4 px-10 rounded-lg text-lg inline-flex items-center justify-center space-x-2 hover:shadow-xl transition-all transform hover:-translate-y-1">
                    <i class="fas fa-rocket"></i>
                    <span>Bắt đầu miễn phí</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-gradient py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <!-- Stat 1 - MÀU XANH ĐẬM -->
                <div class="bg-white card-shadow rounded-xl p-8">
                    <div class="text-4xl font-bold text-blue-700 mb-3">500+</div>
                    <div class="text-gray-700 font-semibold">Dự án đã quản lý</div>
                </div>
                
                <!-- Stat 2 - MÀU XANH LÁ ĐẬM -->
                <div class="bg-white card-shadow rounded-xl p-8">
                    <div class="text-4xl font-bold text-green-600 mb-3">98%</div>
                    <div class="text-gray-700 font-semibold">Dự án hoàn thành đúng hạn</div>
                </div>
                
                <!-- Stat 3 - MÀU CAM ĐẬM -->
                <div class="bg-white card-shadow rounded-xl p-8">
                    <div class="text-4xl font-bold text-orange-600 mb-3">1,200+</div>
                    <div class="text-gray-700 font-semibold">Người dùng tin tưởng</div>
                </div>
                
                <!-- Stat 4 - MÀU TÍM ĐẬM -->
                <div class="bg-white card-shadow rounded-xl p-8">
                    <div class="text-4xl font-bold text-purple-600 mb-3">24/7</div>
                    <div class="text-gray-700 font-semibold">Hỗ trợ kỹ thuật</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="feature-gradient py-20">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <!-- Tiêu đề section - MÀU ĐEN ĐẬM -->
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Tính năng nổi bật</h2>
                <p class="text-xl text-gray-700 max-w-2xl mx-auto font-medium">
                    Công cụ quản lý toàn diện cho mọi khía cạnh của dự án xây dựng
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 - QUẢN LÝ DỰ ÁN -->
                <div class="bg-white rounded-xl card-shadow p-8 hover:shadow-2xl transition-all">
                    <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-project-diagram text-blue-700 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Quản lý dự án</h3>
                    <p class="text-gray-700 mb-6">
                        Theo dõi tiến độ, phân công công việc, quản lý ngân sách và kiểm soát toàn bộ dự án từ A đến Z.
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <span class="text-gray-800 font-medium">Giao diện trực quan</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <span class="text-gray-800 font-medium">Báo cáo tự động</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <span class="text-gray-800 font-medium">Cảnh báo thông minh</span>
                        </div>
                    </div>
                </div>
                
                <!-- Feature 2 - QUẢN LÝ VẬT TƯ -->
                <div class="bg-white rounded-xl card-shadow p-8 hover:shadow-2xl transition-all">
                    <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-truck-loading text-green-700 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Quản lý vật tư</h3>
                    <p class="text-gray-700 mb-6">
                        Kiểm soát tồn kho, theo dõi sử dụng vật tư, tối ưu hóa chi phí và tránh lãng phí.
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <span class="text-gray-800 font-medium">Theo dõi tồn kho thời gian thực</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <span class="text-gray-800 font-medium">Cảnh báo vật tư sắp hết</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <span class="text-gray-800 font-medium">Thống kê sử dụng chi tiết</span>
                        </div>
                    </div>
                </div>
                
                <!-- Feature 3 - QUẢN LÝ TIẾN ĐỘ -->
                <div class="bg-white rounded-xl card-shadow p-8 hover:shadow-2xl transition-all">
                    <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-tasks text-purple-700 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Quản lý tiến độ</h3>
                    <p class="text-gray-700 mb-6">
                        Cập nhật tiến độ công việc, đánh giá hiệu suất, đảm bảo dự án hoàn thành đúng hạn.
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <span class="text-gray-800 font-medium">Biểu đồ Gantt trực quan</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <span class="text-gray-800 font-medium">Theo dõi % hoàn thành</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <span class="text-gray-800 font-medium">Cảnh báo chậm tiến độ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Temperature Widget -->
    <section class="py-12 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="bg-white card-shadow rounded-xl p-8 max-w-md mx-auto">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Thời tiết công trường</h3>
                    <i class="fas fa-sun text-yellow-500 text-3xl"></i>
                </div>
                <div class="text-center">
                    <!-- Nhiệt độ lớn, màu đỏ nổi bật -->
                    <div class="text-6xl font-bold text-red-600 mb-4">30°C</div>
                    <div class="text-gray-700 mb-6">
                        <div class="font-semibold text-lg mb-2">Điều kiện làm việc lý tưởng</div>
                        <p class="text-gray-600">Nắng nhẹ, độ ẩm 65%, gió nhẹ 10km/h</p>
                    </div>
                    <!-- Button màu xanh đậm -->
                    <button class="btn-primary text-white font-semibold py-3 px-6 rounded-lg w-full hover:shadow-lg transition-all">
                        <i class="fas fa-cloud-sun mr-2"></i>
                        Cập nhật điều kiện làm việc
                    </button>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Smooth scroll cho các link
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>
@endsection
