<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Trang hồ sơ cá nhân
     */
    public function index()
    {
        return view('profile');
    }

    /**
     * Cập nhật thông tin cơ bản
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'username' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('users', 'username')->ignore($user->id),
                ],
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($user->id),
                ],
                'phone' => 'nullable|string|max:20',
                // 'address' và 'bio' có thể thêm nếu bảng users có cột này
            ]);

            $user->username = $request->username;
            $user->email = $request->email;
            if ($request->has('phone')) $user->phone = $request->phone;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật hồ sơ thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Đổi mật khẩu (ĐÃ SỬA LẠI ĐỂ TRẢ VỀ JSON VÀ FIX LỖI 500)
     */
    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|string|min:6|confirmed',
            ]);

            $user = Auth::user();

            // Kiểm tra pass cũ
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu hiện tại không chính xác'
                ], 422); // 422 là mã lỗi dữ liệu không hợp lệ
            }

            // Cập nhật pass mới (Dùng cách này tránh lỗi fillable)
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Đổi mật khẩu thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi Server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật avatar
     */
    public function updateAvatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            $user = Auth::user();

            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars/' . $user->id, 'public');

            $user->avatar = $path;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật ảnh đại diện thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi upload: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gửi yêu cầu đổi vai trò
     */

    public function requestRoleChange(Request $request)
    {
        try {
            $request->validate([
                'requested_role' => 'required|in:owner,contractor,engineer,admin',
                'reason' => 'required|min:10|max:1000',
            ]);
        
            $user = Auth::user();
        
            if ($user->hasPendingRoleRequest()) {
                return response()->json(['success' => false, 'message' => 'Bạn đã có yêu cầu đang chờ duyệt'], 400);
            }
        
            // --- SỬA ĐỔI: Tạo record mới vào bảng riêng ---
            $user->roleChangeRequests()->create([
                'requested_role' => $request->requested_role,
                'reason' => $request->reason,
                'status' => 'pending'
            ]);
        
            return response()->json([
                'success' => true,
                'message' => 'Yêu cầu đổi vai trò đã được gửi'
            ]);
        
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }
}