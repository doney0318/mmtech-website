<?php
// 简单的测试文件，检查 PHP 是否工作
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP 测试页面\n";
echo "============\n\n";

// 基本信息
echo "PHP 版本: " . PHP_VERSION . "\n";
echo "当前文件: " . __FILE__ . "\n";
echo "当前目录: " . __DIR__ . "\n\n";

// 检查关键扩展
$extensions = ['pdo_mysql', 'mbstring', 'openssl', 'json', 'curl', 'fileinfo'];
foreach ($extensions as $ext) {
    echo $ext . ": " . (extension_loaded($ext) ? "✅ 已加载" : "❌ 未加载") . "\n";
}

echo "\n";

// 检查目录权限
$dirs = [
    '../storage' => 'storage 目录',
    '../bootstrap/cache' => 'bootstrap/cache 目录'
];

foreach ($dirs as $path => $name) {
    $fullPath = __DIR__ . '/' . $path;
    if (!is_dir($fullPath)) {
        echo $name . ": ❌ 目录不存在\n";
    } else {
        echo $name . ": " . (is_writable($fullPath) ? "✅ 可写" : "❌ 不可写") . "\n";
    }
}

echo "\n";

// 测试数据库连接（如果提供参数）
if (isset($_GET['test_db'])) {
    echo "数据库连接测试:\n";
    try {
        $host = $_GET['host'] ?? '127.0.0.1';
        $port = $_GET['port'] ?? '3306';
        $user = $_GET['user'] ?? 'root';
        $pass = $_GET['pass'] ?? '';
        
        $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        echo "✅ 数据库连接成功\n";
        
        // 显示数据库版本
        $version = $pdo->query('SELECT VERSION()')->fetchColumn();
        echo "MySQL 版本: " . $version . "\n";
        
    } catch (PDOException $e) {
        echo "❌ 数据库连接失败: " . $e->getMessage() . "\n";
    }
}

echo "\n";
echo "测试完成！";
?>