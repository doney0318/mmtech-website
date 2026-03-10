@extends('admin.layouts.app')

@section('title', $title)

@section('content')
    <div class="top">
        <h1>{{ $title }}</h1>
        <a class="btn" href="{{ route('admin.users.index') }}">返回列表</a>
    </div>

    <div class="card">
        <form method="post" action="{{ $action }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <label>用户名</label>
            <input name="username" value="{{ old('username', $user->username) }}" required>

            <label>昵称</label>
            <input name="nickname" value="{{ old('nickname', $user->nickname) }}">

            <label>邮箱</label>
            <input name="email" value="{{ old('email', $user->email) }}">

            <label>密码 @if($method !== 'POST')（留空则不修改）@endif</label>
            <input type="password" name="password">

            <label>状态</label>
            <select name="status">
                <option value="1" @selected((string) old('status', $user->status ?? 1) === '1')>启用</option>
                <option value="0" @selected((string) old('status', $user->status ?? 1) === '0')>禁用</option>
            </select>

            <button class="btn" type="submit">保存</button>
        </form>
    </div>
@endsection
