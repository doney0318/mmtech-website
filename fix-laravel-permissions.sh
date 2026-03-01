#!/bin/bash
# fix-laravel-permissions.sh - 修复 Laravel 目录权限和配置

echo "🔧 开始修复 Laravel 环境问题..."
echo "======================================"

# 1. 创建缺失的目录
echo "📁 创建缺失的目录..."
mkdir -p storage storage/framework storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
mkdir -p bootstrap/cache

echo "✅ 目录创建完成"

# 2. 设置权限
echo "🔒 设置目录权限..."
chmod -R 775 storage bootstrap/cache
chmod -R 777 storage/framework/sessions storage/framework/views

# 设置正确的所有者（根据服务器配置调整）
# chown -R www-data:www-data storage bootstrap/cache

echo "✅ 权限设置完成"

# 3. 检查 PHP 扩展
echo "🔍 检查 PHP 扩展..."
echo "已安装的扩展:"
php -m | grep -E "(pdo_mysql|mbstring|openssl|json|curl|fileinfo|gd|zip|xml|tokenizer)"

echo ""
echo "📋 需要安装的扩展:"
echo "1. fileinfo (用于文件上传和验证)"
echo "2. gd (用于图片处理)"
echo "3. zip (用于压缩解压)"
echo ""

# 4. 安装 fileinfo 扩展的指令
echo "💡 安装 fileinfo 扩展 (在服务器上执行):"
echo "--------------------------------------"
echo "# 对于 Ubuntu/Debian:"
echo "sudo apt-get update"
echo "sudo apt-get install php8.2-fileinfo"
echo "sudo systemctl restart php8.2-fpm"
echo ""
echo "# 对于 CentOS/RHEL:"
echo "sudo yum install php-fileinfo"
echo "sudo systemctl restart php-fpm"
echo ""
echo "# 对于宝塔面板:"
echo "1. 进入宝塔面板"
echo "2. 点击左侧 '软件商店'"
echo "3. 找到 PHP 8.2"
echo "4. 点击 '设置'"
echo "5. 点击 '安装扩展'"
echo "6. 找到 'fileinfo' 并安装"
echo "7. 重启 PHP-FPM"
echo ""

# 5. 检查当前环境
echo "🌐 当前环境信息:"
echo "PHP 版本: $(php -v | head -1)"
echo "工作目录: $(pwd)"
echo ""

# 6. 目录结构验证
echo "📂 目录结构验证:"
echo "storage/ 目录:"
ls -la storage/
echo ""
echo "bootstrap/cache/ 目录:"
ls -la bootstrap/cache/
echo ""

# 7. 创建 .env 文件模板
echo "📝 创建 .env 文件模板..."
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
        echo "✅ 从 .env.example 创建 .env"
    else
        cat > .env << 'EOF'
APP_NAME="MMTech"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://mmtech.ltd

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mmtech_laravel
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
EOF
        echo "✅ 创建默认 .env 文件"
    fi
else
    echo "✅ .env 文件已存在"
fi

# 8. 生成应用密钥
echo "🔑 生成应用密钥..."
if grep -q "^APP_KEY=$" .env; then
    php artisan key:generate --show
    echo "💡 请将上面的密钥复制到 .env 文件的 APP_KEY= 后面"
else
    echo "✅ 应用密钥已设置"
fi

echo ""
echo "======================================"
echo "🎉 修复完成！"
echo ""
echo "📋 下一步操作:"
echo "1. 在服务器上安装 fileinfo 扩展"
echo "2. 重启 PHP-FPM"
echo "3. 运行: php artisan storage:link"
echo "4. 运行: php artisan optimize:clear"
echo "5. 访问 https://mmtech.ltd/install/ 继续安装"
echo ""
echo "💡 快速测试:"
echo "访问 https://mmtech.ltd/install/test_simple.php"
echo "应该显示所有扩展都已加载 ✅"
echo ""
echo "🔧 如果还有问题，检查:"
echo "- PHP-FPM 是否运行"
echo "- Nginx 配置是否正确"
echo "- 目录权限是否正确"
echo "- .env 文件是否存在"