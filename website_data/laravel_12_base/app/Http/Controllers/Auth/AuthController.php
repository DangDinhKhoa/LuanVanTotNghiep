<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        @session_start();
    }

    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handel login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::attempt($credentials)) {
            // Kiểm tra trạng thái của người dùng
            $user = Auth::user();
            if ($user->status != 1) {
                // Nếu status không phải 1, đăng xuất ngay lập tức
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
                ])->withInput();
            }
            if($user->level == 0){
                // Đăng nhập người dùng
                $request->session()->regenerate();
                return redirect()->intended('/user/layout');
            }
            else if($user->level == 1){
                // Đăng nhập admin
                $request->session()->regenerate();
                return redirect()->intended('/admin/layout');
            }
        }
        // Nếu thông tin đăng nhập không hợp lệ
        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ])->withInput();
    }

    // Show register form
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Handle register
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|max:255|confirmed',
        ]);
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        Auth::login($user);
        return redirect('/admin/layout');
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
