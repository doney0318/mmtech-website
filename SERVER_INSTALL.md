# 🚀 MMTech Laravel 项目 - 服务器安装指南

## 📋 快速安装步骤

### 1. 克隆项目
```bash
cd /root
git clone https://github.com/doney0318/mmtech-website.git
cd mmtech-website
```

### 2. 运行环境检查
```bash
# 给测试脚本执行权限
chmod +x server_test.sh

# 运行环境检查
./server_test.sh
```

### 3. 安装 Composer 依赖（关键步骤）
```bash
# 如果 vendor 目录不存在，安装依赖
if [ ! -d "vendor" ]; then
    echo "正在安装 Composer 依赖..."
    composer install --no-dev --optimize-autoloader
else
    echo "vendor 目录已存在，跳过安装"
fi

# 确保 artisan 有执行权限
chmod +x artisan
```

### 4. 验证安装
```bash
# 测试 Laravel 是否正常工作
php artisan --version
```

### 5. 配置环境
```bash
# 复制环境配置文件
cp .env.example .env

# 生成应用密钥
php artisan key:generate

# 编辑数据库配置
# nano .env 或 vim .env
# 修改以下配置：
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=your_database_name
# DB_USERNAME=your_username
# DB_PASSWORD=your_password
```

### 6. 设置目录权限
```bash
# 设置存储目录权限
chmod -R 775 storage bootstrap/cache

# 设置所有者（根据你的 Web 服务器用户）
# Nginx + PHP-FPM: www-data
# Apache: www
chown -R www-data:www-data storage bootstrap/cache
```

### 7. 配置 Web 服务器

#### Nginx 配置
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /root/mmtech-website/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

#### Apache 配置
确保 `.htaccess` 文件在 `public/` 目录，并启用 `mod_rewrite`。

### 8. 测试运行
```bash
# 开发模式测试
php artisan serve --host=0.0.0.0 --port=8000

# 访问测试
# http://your-server-ip:8000
```

## 🔧 故障排除

### 问题1: "Could not open input file: artisan"
```bash
# 解决方案
chmod +x artisan
rm -rf vendor composer.lock
composer install --no-dev --optimize-autoloader
```

### 问题2: Composer 安装缓慢
```bash
# 使用中国镜像
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
composer clear-cache
composer install --no-dev --optimize-autoloader
```

### 问题3: PHP 版本过低
```bash
# 检查 PHP 版本
php -v

# Ubuntu/Debian 升级
sudo apt install php8.2 php8.2-{mbstring,xml,curl,mysql,zip,gd,bcmath,fpm}
```

### 问题4: 权限错误
```bash
# 修复权限
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache public
```

## 📊 安装验证清单

运行以下命令验证安装：

```bash
# 1. 检查 PHP 版本
php -v

# 2. 检查 Composer
composer --version

# 3. 检查 Laravel
php artisan --version

# 4. 检查关键文件
ls -la artisan bootstrap/app.php composer.json

# 5. 检查依赖
if [ -d "vendor" ]; then
    echo "✅ vendor 目录存在"
else
    echo "❌ vendor 目录缺失，运行: composer install"
fi
```

## 🚀 一键安装脚本

创建 `install.sh` 文件：

```bash
#!/bin/bash

echo "🚀 开始安装 MMTech Laravel 项目..."

# 1. 克隆项目
cd /root
if [ -d "mmtech-website" ]; then
    echo "项目已存在，跳过克隆"
else
    git clone https://github.com/doney0318/mmtech-website.git
fi
cd mmtech-website

# 2. 安装依赖
if [ ! -d "vendor" ]; then
    echo "安装 Composer 依赖..."
    composer install --no-dev --optimize-autoloader
else
    echo "vendor 目录已存在，跳过安装"
fi

# 3. 设置权限
chmod +x artisan
chmod -R 775 storage bootstrap/cache

# 4. 配置环境
if [ ! -f ".env" ]; then
    cp .env.example .env
    php artisan key:generate
    echo "请编辑 .env 文件配置数据库"
else
    echo ".env 文件已存在"
fi

# 5. 验证安装
php artisan --version

echo "✅ 安装完成！"
echo "下一步："
echo "1. 编辑 .env 文件配置数据库"
echo "2. 配置 Web 服务器指向 /public 目录"
echo "3. 访问网站测试"
```

给脚本执行权限并运行：
```bash
chmod +x install.sh
./install.sh
```

## 📞 技术支持

### 获取帮助信息
```bash
# 运行诊断脚本
./server_test.sh

# 查看 PHP 信息
php -i | grep -E "(version|memory|opcache)"

# 查看错误日志
tail -f /var/log/nginx/error.log
tail -f storage/logs/laravel.log
```

### 需要提供的信息
1. `./server_test.sh` 的输出
2. 错误信息截图
3. PHP 版本：`php -v`
4. Composer 版本：`composer --version`
5. 操作系统：`cat /etc/os-release`

---

**最后更新**: 2026-03-08  
**项目状态**: Laravel 11 转换完成，核心文件已修复  
**GitHub**: https://github.com/doney0318/mmtech-website  

💡 **提示**: 安装完成后，运行 `php artisan --version` 验证安装成功。