@extends('admin.layouts.app')

@section('title', $title)

@section('content')
    <div class="top">
        <h1>{{ $title }}</h1>
        <a class="btn" href="{{ route('admin.articles.index') }}">返回列表</a>
    </div>

    <div class="card">
        <form method="post" action="{{ $action }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <label>中文标题</label>
            <input name="title_zh" value="{{ old('title_zh', $article->title_zh) }}" required>

            <label>英文标题</label>
            <input name="title_en" value="{{ old('title_en', $article->title_en) }}">

            <label>Slug</label>
            <input name="slug" value="{{ old('slug', $article->slug) }}">

            <label>分类</label>
            <select name="category_id">
                <option value="">未分类</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('category_id', $article->category_id) === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>

            <label>作者</label>
            <input name="author" value="{{ old('author', $article->author) }}">

            <label>标签（逗号分隔）</label>
            <input name="tags" value="{{ old('tags', is_array($article->tags ?? null) ? implode(',', $article->tags) : '') }}">

            <label>发布时间</label>
            <input type="datetime-local" name="published_at" value="{{ old('published_at', optional($article->published_at)->format('Y-m-d\TH:i')) }}">

            <label>状态</label>
            <select name="status">
                <option value="1" @selected((string) old('status', $article->status ?? 1) === '1')>发布</option>
                <option value="0" @selected((string) old('status', $article->status ?? 1) === '0')>草稿</option>
            </select>

            <label>中文摘要</label>
            <textarea rows="3" name="excerpt_zh">{{ old('excerpt_zh', $article->excerpt_zh) }}</textarea>

            <label>英文摘要</label>
            <textarea rows="3" name="excerpt_en">{{ old('excerpt_en', $article->excerpt_en) }}</textarea>

            <label>中文内容</label>
            <textarea rows="8" name="content_zh">{{ old('content_zh', $article->content_zh) }}</textarea>

            <label>英文内容</label>
            <textarea rows="8" name="content_en">{{ old('content_en', $article->content_en) }}</textarea>

            <button class="btn" type="submit">保存</button>
        </form>
    </div>
@endsection
