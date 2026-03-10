@extends('admin.layouts.app')

@section('title', '自定义页面')

@section('content')
    <div class="top">
        <h1>自定义页面</h1>
        <a class="btn" href="{{ route('admin.pages.create') }}">新增页面</a>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>标题</th>
            <th>Slug</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($pages as $page)
            <tr>
                <td>{{ $page->id }}</td>
                <td>{{ $page->title_zh }}</td>
                <td>{{ $page->slug }}</td>
                <td>{{ (int)$page->status === 1 ? '启用' : '禁用' }}</td>
                <td>
                    <a href="{{ route('admin.pages.edit', $page) }}">编辑</a>
                    <form style="display:inline;" method="post" action="{{ route('admin.pages.destroy', $page) }}">
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
