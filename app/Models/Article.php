<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'mm_article';

    protected $fillable = [
        'slug',
        'title_zh',
        'title_en',
        'excerpt_zh',
        'excerpt_en',
        'content_zh',
        'content_en',
        'cover_image',
        'author',
        'category_id',
        'tags',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id', 'id');
    }
}
