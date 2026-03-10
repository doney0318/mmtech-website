@extends('admin.layouts.app')

@section('title', '文章分类')

@section('content')
    <div class="top">
        <h1>文章分类</h1>
        <a class="btn" href="{{ route('admin.categories.create') }}">新增分类</a>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>名称</th>
            <th>Slug</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($categories as $category)
            <tr>
                <td>{{ $category->id }}</td>
                <td>{{ $category->name }}</td>
                <td>{{ $category->slug }}</td>
                <td>{{ (int)$category->status === 1 ? '启用' : '禁用' }}</td>
                <td>
                    <a href="{{ route('admin.categories.edit', $category) }}">编辑</a>
                    <form style="display:inline;" method="post" action="{{ route('admin.categories.destroy', $category) }}">
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
