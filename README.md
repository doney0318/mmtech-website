# 🚀 MMTech 企业网站系统 - Laravel 11

![Laravel Version](https://img.shields.io/badge/Laravel-11.x-red.svg)
![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

现代化的企业网站系统，基于 Laravel 11 开发，支持中英文双语，包含完整的前后台管理系统。

## ✨ 特性

- 🎨 **现代化设计** - 响应式布局，美观的企业级界面
- 🌐 **双语支持** - 完整的中英文内容管理
- 🔐 **安全可靠** - Laravel 11 安全框架，密码哈希加密
- 📱 **移动友好** - 完美适配手机、平板、桌面设备
- 🚀 **快速安装** - 4步安装向导，10分钟完成部署
- 📊 **后台管理** - 完整的 CRUD 管理系统
- 🔧 **扩展性强** - 模块化设计，易于二次开发

## 📋 系统要求

### 服务器要求
- **PHP**: 8.2 或更高版本
- **数据库**: MySQL 5.7+ / MariaDB 10.3+
- **Web 服务器**: Nginx / Apache
- **Composer**: 2.0 或更高版本 (依赖管理)
- **扩展要求**:
  - PDO MySQL 扩展
  - OpenSSL 扩展
  - Mbstring 扩展
  - JSON 扩展
  - cURL 扩展
  - Fileinfo 扩展
  - Bcmath 扩展
  - XML 扩展
  - Tokenizer 扩展

### 推荐环境
- **操作系统**: Ubuntu 22.04 / CentOS 8 / Debian 11
- **内存**: 1GB 或更高
- **存储**: 至少 2GB 可用空间
- **PHP 内存限制**: 至少 256M

## 🚀 快速开始

### ⚠️ 重要：项目状态说明
当前项目已从 ThinkPHP 8.0 转换为 Laravel 11，但缺少 `vendor` 目录和部分缓存文件。首次安装时需要运行 `composer install` 来生成这些文件。

### 第一步：获取代码

#### 方式一：Git 克隆（推荐）
```bash
# 克隆项目
git clone https://github.com/doney0318/mmtech-website.git

# 进入项目目录
cd mmtech-website

# 运行测试脚本检查环境
chmod +x server_test.sh
./server_test.sh
```

#### 方式二：下载 ZIP
1. 访问 [GitHub Releases](https://github.com/doney0318/mmtech-website/releases)
2. 下载最新版本的 ZIP 文件
3. 解压到网站目录

### 第二步：服务器准备

#### 1. 创建数据库
```bash
# 登录 MySQL
mysql -u root -p

# 创建数据库
CREATE DATABASE mmtech_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 创建用户（可选）
CREATE USER 'mmtech_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON mmtech_laravel.* TO 'mmtech_user'@'localhost';
FLUSH PRIVILEGES;

# 退出
EXIT;
```

#### 2. 安装 Composer 依赖（关键步骤）
```bash
# 安装 Composer (如果未安装)
# macOS: brew install composer
# Ubuntu/Debian: sudo apt install composer
# CentOS/RHEL: sudo yum install composer

# 重要：当前项目缺少 vendor 目录，需要首次安装
# 安装项目依赖（使用优化模式）
composer install --no-dev --optimize-autoloader

# 如果出现 "Could not open input file: artisan" 错误
# 确保 artisan 文件有执行权限
chmod +x artisan

# 如果使用中国网络，可以使用镜像加速
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
composer clear-cache
composer install --no-dev --optimize-autoloader
```

#### 3. 设置目录权限
```bash
# 设置存储目录权限
chmod -R 775 storage bootstrap/cache

# 设置上传目录权限
chmod -R 775 public/uploads

# 设置文件所有者（宝塔面板通常使用 www）
chown -R www:www storage bootstrap/cache public/uploads
```

### 第三步：运行安装向导

#### 1. 访问安装页面
```
http://your-domain.com/install/
```
或
```
http://your-ip-address/install/
```

#### 2. 按照向导完成安装

**步骤 1：环境检测**
- 系统会自动检测 PHP 版本、扩展、目录权限
- 所有检测通过后点击"下一步"

**步骤 2：数据库配置**
```
数据库主机: 127.0.0.1
数据库端口: 3306
数据库名称: mmtech_laravel
数据库用户: root (或您创建的用户)
数据库密码: 您的数据库密码
表前缀: mm_ (默认)
```

**步骤 3：管理员设置**
```
管理员用户名: admin (可修改)
管理员密码: 设置强密码
确认密码: 再次输入密码
管理员邮箱: admin@your-domain.com
```

**步骤 4：完成安装**
- 系统会自动创建数据库表
- 插入基础数据
- 生成配置文件
- 创建管理员账号

### 第四步：安装完成

#### 1. 删除安装目录（重要！）
```bash
# 安装完成后立即删除安装目录
rm -rf public/install/
```

#### 2. 访问网站
- **前台首页**: `http://your-domain.com/`
- **后台登录**: `http://your-domain.com/admin/login`
- **管理员账号**: 安装时设置的用户名和密码

## 🔧 高级配置

### Nginx 配置示例
```nginx
server {
    listen 80;
    server_name mmtech.ltd www.mmtech.ltd;
    root /www/wwwroot/mmtech.ltd/mmtech-website/public;
    index index.php index.html index.htm;
    
    # Laravel URL 重写
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # PHP 处理
    location ~ \.php$ {
        fastcgi_pass unix:/tmp/php-cgi-82.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        
        # 超时设置
        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
        fastcgi_read_timeout 300;
    }
    
    # 禁止访问敏感文件
    location ~ /\. {
        deny all;
    }
    
    location ~* \.(sql|log|ini|sh)$ {
        deny all;
    }
    
    access_log /www/wwwlogs/mmtech.ltd.log;
    error_log /www/wwwlogs/mmtech.ltd.error.log;
}
```

### Apache 配置 (.htaccess)
项目已包含标准的 Laravel .htaccess 文件，确保 `mod_rewrite` 已启用。

### 宝塔面板配置
1. 添加网站，选择 PHP 8.2
2. 网站目录指向 `/public`
3. 伪静态选择 `Laravel`
4. 禁用防跨站攻击(open_basedir)

## 📁 项目结构

```
mmtech-website/
├── app/                          # 应用核心
│   ├── Http/Controllers/        # 控制器
│   ├── Models/                  # 数据模型
│   └── Services/               # 业务逻辑
├── database/                    # 数据库
│   ├── migrations/             # 迁移文件
│   └── seeders/                # 数据填充
├── public/                      # 网站根目录
│   ├── css/                    # 样式文件
│   ├── js/                     # 脚本文件
│   └── uploads/                # 上传目录
├── resources/                   # 资源文件
│   ├── views/                  # 视图模板
│   └── lang/                   # 多语言文件
├── routes/                      # 路由定义
├── storage/                     # 存储目录
├── config/                      # 配置文件
└── vendor/                      # Composer 依赖
```

## 🔐 后台管理

### 功能模块
1. **服务管理** - 添加、编辑、删除服务项目
2. **案例管理** - 管理项目案例，支持图片上传
3. **文章管理** - 发布技术文章，支持分类标签
4. **留言管理** - 查看和回复客户留言
5. **配置管理** - 网站基础配置
6. **导航管理** - 管理网站导航菜单

### 访问方式
```
后台地址: http://your-domain.com/admin/login
默认账号: 安装时设置的管理员账号
```

## 🌐 双语支持

### 语言切换
- 前台页面右上角提供语言切换按钮
- 后台内容支持中英文分别编辑
- 数据库设计支持双语字段

### 添加新语言
1. 在 `resources/lang/` 创建语言目录
2. 复制现有语言文件并翻译
3. 更新语言切换器

## 🚨 故障排除

### 当前项目特定问题

#### 0. "Could not open input file: artisan"（当前项目最常见）
**原因**: 项目从 ThinkPHP 转换为 Laravel，缺少核心文件
**解决**:
```bash
# 1. 确保 artisan 文件存在且有执行权限
ls -la artisan
chmod +x artisan

# 2. 如果 artisan 不存在，从 GitHub 重新拉取
git pull origin main

# 3. 安装 Composer 依赖（关键步骤）
rm -rf vendor composer.lock
composer install --no-dev --optimize-autoloader

# 4. 验证安装
php artisan --version
```

### 常见问题

#### 1. 安装页面无法访问
```bash
# 检查 Nginx/Apache 配置
# 确认网站根目录指向 /public
# 检查 PHP-FPM 是否运行
systemctl status php-fpm
```

#### 2. 数据库连接失败
```bash
# 测试数据库连接
mysql -u username -p -h 127.0.0.1

# 检查数据库权限
SHOW GRANTS FOR 'username'@'localhost';
```

#### 3. 500 内部服务器错误
```bash
# 查看错误日志
tail -f /www/wwwlogs/mmtech.ltd.error.log

# 检查文件权限
ls -la storage bootstrap/cache

# 检查 .env 文件
cat .env | grep DB_
```

#### 4. 样式和脚本加载失败
```bash
# 检查 Nginx 配置
# 确认静态文件目录权限
chmod -R 755 public/css public/js

# 清除缓存
php artisan cache:clear
php artisan view:clear
```

### 调试模式
如需开启调试模式，编辑 `.env` 文件：
```env
APP_DEBUG=true
```

**注意**：生产环境请务必设置为 `false`

## 🔄 更新与维护

### 更新代码
```bash
# 拉取最新代码
git pull origin main

# 更新依赖
composer install --no-dev --optimize-autoloader

# 清理缓存
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 备份数据
```bash
# 备份数据库
mysqldump -u username -p mmtech_laravel > backup_$(date +%Y%m%d).sql

# 备份上传文件
tar -czf uploads_backup_$(date +%Y%m%d).tar.gz public/uploads/
```

### 恢复备份
```bash
# 恢复数据库
mysql -u username -p mmtech_laravel < backup_20260301.sql

# 恢复上传文件
tar -xzf uploads_backup_20260301.tar.gz -C public/
```

## 📈 性能优化

### 生产环境优化
```bash
# 优化自动加载
composer install --optimize-autoloader --no-dev

# 缓存路由
php artisan route:cache

# 缓存配置
php artisan config:cache

# 缓存视图
php artisan view:cache
```

### 启用 OPcache
在 `php.ini` 中启用：
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
```

## 🔧 开发指南

### 本地开发环境
```bash
# 安装开发依赖
composer install

# 生成应用密钥
php artisan key:generate

# 运行数据库迁移
php artisan migrate

# 启动开发服务器
php artisan serve
```

### 添加新功能
1. 创建数据库迁移：`php artisan make:migration create_table_name`
2. 创建模型：`php artisan make:model ModelName`
3. 创建控制器：`php artisan make:controller ControllerName`
4. 创建视图：在 `resources/views/` 添加模板文件
5. 定义路由：在 `routes/web.php` 添加路由

## 🔧 Composer 配置说明

### 关于 composer.json
本项目已包含完整的 `composer.json` 配置文件，定义了 Laravel 11 项目的所有依赖。

#### 主要依赖
- **laravel/framework**: ^11.0 (Laravel 11 核心框架)
- **laravel/sanctum**: ^4.0 (API 认证)
- **laravel/tinker**: ^2.9 (交互式命令行)
- **guzzlehttp/guzzle**: ^7.9 (HTTP 客户端)

#### 开发依赖
- **phpunit/phpunit**: ^10.5 (单元测试)
- **fakerphp/faker**: ^1.23 (测试数据生成)
- **laravel/pint**: ^1.13 (代码格式化)

### 安装 Composer

#### macOS
```bash
brew install php@8.2
brew install composer
```

#### Ubuntu/Debian
```bash
sudo apt update
sudo apt install php8.2 php8.2-{mbstring,xml,curl,mysql,zip,gd,bcmath} composer
```

#### CentOS/RHEL
```bash
sudo yum install epel-release
sudo yum install php82 php82-php-{mbstring,xml,curl,mysqlnd,zip,gd,bcmath} composer
```

### 安装项目依赖
```bash
# 进入项目目录
cd mmtech-website

# 安装依赖
composer install

# 使用中国镜像加速 (可选)
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
composer clear-cache
composer install
```

### 常见 Composer 问题

#### 1. 内存限制错误
```bash
# 临时增加内存限制
COMPOSER_MEMORY_LIMIT=-1 composer install

# 或修改 php.ini
memory_limit = 2G
```

#### 2. 扩展缺失错误
```bash
# 检查已安装的扩展
php -m

# 安装缺失的扩展 (Ubuntu 示例)
sudo apt install php8.2-{mbstring,xml,curl,mysql,zip,gd,bcmath}
```

#### 3. 版本冲突
```bash
# 更新依赖版本
composer update

# 或指定特定版本
composer require laravel/framework:11.0.0
```

### 验证安装
```bash
# 检查 PHP 版本
php --version

# 检查 Composer
composer --version

# 检查 Laravel
php artisan --version

# 检查依赖
composer show
```

### 更多信息
- 详细配置指南: [COMPOSER_SETUP_GUIDE.md](COMPOSER_SETUP_GUIDE.md)
- 项目检查脚本: `./check_composer_setup.sh`
- Composer 官方文档: https://getcomposer.org/doc/

## 📞 支持与帮助

### 文档资源
- [Laravel 11 官方文档](https://laravel.com/docs/11.x)
- [GitHub Issues](https://github.com/doney0318/mmtech-website/issues)
- [安装视频教程](https://example.com/tutorial)

### 问题反馈
1. 检查 [常见问题](#常见问题) 部分
2. 查看错误日志获取详细信息
3. 在 GitHub Issues 提交问题
4. 提供以下信息：
   - 错误信息
   - PHP 版本
   - 数据库版本
   - 错误截图

### 社区支持
- Laravel 中文社区
- PHP 开发者论坛
- GitHub Discussions

## 📄 许可证

本项目采用 MIT 许可证。详见 [LICENSE](LICENSE) 文件。

## 🙏 致谢

- [Laravel](https://laravel.com) - 优秀的 PHP 框架
- [Bootstrap](https://getbootstrap.com) - 前端框架
- [Font Awesome](https://fontawesome.com) - 图标库
- 所有贡献者和用户

---

**最后更新**: 2026-03-08  
**版本**: 1.1.0 (Laravel 11 转换版)  
**状态**: 核心文件已修复，待测试安装  

💡 **提示**: 当前项目已从 ThinkPHP 转换为 Laravel 11，首次安装需要运行 `composer install`。安装过程中遇到任何问题，请参考故障排除部分或运行 `./server_test.sh` 获取诊断信息。
## 🚀 服务器快速安装（针对当前项目状态）

### ⚠️ 重要说明
当前项目已从 ThinkPHP 8.0 转换为 Laravel 11，但缺少一些核心文件。请按以下步骤安装：

### 1. 克隆项目并检查
```bash
# 克隆项目
cd /root
git clone https://github.com/doney0318/mmtech-website.git
cd mmtech-website

# 运行测试脚本检查环境
chmod +x server_test.sh
./server_test.sh
```

### 2. 安装 Composer 依赖（关键步骤）
```bash
# 如果 vendor 目录不存在，需要安装依赖
if [ ! -d "vendor" ]; then
    echo "安装 Composer 依赖..."
    composer install --no-dev --optimize-autoloader
else
    echo "vendor 目录已存在，跳过安装"
fi

# 如果出现 "Could not open input file: artisan" 错误
# 确保 artisan 文件存在且有执行权限
chmod +x artisan
```

### 3. 验证安装
```bash
# 测试 Laravel 是否正常工作
php artisan --version

# 如果出现错误，尝试重新安装
rm -rf vendor composer.lock
composer install --no-dev --optimize-autoloader
```

### 4. 配置环境
```bash
# 复制环境配置
cp .env.example .env

# 生成应用密钥
php artisan key:generate

# 编辑 .env 文件配置数据库
# nano .env 或 vim .env
```

### 5. 设置权限
```bash
# 设置存储目录权限
chmod -R 775 storage bootstrap/cache

# 设置所有者（根据你的 Web 服务器用户）
# 如果是 Nginx + PHP-FPM，通常是 www-data
chown -R www-data:www-data storage bootstrap/cache
```

## 🔧 当前项目状态说明

### ✅ 已修复的问题
1. **artisan 文件缺失** - 已添加
2. **bootstrap/app.php 缺失** - 已添加
3. **.gitignore 配置** - 已添加，排除 vendor/ 等目录

### ⚠️ 需要注意
1. **vendor 目录** - 需要首次运行 `composer install` 生成
2. **composer.lock** - 会在安装依赖后自动生成
3. **数据库配置** - 需要手动配置 .env 文件

### 📋 安装检查清单
- [ ] 运行 `./server_test.sh` 检查环境
- [ ] 运行 `composer install` 安装依赖
- [ ] 配置 `.env` 文件中的数据库连接
- [ ] 运行 `php artisan key:generate`
- [ ] 设置目录权限
- [ ] 测试 `php artisan --version`

## 🚨 常见问题解决方案

### 问题1: "Could not open input file: artisan"
**原因**: artisan 文件缺失或权限不正确
**解决**:
```bash
# 检查文件是否存在
ls -la artisan

# 如果不存在，从 GitHub 重新拉取
git pull origin main

# 设置执行权限
chmod +x artisan

# 重新安装依赖
rm -rf vendor composer.lock
composer install --no-dev --optimize-autoloader
```

### 问题2: Composer 安装失败
**解决**:
```bash
# 使用中国镜像加速
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

# 清除缓存
composer clear-cache

# 重新安装
composer install --no-dev --optimize-autoloader
```

### 问题3: PHP 版本不兼容
**要求**: PHP 8.2 或更高版本
**检查**:
```bash
php -v

# 如果版本过低，升级 PHP
# Ubuntu/Debian: sudo apt install php8.2
# CentOS/RHEL: sudo yum install php82
```

## 📞 技术支持

### 快速诊断
运行以下命令获取详细诊断信息：
```bash
./server_test.sh
```

### 获取帮助
1. 查看本文档的故障排除部分
2. 检查 GitHub Issues
3. 提供以下信息寻求帮助：
   - `./server_test.sh` 的输出
   - 错误信息截图
   - PHP 版本 (`php -v`)
   - Composer 版本 (`composer --version`)

### 项目状态更新
- **最后更新**: 2026-03-08
- **框架**: Laravel 11
- **状态**: 已修复核心文件，待测试安装
- **GitHub**: https://github.com/doney0318/mmtech-website
