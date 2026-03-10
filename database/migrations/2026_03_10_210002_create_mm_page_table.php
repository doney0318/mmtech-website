<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mm_page', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique();
            $table->string('title_zh', 200);
            $table->string('title_en', 200)->nullable();
            $table->text('content_zh')->nullable();
            $table->text('content_en')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mm_page');
    }
};
