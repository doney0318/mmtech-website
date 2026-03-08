#!/bin/bash

# MMTech Laravel 项目一键安装脚本
# 适用于服务器快速安装

set -e  # 遇到错误立即退出

echo "========================================="
echo "🚀 MMTech Laravel 项目一键安装脚本"
echo "========================================="

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 函数：打印带颜色的消息
print_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# 检查是否以 root 运行
if [ "$EUID" -ne 0 ]; then 
    print_warning "建议以 root 用户运行此脚本"
    read -p "是否继续? (y/n): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

# 步骤1：检查系统环境
print_info "步骤1: 检查系统环境..."

# 检查 PHP
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -r "echo PHP_VERSION;")
    print_info "PHP 版本: $PHP_VERSION"
    
    # 检查 PHP 版本是否 >= 8.2
    if [[ "$PHP_VERSION" < "8.2" ]]; then
        print_error "PHP 版本过低，需要 8.2 或更高版本"
        exit 1
    fi
else
    print_error "PHP 未安装"
    exit 1
fi

# 检查 Composer
if command -v composer &> /dev/null; then
    COMPOSER_VERSION=$(composer --version | awk '{print $3}')
    print_info "Composer 版本: $COMPOSER_VERSION"
else
    print_error "Composer 未安装"
    echo "安装命令:"
    echo "  curl -sS https://getcomposer.org/installer | php"
    echo "  mv composer.phar /usr/local/bin/composer"
    exit 1
fi

# 步骤2：克隆或更新项目
print_info "步骤2: 准备项目目录..."

PROJECT_DIR="/root/mmtech-website"

if [ -d "$PROJECT_DIR" ]; then
    print_info "项目目录已存在，尝试更新..."
    cd "$PROJECT_DIR"
    
    # 备份当前项目
    BACKUP_DIR="/root/mmtech-website-backup-$(date +%Y%m%d%H%M%S)"
    print_info "备份当前项目到: $BACKUP_DIR"
    cp -r "$PROJECT_DIR" "$BACKUP_DIR"
    
    # 拉取最新代码
    git pull origin main
else
    print_info "克隆项目..."
    cd /root
    git clone https://github.com/doney0318/mmtech-website.git
    cd mmtech-website
fi

# 步骤3：安装 Composer 依赖
print_info "步骤3: 安装 Composer 依赖..."

if [ -d "vendor" ]; then
    print_warning "vendor 目录已存在，是否重新安装? (y/n): "
    read -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        print_info "删除 vendor 目录并重新安装..."
        rm -rf vendor composer.lock
        composer install --no-dev --optimize-autoloader
    else
        print_info "跳过依赖安装"
    fi
else
    print_info "安装依赖..."
    composer install --no-dev --optimize-autoloader
fi

# 步骤4：设置文件权限
print_info "步骤4: 设置文件权限..."

chmod +x artisan server_test.sh
chmod -R 775 storage bootstrap/cache

# 检查文件所有者
WEB_USER="www-data"
if id "www" &>/dev/null; then
    WEB_USER="www"
fi

print_info "设置文件所有者为: $WEB_USER"
chown -R $WEB_USER:$WEB_USER storage bootstrap/cache

# 步骤5：配置环境
print_info "步骤5: 配置环境..."

if [ ! -f ".env" ]; then
    print_info "创建 .env 文件..."
    cp .env.example .env
    
    # 生成应用密钥
    print_info "生成应用密钥..."
    php artisan key:generate
    
    print_warning "请编辑 .env 文件配置数据库连接:"
    echo "  nano .env"
    echo "  修改以下配置:"
    echo "  DB_CONNECTION=mysql"
    echo "  DB_HOST=127.0.0.1"
    echo "  DB_PORT=3306"
    echo "  DB_DATABASE=your_database"
    echo "  DB_USERNAME=your_username"
    echo "  DB_PASSWORD=your_password"
else
    print_info ".env 文件已存在"
fi

# 步骤6：验证安装
print_info "步骤6: 验证安装..."

echo "--- 验证结果 ---"

# 检查 artisan
if [ -f "artisan" ]; then
    print_info "✅ artisan 文件存在"
else
    print_error "❌ artisan 文件缺失"
fi

# 检查 vendor
if [ -d "vendor" ]; then
    print_info "✅ vendor 目录存在"
else
    print_error "❌ vendor 目录缺失"
fi

# 测试 Laravel
if php artisan --version &>/dev/null; then
    LARAVEL_VERSION=$(php artisan --version | awk '{print $3}')
    print_info "✅ Laravel 版本: $LARAVEL_VERSION"
else
    print_error "❌ Laravel 无法运行"
fi

# 运行测试脚本
print_info "运行详细测试..."
chmod +x server_test.sh
./server_test.sh

# 步骤7：完成提示
print_info "========================================="
print_info "✅ 安装完成!"
print_info "========================================="

echo ""
echo "📋 下一步操作:"
echo ""
echo "1. 配置数据库:"
echo "   nano .env"
echo "   修改数据库连接配置"
echo ""
echo "2. 运行数据库迁移 (如果需要):"
echo "   php artisan migrate"
echo ""
echo "3. 配置 Web 服务器:"
echo "   Nginx/Apache 指向: $(pwd)/public"
echo ""
echo "4. 测试运行:"
echo "   php artisan serve --host=0.0.0.0 --port=8000"
echo "   访问: http://服务器IP:8000"
echo ""
echo "5. 生产环境优化:"
echo "   composer install --optimize-autoloader --no-dev"
echo "   php artisan config:cache"
echo "   php artisan route:cache"
echo "   php artisan view:cache"
echo ""
echo "🔧 故障排除:"
echo "   运行: ./server_test.sh"
echo "   查看日志: tail -f storage/logs/laravel.log"
echo ""
echo "📞 技术支持:"
echo "   GitHub: https://github.com/doney0318/mmtech-website"
echo "   提供以下信息寻求帮助:"
echo "     - ./server_test.sh 的输出"
echo "     - 错误信息"
echo "     - PHP 版本: php -v"
echo ""

print_info "安装完成时间: $(date)"
print_info "项目目录: $(pwd)"
print_info "祝您使用愉快! 🎉"