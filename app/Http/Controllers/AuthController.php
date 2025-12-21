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
            
            // Điều hướng theo user_type của người dùng
            if ($user->user_type === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->user_type === 'client') {
                return redirect()->intended(route('client.dashboard'));
            } else {
                // Nếu có các user_type khác, thêm điều hướng ở đây
                return redirect()->intended('/');
            }
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        // Tạo user với user_type mặc định là 'client'
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'user_type' => 'client', // Mặc định là client
            'password' => Hash::make($request->password),
            'status' => 'active'
        ]);

        // Đăng nhập tự động sau khi đăng ký
        Auth::login($user);

        return redirect()->route('client.dashboard')
            ->with('success', 'Đăng ký thành công! Chào mừng đến với BuildManage.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}