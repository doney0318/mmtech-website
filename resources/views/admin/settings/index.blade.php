@extends('admin.layouts.app')

@section('title', '系统设置')

@section('content')
    <div class="top">
        <h1>系统设置</h1>
    </div>

    <div class="card">
        <form method="post" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            <label>网站标题</label>
            <input name="site_title" value="{{ old('site_title', $settings['site_title']->{$valueColumn} ?? '') }}">

            <label>网站描述</label>
            <textarea rows="4" name="site_description">{{ old('site_description', $settings['site_description']->{$valueColumn} ?? '') }}</textarea>

            <label>联系邮箱</label>
            <input name="contact_email" value="{{ old('contact_email', $settings['contact_email']->{$valueColumn} ?? '') }}">

            <label>联系电话</label>
            <input name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone']->{$valueColumn} ?? '') }}">

            <label>公司地址</label>
            <input name="company_address" value="{{ old('company_address', $settings['company_address']->{$valueColumn} ?? '') }}">

            <button class="btn" type="submit">保存设置</button>
        </form>
    </div>
@endsection
