#!/bin/bash

# MMTech Laravel 项目 Composer 配置检查脚本

echo "🔍 检查 MMTech Laravel 项目配置..."
echo "======================================"

# 检查 composer.json
if [ -f "composer.json" ]; then
    echo "✅ composer.json 存在"
    echo "   位置: $(pwd)/composer.json"
    echo "   大小: $(wc -l < composer.json) 行"
else
    echo "❌ composer.json 不存在"
    echo "   请运行: cp composer.json.example composer.json"
    exit 1
fi

# 检查 PHP 版本
echo ""
echo "📊 检查 PHP 环境..."
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v | head -1 | cut -d' ' -f2)
    echo "✅ PHP 已安装: $PHP_VERSION"
    
    # 检查 PHP 版本是否符合要求
    PHP_MAJOR=$(echo $PHP_VERSION | cut -d. -f1)
    PHP_MINOR=$(echo $PHP_VERSION | cut -d. -f2)
    if [ $PHP_MAJOR -ge 8 ] && [ $PHP_MINOR -ge 2 ]; then
        echo "✅ PHP 版本符合要求 (>= 8.2)"
    else
        echo "⚠️  PHP 版本可能不符合要求 (需要 >= 8.2)"
    fi
else
    echo "❌ PHP 未安装"
    echo "   请安装 PHP 8.2 或更高版本"
fi

# 检查 Composer
echo ""
echo "📦 检查 Composer..."
if command -v composer &> /dev/null; then
    COMPOSER_VERSION=$(composer --version | head -1)
    echo "✅ Composer 已安装: $COMPOSER_VERSION"
    
    # 检查依赖
    if [ -f "vendor/autoload.php" ]; then
        echo "✅ 依赖已安装 (vendor/ 目录存在)"
    else
        echo "⚠️  依赖未安装"
        echo "   请运行: composer install"
    fi
else
    echo "❌ Composer 未安装"
    echo "   安装方法:"
    echo "   1. 官方安装: https://getcomposer.org/download/"
    echo "   2. 使用包管理器:"
    echo "      - macOS: brew install composer"
    echo "      - Ubuntu/Debian: sudo apt install composer"
    echo "      - CentOS/RHEL: sudo yum install composer"
fi

# 检查 Laravel 核心文件
echo ""
echo "🚀 检查 Laravel 核心文件..."
LARAVEL_FILES=(
    "app/Http/Controllers/Controller.php"
    "bootstrap/app.php"
    "config/app.php"
    "routes/web.php"
    "public/index.php"
)

MISSING_FILES=0
for file in "${LARAVEL_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $file 存在"
    else
        echo "❌ $file 不存在"
        MISSING_FILES=$((MISSING_FILES+1))
    fi
done

# 检查目录权限
echo ""
echo "🔐 检查目录权限..."
STORAGE_PERM=$(stat -f "%A" storage 2>/dev/null || stat -c "%a" storage 2>/dev/null)
BOOTSTRAP_CACHE_PERM=$(stat -f "%A" bootstrap/cache 2>/dev/null || stat -c "%a" bootstrap/cache 2>/dev/null)

if [ "$STORAGE_PERM" -ge 775 ] 2>/dev/null; then
    echo "✅ storage 目录权限: $STORAGE_PERM"
else
    echo "⚠️  storage 目录权限可能需要调整: $STORAGE_PERM"
    echo "   建议: chmod -R 775 storage"
fi

if [ "$BOOTSTRAP_CACHE_PERM" -ge 775 ] 2>/dev/null; then
    echo "✅ bootstrap/cache 目录权限: $BOOTSTRAP_CACHE_PERM"
else
    echo "⚠️  bootstrap/cache 目录权限可能需要调整: $BOOTSTRAP_CACHE_PERM"
    echo "   建议: chmod -R 775 bootstrap/cache"
fi

# 总结
echo ""
echo "======================================"
echo "📋 检查完成"

if [ $MISSING_FILES -eq 0 ]; then
    echo "✅ 项目结构基本完整"
    echo ""
    echo "🚀 下一步操作:"
    echo "   1. 安装 Composer (如果未安装)"
    echo "   2. 运行: composer install"
    echo "   3. 复制环境文件: cp .env.example .env"
    echo "   4. 生成应用密钥: php artisan key:generate"
    echo "   5. 配置数据库连接"
    echo "   6. 运行迁移: php artisan migrate"
else
    echo "⚠️  项目缺少 $MISSING_FILES 个核心文件"
    echo ""
    echo "🔧 建议操作:"
    echo "   1. 从 Laravel 11 官方项目复制缺失文件"
    echo "   2. 或运行: composer create-project laravel/laravel . --prefer-dist"
    echo "   3. 然后迁移现有代码到新项目"
fi

echo ""
echo "💡 提示: 这是一个从 ThinkPHP 迁移到 Laravel 的项目"
echo "       请参考 MIGRATION_PLAN.md 了解迁移详情"