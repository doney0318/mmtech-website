# 🚀 MMTech Laravel 11 快速安装指南

## 📋 当前问题诊断

根据测试结果，服务器存在以下问题：

### ❌ 需要修复的问题
1. **fileinfo 扩展未安装** - Laravel 必需扩展
2. **目录缺失** - storage 和 bootstrap/cache 目录不存在
3. **权限问题** - 目录可能不可写

### ✅ 已正常的部分
- PHP 版本：8.2.28 ✅
- 核心扩展：pdo_mysql, mbstring, openssl, json, curl ✅
- Nginx 伪静态规则已配置 ✅

## 🔧 修复步骤

### 步骤 1：更新代码
```bash
cd /www/wwwroot/mmtech.ltd/mmtech-website
git pull origin main
```

### 步骤 2：运行修复脚本
```bash
cd /www/wwwroot/mmtech.ltd/mmtech-website
chmod +x fix-laravel-permissions.sh
./fix-laravel-permissions.sh
```

### 步骤 3：安装 fileinfo 扩展（宝塔面板）

**方法 A：宝塔面板图形界面**
1. 登录宝塔面板
2. 点击左侧「软件商店」
3. 找到「PHP-8.2」
4. 点击「设置」
5. 点击「安装扩展」
6. 找到「fileinfo」并点击安装
7. 重启 PHP-FPM

**方法 B：命令行安装**
```bash
# Ubuntu/Debian
sudo apt-get update
sudo apt-get install php8.2-fileinfo
sudo systemctl restart php8.2-fpm

# CentOS/RHEL
sudo yum install php-fileinfo
sudo systemctl restart php-fpm
```

### 步骤 4：验证修复
```bash
# 测试 PHP 扩展
cd /www/wwwroot/mmtech.ltd/mmtech-website/public/install
php test_simple.php
```

**预期输出**：
```
fileinfo: ✅ 已加载
storage 目录: ✅ 可写
bootstrap/cache 目录: ✅ 可写
```

### 步骤 5：继续安装
访问：https://mmtech.ltd/install/

## 📁 目录权限参考

### Laravel 11 所需目录权限
```
storage/                         775
storage/framework/               775
storage/framework/cache/         775
storage/framework/sessions/      777
storage/framework/views/         777
storage/logs/                    775
bootstrap/cache/                 775
```

### 宝塔面板特殊设置
```bash
# 设置正确的用户组
chown -R www:www storage bootstrap/cache
chmod -R 755 storage bootstrap/cache
```

## 🔍 故障排除

### 问题 1：fileinfo 扩展安装失败
```bash
# 检查 PHP 配置
php --ini

# 检查已加载的扩展
php -m | grep fileinfo

# 手动启用扩展
echo "extension=fileinfo.so" >> /www/server/php/82/etc/php.ini
systemctl restart php-fpm-82
```

### 问题 2：目录权限问题
```bash
# 检查当前权限
ls -la storage/
ls -la bootstrap/cache/

# 修复权限
chmod -R 777 storage bootstrap/cache
chown -R www:www storage bootstrap/cache
```

### 问题 3：Nginx 配置问题
```nginx
# 检查 Nginx 配置
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/tmp/php-cgi-82.sock;
    fastcgi_index index.php;
    include fastcgi.conf;
}
```

## 🎯 快速测试命令

```bash
# 测试 PHP 环境
curl https://mmtech.ltd/install/test_simple.php

# 测试安装检查
curl https://mmtech.ltd/install/install_check.php

# 测试数据库连接
curl -X POST https://mmtech.ltd/install/install_test_db.php \
  -H "Content-Type: application/json" \
  -d '{"db_host":"127.0.0.1","db_port":"3306","db_name":"mmtech_laravel","db_user":"root","db_pass":"你的密码"}'
```

## 📞 技术支持

### 如果还有问题
1. **查看 PHP 错误日志**
   ```bash
   tail -50 /www/wwwlogs/php-fpm.log
   ```

2. **查看 Nginx 错误日志**
   ```bash
   tail -50 /www/wwwlogs/mmtech.ltd.error.log
   ```

3. **检查 PHP-FPM 状态**
   ```bash
   systemctl status php-fpm-82
   ```

4. **检查目录结构**
   ```bash
   ls -la /www/wwwroot/mmtech.ltd/mmtech-website/
   ```

### 紧急联系方式
- 问题描述：安装页面环境检测卡住
- 当前状态：PHP 8.2.28 正常，缺少 fileinfo 扩展
- 修复方案：安装 fileinfo，创建缺失目录

---

**最后更新**：2026-03-01  
**状态**：等待服务器修复  
**预计修复时间**：5-10分钟