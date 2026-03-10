<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Article;
use App\Models\Page;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $article = new Article();
        $page = new Page();
        $admin = new Admin();

        return view('admin.dashboard', [
            'articleCount' => Schema::hasTable($article->getTable()) ? Article::count() : 0,
            'pageCount' => Schema::hasTable($page->getTable()) ? Page::count() : 0,
            'adminCount' => Schema::hasTable($admin->getTable()) ? Admin::count() : 0,
        ]);
    }
}
