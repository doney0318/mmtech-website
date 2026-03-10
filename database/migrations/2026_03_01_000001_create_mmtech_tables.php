<?php
// database/migrations/2026_03_01_000001_create_mmtech_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 管理员表
        if (!Schema::hasTable('mm_admin')) {
            Schema::create('mm_admin', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique()->comment('用户名');
            $table->string('password', 255)->comment('密码');
            $table->string('email', 100)->nullable()->comment('邮箱');
            $table->string('nickname', 50)->nullable()->comment('昵称');
            $table->unsignedTinyInteger('role_id')->default(1)->comment('角色ID');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：1正常 0禁用');
            $table->string('last_login_ip', 45)->nullable()->comment('最后登录IP');
            $table->timestamp('last_login_time')->nullable()->comment('最后登录时间');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
            $table->index('role_id');
            });
        }

        // 网站配置表
        if (!Schema::hasTable('mm_config')) {
            Schema::create('mm_config', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique()->comment('配置键');
            $table->text('value')->nullable()->comment('配置值');
            $table->string('group', 50)->default('base')->comment('分组');
            $table->string('type', 20)->default('text')->comment('类型');
            $table->string('title_zh', 100)->comment('中文标题');
            $table->string('title_en', 100)->comment('英文标题');
            $table->text('description')->nullable()->comment('描述');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->timestamps();
            
            $table->index('group');
            $table->index('sort');
            });
        }

        // 服务项目表
        if (!Schema::hasTable('mm_service')) {
            Schema::create('mm_service', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique()->comment('URL标识');
            $table->string('title_zh', 200)->comment('中文标题');
            $table->string('title_en', 200)->comment('英文标题');
            $table->text('description_zh')->nullable()->comment('中文描述');
            $table->text('description_en')->nullable()->comment('英文描述');
            $table->text('content_zh')->nullable()->comment('中文内容');
            $table->text('content_en')->nullable()->comment('英文内容');
            $table->string('icon', 100)->nullable()->comment('图标');
            $table->string('image', 255)->nullable()->comment('图片');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：1显示 0隐藏');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->unsignedInteger('views')->default(0)->comment('浏览量');
            $table->timestamps();
            
            $table->index('status');
            $table->index('sort');
            $table->index('slug');
            });
        }

        // 项目案例表
        if (!Schema::hasTable('mm_case')) {
            Schema::create('mm_case', function (Blueprint $table) {
            $table->id();
            $table->string('title_zh', 200)->comment('中文标题');
            $table->string('title_en', 200)->comment('英文标题');
            $table->text('description_zh')->nullable()->comment('中文描述');
            $table->text('description_en')->nullable()->comment('英文描述');
            $table->text('content_zh')->nullable()->comment('中文内容');
            $table->text('content_en')->nullable()->comment('英文内容');
            $table->string('cover_image', 255)->nullable()->comment('封面图');
            $table->json('images')->nullable()->comment('案例图片');
            $table->string('client', 100)->nullable()->comment('客户名称');
            $table->string('industry', 100)->nullable()->comment('所属行业');
            $table->date('project_date')->nullable()->comment('项目日期');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：1显示 0隐藏');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->unsignedInteger('views')->default(0)->comment('浏览量');
            $table->timestamps();
            
            $table->index('status');
            $table->index('sort');
            $table->index('project_date');
            });
        }

        // 技术文章表
        if (!Schema::hasTable('mm_article')) {
            Schema::create('mm_article', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique()->comment('URL标识');
            $table->string('title_zh', 200)->comment('中文标题');
            $table->string('title_en', 200)->comment('英文标题');
            $table->text('excerpt_zh')->nullable()->comment('中文摘要');
            $table->text('excerpt_en')->nullable()->comment('英文摘要');
            $table->text('content_zh')->nullable()->comment('中文内容');
            $table->text('content_en')->nullable()->comment('英文内容');
            $table->string('cover_image', 255)->nullable()->comment('封面图');
            $table->string('author', 100)->nullable()->comment('作者');
            $table->unsignedTinyInteger('category_id')->default(0)->comment('分类ID');
            $table->json('tags')->nullable()->comment('标签');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：1发布 0草稿');
            $table->timestamp('published_at')->nullable()->comment('发布时间');
            $table->unsignedInteger('views')->default(0)->comment('浏览量');
            $table->timestamps();
            
            $table->index('status');
            $table->index('category_id');
            $table->index('published_at');
            $table->index('slug');
            });
        }

        // 留言咨询表
        if (!Schema::hasTable('mm_inquiry')) {
            Schema::create('mm_inquiry', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('姓名');
            $table->string('email', 100)->comment('邮箱');
            $table->string('phone', 20)->nullable()->comment('电话');
            $table->string('company', 200)->nullable()->comment('公司');
            $table->text('message')->comment('留言内容');
            $table->text('reply')->nullable()->comment('回复内容');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态：0未处理 1已处理');
            $table->string('ip_address', 45)->nullable()->comment('IP地址');
            $table->timestamps();
            
            $table->index('status');
            $table->index('created_at');
            });
        }

        // 导航菜单表
        if (!Schema::hasTable('mm_navigation')) {
            Schema::create('mm_navigation', function (Blueprint $table) {
            $table->id();
            $table->string('name_zh', 100)->comment('中文名称');
            $table->string('name_en', 100)->comment('英文名称');
            $table->string('url', 255)->comment('链接地址');
            $table->string('icon', 100)->nullable()->comment('图标');
            $table->unsignedTinyInteger('type')->default(1)->comment('类型：1前台 2后台');
            $table->unsignedInteger('parent_id')->default(0)->comment('父级ID');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：1显示 0隐藏');
            $table->timestamps();
            
            $table->index('type');
            $table->index('parent_id');
            $table->index('sort');
            $table->index('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mm_navigation');
        Schema::dropIfExists('mm_inquiry');
        Schema::dropIfExists('mm_article');
        Schema::dropIfExists('mm_case');
        Schema::dropIfExists('mm_service');
        Schema::dropIfExists('mm_config');
        Schema::dropIfExists('mm_admin');
    }
};
