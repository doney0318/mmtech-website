#!/bin/bash

echo "🚀 MMTech Laravel 项目服务器测试脚本"
echo "======================================"

# 检查当前目录
echo "1. 检查当前目录..."
pwd

# 检查文件
echo -e "\n2. 检查关键文件..."
if [ -f "artisan" ]; then
    echo "   ✅ artisan 文件存在"
    ls -la artisan
else
    echo "   ❌ artisan 文件缺失"
fi

if [ -f "composer.json" ]; then
    echo "   ✅ composer.json 文件存在"
else
    echo "   ❌ composer.json 文件缺失"
fi

if [ -f "bootstrap/app.php" ]; then
    echo "   ✅ bootstrap/app.php 文件存在"
else
    echo "   ❌ bootstrap/app.php 文件缺失"
fi

# 检查 PHP
echo -e "\n3. 检查 PHP 版本..."
php -v

# 检查 Composer
echo -e "\n4. 检查 Composer..."
if command -v composer &> /dev/null; then
    echo "   ✅ Composer 已安装"
    composer --version
else
    echo "   ❌ Composer 未安装"
    echo "   安装命令: curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer"
fi

# 检查 vendor 目录
echo -e "\n5. 检查 vendor 目录..."
if [ -d "vendor" ]; then
    echo "   ✅ vendor 目录存在"
    echo "   依赖已安装，跳过安装步骤"
else
    echo "   ⚠️ vendor 目录不存在"
    echo "   运行以下命令安装依赖:"
    echo "   composer install --no-dev --optimize-autoloader"
fi

# 测试 artisan
echo -e "\n6. 测试 artisan 命令..."
if [ -f "artisan" ]; then
    php artisan --version 2>/dev/null && echo "   ✅ artisan 命令工作正常" || echo "   ❌ artisan 命令失败（可能需要先安装依赖）"
else
    echo "   ⚠️ 跳过 artisan 测试（文件不存在）"
fi

echo -e "\n======================================"
echo "📋 下一步建议："
echo "1. 如果 vendor 目录不存在，运行: composer install --no-dev --optimize-autoloader"
echo "2. 复制环境配置: cp .env.example .env"
echo "3. 生成应用密钥: php artisan key:generate"
echo "4. 配置数据库连接（编辑 .env 文件）"
echo "5. 设置目录权限: chmod -R 775 storage bootstrap/cache"
echo "6. 测试运行: php artisan serve --host=0.0.0.0 --port=8000"
echo "======================================"
