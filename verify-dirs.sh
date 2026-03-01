#!/bin/bash
# verify-dirs.sh - 验证目录结构和权限

echo "🔍 验证 Laravel 目录结构..."
echo "======================================"

# 当前目录
CURRENT_DIR=$(pwd)
echo "当前目录: $CURRENT_DIR"

# 检查目录是否存在
echo ""
echo "📁 检查目录是否存在:"

check_dir() {
    local dir="$1"
    local name="$2"
    
    if [ -d "$dir" ]; then
        echo "✅ $name: 存在 ($dir)"
        
        # 检查权限
        if [ -w "$dir" ]; then
            echo "   ✅ 可写"
        else
            echo "   ❌ 不可写"
        fi
        
        # 显示权限
        echo "   权限: $(stat -c "%a %U:%G" "$dir")"
    else
        echo "❌ $name: 不存在 ($dir)"
    fi
}

# 检查关键目录
check_dir "storage" "storage 目录"
check_dir "storage/framework" "storage/framework 目录"
check_dir "storage/framework/cache" "storage/framework/cache 目录"
check_dir "storage/framework/sessions" "storage/framework/sessions 目录"
check_dir "storage/framework/views" "storage/framework/views 目录"
check_dir "storage/logs" "storage/logs 目录"
check_dir "bootstrap/cache" "bootstrap/cache 目录"

# 检查安装目录
echo ""
echo "📂 检查安装目录:"
if [ -d "public/install" ]; then
    echo "✅ public/install 目录存在"
    ls -la public/install/
else
    echo "❌ public/install 目录不存在"
fi

# PHP 环境检查
echo ""
echo "🌐 PHP 环境检查:"
php -r "
echo 'PHP 版本: ' . PHP_VERSION . \"\\n\";

\$exts = ['pdo_mysql', 'mbstring', 'openssl', 'json', 'curl', 'fileinfo'];
foreach (\$exts as \$ext) {
    echo \$ext . ': ' . (extension_loaded(\$ext) ? '✅' : '❌') . \"\\n\";
}

echo \"\\n目录可写性检查:\\n\";
\$dirs = ['storage', 'bootstrap/cache'];
foreach (\$dirs as \$dir) {
    if (!is_dir(\$dir)) {
        echo \$dir . ': ❌ 目录不存在\\n';
    } else {
        echo \$dir . ': ' . (is_writable(\$dir) ? '✅ 可写' : '❌ 不可写') . \"\\n\";
    }
}
"

# 测试安装检查
echo ""
echo "🔧 测试安装检查接口:"
if [ -f "public/install/install_check.php" ]; then
    php public/install/install_check.php | head -20
else
    echo "❌ install_check.php 不存在"
fi

echo ""
echo "======================================"
echo "📋 总结:"
echo ""
echo "如果目录存在但不可写，请执行:"
echo "chmod -R 777 storage bootstrap/cache"
echo "chown -R www:www storage bootstrap/cache"
echo ""
echo "💡 快速测试:"
echo "curl https://mmtech.ltd/install/test_simple.php"
echo "curl https://mmtech.ltd/install/install_check.php"