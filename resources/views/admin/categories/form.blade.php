@extends('admin.layouts.app')

@section('title', $title)

@section('content')
    <div class="top">
        <h1>{{ $title }}</h1>
        <a class="btn" href="{{ route('admin.categories.index') }}">返回列表</a>
    </div>

    <div class="card">
        <form method="post" action="{{ $action }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <label>分类名称</label>
            <input name="name" value="{{ old('name', $category->name) }}" required>

            <label>Slug</label>
            <input name="slug" value="{{ old('slug', $category->slug) }}">

            <label>排序</label>
            <input type="number" name="sort" value="{{ old('sort', $category->sort ?? 0) }}">

            <label>状态</label>
            <select name="status">
                <option value="1" @selected((string) old('status', $category->status ?? 1) === '1')>启用</option>
                <option value="0" @selected((string) old('status', $category->status ?? 1) === '0')>禁用</option>
            </select>

            <button class="btn" type="submit">保存</button>
        </form>
    </div>
@endsection
