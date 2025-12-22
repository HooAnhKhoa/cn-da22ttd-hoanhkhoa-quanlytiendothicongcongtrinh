<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Kiểm tra xem người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. Kiểm tra vai trò người dùng (Tất cả trừ 'admin' được coi là Client/Đối tác)
        // Dựa trên Migration: 'owner', 'contractor', 'engineer' là các vai trò client
        $clientRoles = ['owner', 'contractor', 'engineer'];

        if (in_array($user->user_type, $clientRoles)) {
            return $next($request);
        }

        // 3. Nếu là Admin truy cập nhầm trang Client, có thể cho phép hoặc chuyển hướng về Admin Dashboard
        if ($user->user_type === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Bạn đang dùng tài khoản Admin, vui lòng truy cập trang quản trị.');
        }

        // 4. Các trường hợp còn lại (không hợp lệ)
        Auth::logout();
        return redirect()->route('login')->with('error', 'Tài khoản của bạn không có quyền truy cập khu vực này.');
    }
}