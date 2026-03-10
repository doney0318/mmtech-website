@extends('admin.layouts.app')

@section('title', '后台控制台')

@section('content')
    <div class="top">
        <h1>后台控制台</h1>
    </div>

    <div class="grid-2">
        <div class="card">
            <h3>文章数量</h3>
            <p>{{ $articleCount }}</p>
        </div>
        <div class="card">
            <h3>页面数量</h3>
            <p>{{ $pageCount }}</p>
        </div>
        <div class="card">
            <h3>管理员数量</h3>
            <p>{{ $adminCount }}</p>
        </div>
        <div class="card">
            <h3>当前账号</h3>
            <p>{{ auth('admin')->user()->username ?? 'Admin' }}</p>
        </div>
    </div>
@endsection
