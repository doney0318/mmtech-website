<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('admin.profile.edit', [
            'admin' => auth('admin')->user(),
        ]);
    }

    public function update(Request $request)
    {
        $admin = auth('admin')->user();

        $data = $request->validate([
            'email' => ['nullable', 'email', 'max:100'],
            'nickname' => ['nullable', 'string', 'max:50'],
        ]);

        $admin->update($data);
        return back()->with('success', '资料更新成功');
    }

    public function updatePassword(Request $request)
    {
        $admin = auth('admin')->user();

        $data = $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $admin->update(['password' => $data['password']]);
        return back()->with('success', '密码修改成功');
    }
}
