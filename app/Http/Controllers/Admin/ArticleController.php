<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with('category')->latest()->paginate(15);
        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        $categories = ArticleCategory::orderBy('sort')->get();
        return view('admin.articles.form', [
            'article' => new Article(),
            'categories' => $categories,
            'action' => route('admin.articles.store'),
            'method' => 'POST',
            'title' => '新增文章',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        Article::create($data);
        return redirect()->route('admin.articles.index')->with('success', '文章创建成功');
    }

    public function edit(Article $article)
    {
        $categories = ArticleCategory::orderBy('sort')->get();
        return view('admin.articles.form', [
            'article' => $article,
            'categories' => $categories,
            'action' => route('admin.articles.update', $article),
            'method' => 'PUT',
            'title' => '编辑文章',
        ]);
    }

    public function update(Request $request, Article $article)
    {
        $data = $this->validateData($request, $article->id);
        $article->update($data);
        return redirect()->route('admin.articles.index')->with('success', '文章更新成功');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('admin.articles.index')->with('success', '文章删除成功');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'title_zh' => ['required', 'string', 'max:200'],
            'title_en' => ['nullable', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:100'],
            'excerpt_zh' => ['nullable', 'string'],
            'excerpt_en' => ['nullable', 'string'],
            'content_zh' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'author' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer'],
            'status' => ['required', 'integer'],
            'published_at' => ['nullable', 'date'],
            'tags' => ['nullable', 'string'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title_en'] ?: $data['title_zh']).'-'.Str::lower(Str::random(6));
        $data['tags'] = empty($data['tags']) ? null : array_map('trim', explode(',', $data['tags']));

        return $data;
    }
}
