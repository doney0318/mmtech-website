<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleCategory extends Model
{
    protected $table = 'mm_article_category';

    protected $fillable = [
        'name',
        'slug',
        'sort',
        'status',
    ];
}
