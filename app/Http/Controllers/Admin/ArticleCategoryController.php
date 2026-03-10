<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleCategoryController extends Controller
{
    public function index()
    {
        $categories = ArticleCategory::orderBy('sort')->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.form', [
            'category' => new ArticleCategory(),
            'action' => route('admin.categories.store'),
            'method' => 'POST',
            'title' => '新增分类',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'integer'],
            'status' => ['required', 'integer'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']).'-'.Str::lower(Str::random(4));
        $data['sort'] = $data['sort'] ?? 0;
        ArticleCategory::create($data);

        return redirect()->route('admin.categories.index')->with('success', '分类创建成功');
    }

    public function edit(ArticleCategory $category)
    {
        return view('admin.categories.form', [
            'category' => $category,
            'action' => route('admin.categories.update', $category),
            'method' => 'PUT',
            'title' => '编辑分类',
        ]);
    }

    public function update(Request $request, ArticleCategory $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'integer'],
            'status' => ['required', 'integer'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']).'-'.Str::lower(Str::random(4));
        $data['sort'] = $data['sort'] ?? 0;
        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', '分类更新成功');
    }

    public function destroy(ArticleCategory $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', '分类删除成功');
    }
}
