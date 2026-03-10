@extends('admin.layouts.app')

@section('title', '个人中心')

@section('content')
    <div class="top">
        <h1>个人中心</h1>
    </div>

    <div class="card">
        <h3>基本资料</h3>
        <form method="post" action="{{ route('admin.profile.update') }}">
            @csrf
            @method('PUT')
            <label>用户名</label>
            <input value="{{ $admin->username }}" disabled>
            <label>昵称</label>
            <input name="nickname" value="{{ old('nickname', $admin->nickname) }}">
            <label>邮箱</label>
            <input name="email" value="{{ old('email', $admin->email) }}">
            <button class="btn" type="submit">保存资料</button>
        </form>
    </div>

    <div class="card">
        <h3>修改密码</h3>
        <form method="post" action="{{ route('admin.profile.password') }}">
            @csrf
            @method('PUT')
            <label>新密码</label>
            <input type="password" name="password" required>
            <label>确认新密码</label>
            <input type="password" name="password_confirmation" required>
            <button class="btn" type="submit">更新密码</button>
        </form>
    </div>
@endsection
