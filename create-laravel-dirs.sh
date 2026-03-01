#!/bin/bash
# create-laravel-dirs.sh - 仅创建 Laravel 必需的目录

echo "📁 创建 Laravel 必需目录..."
echo "======================================"

# 当前目录
CURRENT_DIR=$(pwd)
echo "当前目录: $CURRENT_DIR"

# 1. 创建缺失的目录
echo ""
echo "1. 创建目录结构..."
mkdir -p storage storage/framework storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
mkdir -p bootstrap/cache

echo "✅ 目录创建完成"

# 2. 显示创建的目录
echo ""
echo "2. 目录结构:"
echo "storage/ 目录:"
ls -la storage/
echo ""
echo "bootstrap/cache/ 目录:"
ls -la bootstrap/cache/

# 3. 设置建议权限（仅建议，不强制修改）
echo ""
echo "3. 权限建议（根据服务器配置调整）:"
echo "--------------------------------------"
echo "# 宝塔面板通常使用 www:www 用户组"
echo "chown -R www:www storage bootstrap/cache"
echo "chmod -R 755 storage bootstrap/cache"
echo ""
echo "# 如果需要写入权限（如缓存、会话）"
echo "chmod -R 777 storage/framework/sessions storage/framework/views"
echo ""

# 4. 验证目录是否可写
echo "4. 验证目录可写性:"
php -r "
\$dirs = [
    'storage' => 'storage 目录',
    'bootstrap/cache' => 'bootstrap/cache 目录'
];

foreach (\$dirs as \$dir => \$name) {
    if (!is_dir(\$dir)) {
        echo \"❌ \$name: 目录不存在\\n\";
    } else {
        echo is_writable(\$dir) ? \"✅ \$name: 可写\\n\" : \"⚠️  \$name: 不可写\\n\";
    }
}
"

# 5. 创建 .env 文件（如果不存在）
echo ""
echo "5. 检查 .env 文件..."
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
        echo "✅ 从 .env.example 创建 .env"
    else
        echo "⚠️  .env.example 不存在，无法创建 .env"
    fi
else
    echo "✅ .env 文件已存在"
fi

echo ""
echo "======================================"
echo "🎉 目录创建完成！"
echo ""
echo "📋 下一步:"
echo "1. 访问 https://mmtech.ltd/install/test_simple.php 验证"
echo "2. 访问 https://mmtech.ltd/install/install_check.php 检测环境"
echo "3. 如果目录不可写，请设置正确权限"
echo ""
echo "💡 快速权限设置（如果需要）:"
echo "chmod -R 777 storage bootstrap/cache"
echo "chown -R www:www storage bootstrap/cache"
echo ""
echo "🔧 测试命令:"
echo "curl https://mmtech.ltd/install/test_simple.php"
echo "curl https://mmtech.ltd/install/install_check.php"