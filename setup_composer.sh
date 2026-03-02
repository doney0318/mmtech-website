#!/bin/bash

# MMTech Laravel 项目 Composer 自动安装脚本

set -e

echo "🚀 MMTech Laravel Composer 安装脚本"
echo "======================================"

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 检查是否在项目目录
if [ ! -f "composer.json" ]; then
    echo -e "${RED}❌ 错误: 请在项目根目录运行此脚本${NC}"
    echo "当前目录: $(pwd)"
    exit 1
fi

echo -e "${GREEN}✅ 检测到 composer.json 文件${NC}"

# 函数：检查命令是否存在
check_command() {
    if command -v $1 &> /dev/null; then
        echo -e "${GREEN}✅ $1 已安装${NC}"
        return 0
    else
        echo -e "${YELLOW}⚠️  $1 未安装${NC}"
        return 1
    fi
}

# 检查 PHP
echo ""
echo "📊 检查 PHP 环境..."
if check_command "php"; then
    PHP_VERSION=$(php -v | head -1 | cut -d' ' -f2)
    echo -e "   版本: $PHP_VERSION"
    
    # 检查 PHP 扩展
    echo "   检查 PHP 扩展..."
    REQUIRED_EXTENSIONS=("mbstring" "xml" "curl" "pdo_mysql" "json" "bcmath" "gd")
    for ext in "${REQUIRED_EXTENSIONS[@]}"; do
        if php -m | grep -i $ext > /dev/null; then
            echo -e "   ${GREEN}✅ $ext${NC}"
        else
            echo -e "   ${YELLOW}⚠️  $ext (可能需要安装)${NC}"
        fi
    done
else
    echo -e "${RED}❌ PHP 未安装，请先安装 PHP 8.2+${NC}"
    echo ""
    echo "安装方法:"
    echo "  macOS:   brew install php@8.2"
    echo "  Ubuntu:  sudo apt install php8.2 php8.2-{mbstring,xml,curl,mysql,zip,gd,bcmath}"
    echo "  CentOS:  sudo yum install php82 php82-php-{mbstring,xml,curl,mysqlnd,zip,gd,bcmath}"
    exit 1
fi

# 检查 Composer
echo ""
echo "📦 检查 Composer..."
if check_command "composer"; then
    COMPOSER_VERSION=$(composer --version | head -1)
    echo -e "   版本: $COMPOSER_VERSION"
else
    echo -e "${YELLOW}⚠️  Composer 未安装，正在尝试安装...${NC}"
    
    # 尝试安装 Composer
    echo "   下载 Composer..."
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php
    php -r "unlink('composer-setup.php');"
    
    # 移动 Composer 到全局
    if [ -f "composer.phar" ]; then
        sudo mv composer.phar /usr/local/bin/composer
        sudo chmod +x /usr/local/bin/composer
        echo -e "${GREEN}✅ Composer 安装成功${NC}"
    else
        echo -e "${RED}❌ Composer 安装失败${NC}"
        echo "请手动安装 Composer: https://getcomposer.org/download/"
        exit 1
    fi
fi

# 设置中国镜像（可选）
echo ""
echo "🌐 配置 Composer 镜像..."
read -p "是否使用中国镜像加速？(y/n, 默认 y): " USE_MIRROR
USE_MIRROR=${USE_MIRROR:-y}

if [[ $USE_MIRROR =~ ^[Yy]$ ]]; then
    echo "   设置阿里云镜像..."
    composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
    echo -e "${GREEN}✅ 镜像设置完成${NC}"
fi

# 清除缓存
echo ""
echo "🧹 清除 Composer 缓存..."
composer clear-cache
echo -e "${GREEN}✅ 缓存已清除${NC}"

# 安装依赖
echo ""
echo "📥 安装项目依赖..."
echo "   这可能需要几分钟时间，请耐心等待..."

# 检查是否已有 vendor 目录
if [ -d "vendor" ]; then
    echo "   检测到已有 vendor 目录，尝试更新..."
    composer update --no-scripts
else
    echo "   首次安装依赖..."
    composer install --no-scripts
fi

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ 依赖安装成功${NC}"
else
    echo -e "${RED}❌ 依赖安装失败${NC}"
    echo "可能的原因:"
    echo "  1. 网络连接问题"
    echo "  2. PHP 版本不符合要求"
    echo "  3. 缺少必要的 PHP 扩展"
    echo "  4. 内存不足 (尝试: COMPOSER_MEMORY_LIMIT=-1 composer install)"
    exit 1
fi

# 运行 Composer 脚本
echo ""
echo "⚡ 运行 Composer 脚本..."
composer run-script post-autoload-dump
echo -e "${GREEN}✅ 脚本执行完成${NC}"

# 检查 Laravel 核心文件
echo ""
echo "🔍 检查 Laravel 核心文件..."
MISSING_FILES=0

check_file() {
    if [ -f "$1" ]; then
        echo -e "   ${GREEN}✅ $1${NC}"
    else
        echo -e "   ${YELLOW}⚠️  $1 (缺失)${NC}"
        MISSING_FILES=$((MISSING_FILES+1))
    fi
}

check_file "vendor/autoload.php"
check_file "bootstrap/app.php"
check_file "config/app.php"
check_file "routes/web.php"
check_file "public/index.php"

if [ $MISSING_FILES -gt 0 ]; then
    echo -e "${YELLOW}⚠️  缺少 $MISSING_FILES 个核心文件${NC}"
    echo "   可能需要从 Laravel 官方项目复制缺失文件"
    echo "   参考: COMPOSER_SETUP_GUIDE.md 中的方案二"
fi

# 设置目录权限
echo ""
echo "🔐 设置目录权限..."
if [ -d "storage" ]; then
    chmod -R 775 storage 2>/dev/null || sudo chmod -R 775 storage
    echo -e "   ${GREEN}✅ storage 目录权限已设置${NC}"
fi

if [ -d "bootstrap/cache" ]; then
    chmod -R 775 bootstrap/cache 2>/dev/null || sudo chmod -R 775 bootstrap/cache
    echo -e "   ${GREEN}✅ bootstrap/cache 目录权限已设置${NC}"
fi

# 验证安装
echo ""
echo "🎯 验证安装..."
if [ -f "vendor/autoload.php" ]; then
    echo -e "${GREEN}✅ Composer 依赖安装验证通过${NC}"
    
    # 尝试检查 Laravel
    if [ -f "artisan" ]; then
        ARTISAN_VERSION=$(php artisan --version 2>/dev/null || echo "无法获取版本")
        echo -e "   Laravel: $ARTISAN_VERSION"
    fi
else
    echo -e "${YELLOW}⚠️  依赖安装可能不完整${NC}"
fi

# 总结
echo ""
echo "======================================"
echo -e "${GREEN}🚀 Composer 配置完成！${NC}"
echo ""
echo "📋 下一步操作:"
echo "   1. 复制环境文件: cp .env.example .env"
echo "   2. 生成应用密钥: php artisan key:generate"
echo "   3. 配置数据库连接 (编辑 .env 文件)"
echo "   4. 运行数据库迁移: php artisan migrate"
echo "   5. 启动开发服务器: php artisan serve"
echo ""
echo "📚 参考文档:"
echo "   - COMPOSER_SETUP_GUIDE.md (详细配置指南)"
echo "   - README.md (项目文档)"
echo "   - MIGRATION_PLAN.md (迁移计划)"
echo ""
echo "💡 提示: 如果遇到问题，请运行 ./check_composer_setup.sh 进行检查"
echo "======================================"