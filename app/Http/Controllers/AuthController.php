<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Điều hướng "sát gốc" theo vai trò thực tế trong database
            if ($user->user_type === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            } 
            
            // Các vai trò owner, contractor, engineer đều dùng chung Dashboard Client
            $clientRoles = ['owner', 'contractor', 'engineer'];
            if (in_array($user->user_type, $clientRoles)) {
                return redirect()->intended(route('client.dashboard'));
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ]);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255', // Đổi name thành username cho khớp database
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        // Tạo user với user_type mặc định là 'owner' (hoặc contractor tùy ý bạn)
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'user_type' => 'owner', // Vai trò mặc định cho người đăng ký mới
            'password' => Hash::make($request->password),
            'status' => 'active'
        ]);

        Auth::login($user);

        return redirect()->route('client.dashboard')
            ->with('success', 'Đăng ký thành công! Chào mừng bạn đến với hệ thống.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login'); // Đưa về trang login thay vì trang chủ
    }
}