<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('sort')->paginate(20);
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.form', [
            'page' => new Page(),
            'action' => route('admin.pages.store'),
            'method' => 'POST',
            'title' => '新增页面',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        Page::create($data);
        return redirect()->route('admin.pages.index')->with('success', '页面创建成功');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.form', [
            'page' => $page,
            'action' => route('admin.pages.update', $page),
            'method' => 'PUT',
            'title' => '编辑页面',
        ]);
    }

    public function update(Request $request, Page $page)
    {
        $data = $this->validateData($request);
        $page->update($data);
        return redirect()->route('admin.pages.index')->with('success', '页面更新成功');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', '页面删除成功');
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'slug' => ['nullable', 'string', 'max:100'],
            'title_zh' => ['required', 'string', 'max:200'],
            'title_en' => ['nullable', 'string', 'max:200'],
            'content_zh' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'status' => ['required', 'integer'],
            'sort' => ['nullable', 'integer'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title_en'] ?: $data['title_zh']).'-'.Str::lower(Str::random(6));
        $data['sort'] = $data['sort'] ?? 0;

        return $data;
    }
}
