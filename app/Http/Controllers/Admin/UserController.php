<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = Admin::latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.form', [
            'user' => new Admin(),
            'action' => route('admin.users.store'),
            'method' => 'POST',
            'title' => '新增管理员',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:mm_admin,username'],
            'email' => ['nullable', 'email', 'max:100'],
            'nickname' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:6'],
            'status' => ['required', 'integer'],
        ]);

        Admin::create($data);
        return redirect()->route('admin.users.index')->with('success', '管理员创建成功');
    }

    public function edit(Admin $user)
    {
        return view('admin.users.form', [
            'user' => $user,
            'action' => route('admin.users.update', $user),
            'method' => 'PUT',
            'title' => '编辑管理员',
        ]);
    }

    public function update(Request $request, Admin $user)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:mm_admin,username,'.$user->id],
            'email' => ['nullable', 'email', 'max:100'],
            'nickname' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:6'],
            'status' => ['required', 'integer'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        return redirect()->route('admin.users.index')->with('success', '管理员更新成功');
    }

    public function destroy(Admin $user)
    {
        if ((int) auth('admin')->id() === (int) $user->id) {
            return back()->with('error', '不能删除当前登录账号');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', '管理员删除成功');
    }
}
