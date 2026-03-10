@extends('admin.layouts.app')

@section('title', '文章管理')

@section('content')
    <div class="top">
        <h1>文章管理</h1>
        <a class="btn" href="{{ route('admin.articles.create') }}">新增文章</a>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>标题</th>
            <th>分类</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($articles as $article)
            <tr>
                <td>{{ $article->id }}</td>
                <td>{{ $article->title_zh }}</td>
                <td>{{ $article->category?->name ?? '-' }}</td>
                <td>{{ (int)$article->status === 1 ? '发布' : '草稿' }}</td>
                <td>
                    <a href="{{ route('admin.articles.edit', $article) }}">编辑</a>
                    <form style="display:inline;" method="post" action="{{ route('admin.articles.destroy', $article) }}">
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
