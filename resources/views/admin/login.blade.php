<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMTech 后台登录</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .card {
            width: 360px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 24px;
        }
        h1 {
            margin: 0 0 16px;
            font-size: 22px;
            text-align: center;
        }
        .tip {
            margin: 0 0 16px;
            color: #6b7280;
            font-size: 13px;
            text-align: center;
        }
        input {
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 12px;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
        }
        button {
            width: 100%;
            padding: 10px 12px;
            border: 0;
            border-radius: 6px;
            background: #2563eb;
            color: #fff;
            cursor: pointer;
        }
        .note {
            margin-top: 12px;
            color: #9ca3af;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="card">
    <h1>MMTech 管理后台</h1>
    <p class="tip">请输入安装时创建的管理员账号密码。</p>
    @if ($errors->any())
        <p class="tip" style="color:#dc2626;">{{ $errors->first() }}</p>
    @endif
    <form method="post" action="{{ route('admin.login.submit') }}">
        @csrf
        <input type="text" name="username" value="{{ old('username') }}" placeholder="用户名" autocomplete="username">
        <input type="password" name="password" placeholder="密码" autocomplete="current-password">
        <button type="submit">登录</button>
    </form>
    <p class="note">登录后将进入后台控制台。</p>
</div>
</body>
</html>
