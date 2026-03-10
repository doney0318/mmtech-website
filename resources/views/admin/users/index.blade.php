@extends('admin.layouts.app')

@section('title', '用户管理')

@section('content')
    <div class="top">
        <h1>用户管理</h1>
        <a class="btn" href="{{ route('admin.users.create') }}">新增管理员</a>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>用户名</th>
            <th>邮箱</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->username }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ (int)$user->status === 1 ? '启用' : '禁用' }}</td>
                <td>
                    <a href="{{ route('admin.users.edit', $user) }}">编辑</a>
                    <form style="display:inline;" method="post" action="{{ route('admin.users.destroy', $user) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit">删除</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
