<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Article;
use App\Models\Page;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'articleCount' => Article::count(),
            'pageCount' => Page::count(),
            'adminCount' => Admin::count(),
        ]);
    }
}
