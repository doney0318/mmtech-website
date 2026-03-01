<?php
/**
 * Laravel 11 数据库连接测试
 */
error_reporting(0);
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$host = $input['db_host'] ?? '127.0.0.1';
$port = $input['db_port'] ?? '3306';
$dbname = $input['db_name'] ?? 'mmtech_laravel';
$username = $input['db_user'] ?? 'root';
$password = $input['db_pass'] ?? '';
$prefix = $input['db_prefix'] ?? 'mm_';

try {
    // 测试数据库连接
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // 检查数据库是否存在，不存在则创建
    $stmt = $pdo->query("SHOW DATABASES LIKE '{$dbname}'");
    if ($stmt->rowCount() === 0) {
        $pdo->exec("CREATE DATABASE `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }
    
    // 测试连接到目标数据库
    $pdo->exec("USE `{$dbname}`");
    
    // 测试文件权限（Laravel 关键目录）
    $pathsToCheck = [
        '../storage/app/public' => 'storage/app/public',
        '../storage/framework/cache' => 'storage/framework/cache',
        '../storage/framework/sessions' => 'storage/framework/sessions',
        '../storage/framework/views' => 'storage/framework/views',
        '../storage/logs' => 'storage/logs',
        '../bootstrap/cache' => 'bootstrap/cache'
    ];
    
    $writablePaths = [];
    foreach ($pathsToCheck as $realPath => $displayPath) {
        $fullPath = __DIR__ . '/' . $realPath;
        if (!is_dir($fullPath)) {
            @mkdir($fullPath, 0755, true);
        }
        $writablePaths[$displayPath] = is_writable($fullPath);
    }
    
    // 检查 Composer 依赖
    $composerPath = __DIR__ . '/../vendor/autoload.php';
    $composerExists = file_exists($composerPath);
    
    // 保存配置到临时文件
    $configData = [
        'db_host' => $host,
        'db_port' => $port,
        'db_name' => $dbname,
        'db_user' => $username,
        'db_pass' => $password,
        'db_prefix' => $prefix,
        'tested_at' => time()
    ];
    
    $tempConfigFile = __DIR__ . '/.db_config_temp.json';
    file_put_contents($tempConfigFile, json_encode($configData));
    chmod($tempConfigFile, 0600);
    
    echo json_encode([
        'success' => true,
        'message' => '数据库连接成功，可以继续安装',
        'database_info' => [
            'host' => $host,
            'port' => $port,
            'database' => $dbname,
            'username' => $username,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci'
        ],
        'paths_writable' => $writablePaths,
        'composer_available' => $composerExists,
        'ready_for_install' => true
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => '数据库连接失败：' . $e->getMessage(),
        'pdo_error_info' => $e->errorInfo ?? [],
        'suggestions' => [
            '请检查数据库连接信息是否正确',
            '确认数据库服务正在运行',
            '验证用户名和密码权限'
        ]
    ]);
}
