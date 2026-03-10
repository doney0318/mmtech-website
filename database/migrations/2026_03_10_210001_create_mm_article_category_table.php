<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mm_article_category', function (Blueprint $table) {
            $table->unsignedTinyInteger('id', true);
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->unsignedInteger('sort')->default(0);
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mm_article_category');
    }
};
