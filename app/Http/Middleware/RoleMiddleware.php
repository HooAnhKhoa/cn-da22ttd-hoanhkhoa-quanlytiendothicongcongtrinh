<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Xử lý kiểm tra vai trò cụ thể dựa trên user_type.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Kiểm tra xem user_type của người dùng có nằm trong danh sách các role được phép không
        if (in_array($user->user_type, $roles)) {
            return $next($request);
        }

        // Nếu không có quyền, trả về lỗi 403 hoặc chuyển hướng
        abort(403, 'Bạn không có quyền truy cập khu vực này.');
    }
}