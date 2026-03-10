<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMTech 后台控制台</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f8fafc; margin:0; }
        .wrap { max-width: 900px; margin: 48px auto; background:#fff; padding:24px; border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,.08); }
        h1 { margin-top: 0; }
        .muted { color:#6b7280; }
        form { margin-top: 20px; }
        button { padding:10px 14px; border:0; border-radius:6px; background:#ef4444; color:#fff; cursor:pointer; }
    </style>
</head>
<body>
<div class="wrap">
    <h1>后台控制台</h1>
    <p class="muted">欢迎你，{{ auth('admin')->user()->username ?? 'Admin' }}。</p>
    <p class="muted">当前为最小可用后台骨架，后续可继续接入菜单、权限和业务管理页面。</p>

    <form method="post" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit">退出登录</button>
    </form>
</div>
</body>
</html>
