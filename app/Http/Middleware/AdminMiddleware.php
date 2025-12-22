<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Kiểm tra quyền admin dựa trên user_type trong migration
        if (Auth::user()->user_type === 'admin') {
            return $next($request);
        }

        // 3. Nếu là Client truy cập nhầm trang Admin, đẩy về trang Client
        return redirect()->route('client.dashboard')
            ->with('error', 'Bạn không có quyền truy cập khu vực quản trị.');
    }
}