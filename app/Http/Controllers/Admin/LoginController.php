<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * 显示登录页面
     */
    public function showLoginForm()
    {
        // 如果已登录，重定向到后台首页
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.login');
    }
    
    /**
     * 处理登录请求
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        
        $remember = $request->filled('remember');
        
        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // 更新最后登录信息
            $admin = Auth::guard('admin')->user();
            $admin->update([
                'last_login_ip' => $request->ip(),
                'last_login_time' => now(),
            ]);
            
            return response()->json([
                'code' => 1,
                'msg' => '登录成功',
                'data' => [
                    'username' => $admin->username,
                    'nickname' => $admin->nickname,
                ],
                'redirect' => route('admin.dashboard')
            ]);
        }
        
        return response()->json([
            'code' => 0,
            'msg' => '用户名或密码错误'
        ], 401);
    }
    
    /**
     * 退出登录
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }
}
