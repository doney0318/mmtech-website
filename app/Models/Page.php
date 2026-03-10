<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'mm_page';

    protected $fillable = [
        'slug',
        'title_zh',
        'title_en',
        'content_zh',
        'content_en',
        'status',
        'sort',
    ];
}
