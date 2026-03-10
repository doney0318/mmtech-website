<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MMTech CMS')</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background:#f3f4f6; color:#111827; }
        .wrap { display:flex; min-height:100vh; }
        .sidebar { width:220px; background:#111827; color:#fff; padding:20px; }
        .sidebar h2 { font-size:18px; margin-top:0; }
        .sidebar a { display:block; color:#d1d5db; text-decoration:none; padding:8px 0; }
        .sidebar a:hover { color:#fff; }
        .main { flex:1; padding:24px; }
        .card { background:#fff; border-radius:10px; padding:18px; box-shadow:0 4px 16px rgba(0,0,0,.06); margin-bottom:16px; }
        .top { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
        .btn { display:inline-block; padding:8px 12px; border:0; border-radius:6px; background:#2563eb; color:#fff; text-decoration:none; cursor:pointer; }
        .btn-danger { background:#dc2626; }
        .table { width:100%; border-collapse:collapse; background:#fff; }
        .table th,.table td { border-bottom:1px solid #e5e7eb; padding:10px; text-align:left; }
        input,textarea,select { width:100%; box-sizing:border-box; border:1px solid #d1d5db; border-radius:6px; padding:8px; margin-bottom:10px; }
        label { font-weight:600; font-size:14px; }
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        .alert-success { background:#dcfce7; color:#166534; padding:10px; border-radius:6px; margin-bottom:12px; }
        .alert-error { background:#fee2e2; color:#991b1b; padding:10px; border-radius:6px; margin-bottom:12px; }
    </style>
</head>
<body>
<div class="wrap">
    <aside class="sidebar">
        <h2>MMTech CMS</h2>
        <a href="{{ route('admin.dashboard') }}">控制台</a>
        <a href="{{ route('admin.articles.index') }}">文章管理</a>
        <a href="{{ route('admin.categories.index') }}">文章分类</a>
        <a href="{{ route('admin.pages.index') }}">自定义页面</a>
        <a href="{{ route('admin.settings.index') }}">系统设置</a>
        <a href="{{ route('admin.users.index') }}">用户管理</a>
        <a href="{{ route('admin.profile.edit') }}">个人中心</a>
        <form method="post" action="{{ route('admin.logout') }}" style="margin-top:16px;">
            @csrf
            <button class="btn btn-danger" type="submit">退出登录</button>
        </form>
    </aside>
    <main class="main">
        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert-error">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif
        @yield('content')
    </main>
</div>
</body>
</html>
