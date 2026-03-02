# 🚀 MMTech Laravel 项目 Composer 配置指南

## 📋 当前状态

**项目**: MMTech 企业网站系统 (Laravel 11 版本)  
**问题**: Composer 配置不完整，缺少核心 Laravel 文件  
**状态**: 从 ThinkPHP 8.0 迁移到 Laravel 11 进行中

## ✅ 已完成的配置

1. **composer.json 文件已创建** ✅
   - 位置: `/Users/mac/.openclaw/workspace/mmtech-laravel/composer.json`
   - 包含: Laravel 11 标准依赖配置
   - PHP 要求: ^8.2

2. **项目结构已部分创建** ✅
   - 基础目录结构 (app/, config/, routes/, etc.)
   - 数据库迁移文件
   - 安装向导文件

## ❌ 缺失的配置

### 1. PHP 环境
- ❌ PHP 8.2+ 未安装
- ❌ PHP 扩展未配置

### 2. Composer 工具
- ❌ Composer 未安装
- ❌ 依赖包未安装 (vendor/ 目录不存在)

### 3. Laravel 核心文件
- ❌ `app/Http/Controllers/Controller.php`
- ❌ `bootstrap/app.php`
- ❌ `config/app.php`
- ❌ `routes/web.php`
- ❌ `public/index.php`

## 🛠️ 解决方案

### 方案一：完整安装 Laravel 11 (推荐)

#### 步骤 1: 安装 PHP 和 Composer

```bash
# macOS (使用 Homebrew)
brew install php@8.2
brew install composer

# Ubuntu/Debian
sudo apt update
sudo apt install php8.2 php8.2-{mbstring,xml,curl,mysql,zip} composer

# CentOS/RHEL
sudo yum install epel-release
sudo yum install php82 php82-php-{mbstring,xml,curl,mysqlnd,zip} composer
```

#### 步骤 2: 创建完整的 Laravel 项目

```bash
# 备份现有文件
cd /Users/mac/.openclaw/workspace/mmtech-laravel
mkdir backup_$(date +%Y%m%d)
cp -r app database public bootstrap config routes storage backup_$(date +%Y%m%d)/

# 创建新的 Laravel 11 项目
composer create-project laravel/laravel . --prefer-dist

# 恢复自定义文件
cp -r backup_$(date +%Y%m%d)/app/* app/
cp -r backup_$(date +%Y%m%d)/database/* database/
cp -r backup_$(date +%Y%m%d)/public/install public/
```

#### 步骤 3: 配置环境

```bash
# 复制环境文件
cp .env.example .env

# 生成应用密钥
php artisan key:generate

# 安装依赖
composer install

# 设置目录权限
chmod -R 775 storage bootstrap/cache
```

### 方案二：手动补充缺失文件

如果只想补充缺失的核心文件而不重新创建整个项目：

#### 步骤 1: 下载 Laravel 11 核心文件

```bash
# 下载 Laravel 11 发布包
curl -L https://github.com/laravel/laravel/archive/refs/tags/v11.0.0.tar.gz -o laravel-11.tar.gz
tar -xzf laravel-11.tar.gz

# 复制核心文件
cp laravel-11/app/Http/Controllers/Controller.php app/Http/Controllers/
cp laravel-11/bootstrap/app.php bootstrap/
cp laravel-11/config/app.php config/
cp laravel-11/routes/web.php routes/
cp laravel-11/public/index.php public/
cp laravel-11/public/.htaccess public/

# 复制其他必要配置文件
cp laravel-11/config/*.php config/
cp laravel-11/routes/*.php routes/
```

#### 步骤 2: 安装 Composer 依赖

```bash
# 安装依赖
composer install

# 如果 composer install 失败，先更新
composer update
```

### 方案三：使用 Docker 容器 (开发环境)

```bash
# 创建 docker-compose.yml
cat > docker-compose.yml << 'EOF'
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: mmtech-laravel-app
    restart: unless-stopped
    working_dir: /var/www/html
    volumes:
      - .:/var/www/html
    networks:
      - mmtech-network

  nginx:
    image: nginx:alpine
    container_name: mmtech-laravel-nginx
    restart: unless-stopped
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
      - ./docker/nginx.conf:/etc/nginx/conf.d/default.conf
    networks:
      - mmtech-network

  mysql:
    image: mysql:8.0
    container_name: mmtech-laravel-mysql
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: mmtech_laravel
      MYSQL_ROOT_PASSWORD: secret
    ports:
      - "3306:3306"
    volumes:
      - mysql-data:/var/lib/mysql
    networks:
      - mmtech-network

networks:
  mmtech-network:
    driver: bridge

volumes:
  mysql-data:
EOF

# 创建 Dockerfile
cat > Dockerfile << 'EOF'
FROM php:8.2-fpm

# 安装系统依赖
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# 清理缓存
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 安装 PHP 扩展
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 安装 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 设置工作目录
WORKDIR /var/www/html

# 复制应用文件
COPY . .

# 安装 PHP 依赖
RUN composer install --no-interaction --optimize-autoloader

# 设置权限
RUN chown -R www-data:www-data /var/www/html/storage
RUN chmod -R 775 /var/www/html/storage

EXPOSE 9000
CMD ["php-fpm"]
EOF

# 启动服务
docker-compose up -d
```

## 📊 验证安装

### 验证步骤

```bash
# 1. 检查 PHP 版本
php --version

# 2. 检查 Composer
composer --version

# 3. 检查 Laravel
php artisan --version

# 4. 检查核心文件
ls -la app/Http/Controllers/Controller.php
ls -la bootstrap/app.php
ls -la config/app.php
ls -la routes/web.php
ls -la public/index.php

# 5. 运行开发服务器测试
php artisan serve
# 访问: http://localhost:8000
```

### 预期输出

```
✅ PHP 8.2.x (cli)
✅ Composer version 2.x.x
✅ Laravel Framework 11.x.x
✅ 所有核心文件存在
✅ 开发服务器运行正常
```

## 🔧 故障排除

### 常见问题 1: Composer 安装失败

```bash
# 清除 Composer 缓存
composer clear-cache

# 使用中国镜像 (如果网络慢)
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

# 重新安装
composer install --no-scripts
```

### 常见问题 2: 权限错误

```bash
# 修复存储目录权限
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 常见问题 3: PHP 扩展缺失

```bash
# 检查已安装的扩展
php -m

# 安装常见扩展 (Ubuntu/Debian)
sudo apt install php8.2-{mbstring,xml,curl,mysql,zip,gd,bcmath}
```

## 🎯 下一步

1. **选择方案**：根据需求选择方案一、二或三
2. **安装环境**：安装 PHP 8.2+ 和 Composer
3. **配置项目**：运行 Composer 安装依赖
4. **迁移数据**：参考 `MIGRATION_PLAN.md` 迁移 ThinkPHP 数据
5. **测试运行**：启动开发服务器测试

## 📞 支持

- **项目文档**: `README.md`, `MIGRATION_PLAN.md`
- **检查脚本**: `./check_composer_setup.sh`
- **安装指南**: `INSTALL_FIX_GUIDE.md`
- **目录创建**: `CREATE_DIRS_GUIDE.md`

---

**最后更新**: 2026-03-02  
**状态**: Composer 配置已创建，需要安装 PHP 和 Composer 工具