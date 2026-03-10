@extends('admin.layouts.app')

@section('title', $title)

@section('content')
    <div class="top">
        <h1>{{ $title }}</h1>
        <a class="btn" href="{{ route('admin.pages.index') }}">返回列表</a>
    </div>

    <div class="card">
        <form method="post" action="{{ $action }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <label>中文标题</label>
            <input name="title_zh" value="{{ old('title_zh', $page->title_zh) }}" required>

            <label>英文标题</label>
            <input name="title_en" value="{{ old('title_en', $page->title_en) }}">

            <label>Slug</label>
            <input name="slug" value="{{ old('slug', $page->slug) }}">

            <label>排序</label>
            <input type="number" name="sort" value="{{ old('sort', $page->sort ?? 0) }}">

            <label>状态</label>
            <select name="status">
                <option value="1" @selected((string) old('status', $page->status ?? 1) === '1')>启用</option>
                <option value="0" @selected((string) old('status', $page->status ?? 1) === '0')>禁用</option>
            </select>

            <label>中文内容</label>
            <textarea rows="10" name="content_zh">{{ old('content_zh', $page->content_zh) }}</textarea>

            <label>英文内容</label>
            <textarea rows="10" name="content_en">{{ old('content_en', $page->content_en) }}</textarea>

            <button class="btn" type="submit">保存</button>
        </form>
    </div>
@endsection
