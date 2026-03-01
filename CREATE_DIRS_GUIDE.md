# 🚀 MMTech Laravel 11 目录创建指南

## 📋 当前问题

根据测试结果，服务器存在以下目录问题：

### ❌ 缺失的目录
1. **storage 目录不存在** - Laravel 存储目录
2. **bootstrap/cache 目录不存在** - 缓存目录

### ✅ 已正常的部分
- PHP 版本：8.2.28 ✅
- 所有必需扩展已安装 ✅
- Nginx 伪静态规则已配置 ✅

## 🔧 修复步骤（仅创建目录）

### 步骤 1：更新代码
```bash
cd /www/wwwroot/mmtech.ltd/mmtech-website
git pull origin main
```

### 步骤 2：创建目录
```bash
cd /www/wwwroot/mmtech.ltd/mmtech-website

# 方法 A：使用脚本
chmod +x create-laravel-dirs.sh
./create-laravel-dirs.sh

# 方法 B：手动创建
mkdir -p storage storage/framework storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
mkdir -p bootstrap/cache
```

### 步骤 3：设置权限（如果需要）
```bash
# 宝塔面板通常使用 www:www 用户组
chown -R www:www storage bootstrap/cache
chmod -R 755 storage bootstrap/cache

# 如果需要写入权限
chmod -R 777 storage/framework/sessions storage/framework/views
```

### 步骤 4：验证修复
```bash
# 测试目录是否可写
cd /www/wwwroot/mmtech.ltd/mmtech-website
php -r "
\$dirs = ['storage', 'bootstrap/cache'];
foreach (\$dirs as \$dir) {
    if (!is_dir(\$dir)) {
        echo \"❌ \$dir: 目录不存在\\n\";
    } else {
        echo is_writable(\$dir) ? \"✅ \$dir: 可写\\n\" : \"⚠️  \$dir: 不可写\\n\";
    }
}
"
```

### 步骤 5：继续安装
访问：https://mmtech.ltd/install/

## 📁 目录结构说明

### Laravel 11 必需目录
```
mmtech-website/
├── storage/                    # 存储目录
│   ├── framework/             # 框架文件
│   │   ├── cache/             # 缓存文件
│   │   ├── sessions/          # 会话文件（需要可写）
│   │   └── views/             # 编译的视图文件（需要可写）
│   └── logs/                  # 日志文件
└── bootstrap/
    └── cache/                 # 引导缓存（需要可写）
```

### 权限要求
- **storage/** - 可写（775 或 777）
- **bootstrap/cache/** - 可写（775 或 777）
- **storage/framework/sessions/** - 可写（777）
- **storage/framework/views/** - 可写（777）

## 🔍 快速测试

### 测试 1：PHP 环境
```bash
curl https://mmtech.ltd/install/test_simple.php
```
**预期输出**：
```
fileinfo: ✅ 已加载
storage 目录: ✅ 可写
bootstrap/cache 目录: ✅ 可写
```

### 测试 2：环境检测
```bash
curl https://mmtech.ltd/install/install_check.php
```
**预期输出**：JSON 格式的检测结果

### 测试 3：目录验证
```bash
cd /www/wwwroot/mmtech.ltd/mmtech-website
ls -la storage/
ls -la bootstrap/cache/
```

## 🎯 问题诊断

### 如果目录创建失败
```bash
# 检查当前用户
whoami

# 检查目录权限
ls -la /www/wwwroot/mmtech.ltd/

# 使用 sudo 创建（如果需要）
sudo mkdir -p /www/wwwroot/mmtech.ltd/mmtech-website/storage
sudo mkdir -p /www/wwwroot/mmtech.ltd/mmtech-website/bootstrap/cache
```

### 如果权限设置失败
```bash
# 检查当前用户组
id

# 设置宝塔面板标准权限
sudo chown -R www:www /www/wwwroot/mmtech.ltd/mmtech-website/storage
sudo chown -R www:www /www/wwwroot/mmtech.ltd/mmtech-website/bootstrap/cache
sudo chmod -R 755 /www/wwwroot/mmtech.ltd/mmtech-website/storage
sudo chmod -R 755 /www/wwwroot/mmtech.ltd/mmtech-website/bootstrap/cache
```

## 📞 快速修复命令

### 一键修复（在项目根目录）
```bash
# 创建目录
mkdir -p storage storage/framework storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
mkdir -p bootstrap/cache

# 设置权限
chmod -R 777 storage bootstrap/cache

# 验证
ls -la storage/
ls -la bootstrap/cache/
```

### 验证修复
```bash
# 运行验证脚本
php -r "
echo '📋 环境验证:\\n';
echo 'PHP 版本: ' . PHP_VERSION . '\\n';

\$exts = ['pdo_mysql', 'mbstring', 'openssl', 'json', 'curl', 'fileinfo'];
foreach (\$exts as \$ext) {
    echo \$ext . ': ' . (extension_loaded(\$ext) ? '✅' : '❌') . '\\n';
}

echo '\\n📁 目录验证:\\n';
\$dirs = ['storage', 'bootstrap/cache'];
foreach (\$dirs as \$dir) {
    if (!is_dir(\$dir)) {
        echo \$dir . ': ❌ 目录不存在\\n';
    } else {
        echo \$dir . ': ' . (is_writable(\$dir) ? '✅ 可写' : '❌ 不可写') . '\\n';
    }
}
"
```

---

**最后更新**：2026-03-01  
**状态**：等待目录创建  
**预计修复时间**：2-3分钟  
**核心问题**：仅需创建 storage/ 和 bootstrap/cache/ 目录