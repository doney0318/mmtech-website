<?php
/**
 * Laravel 11 环境检测
 */
error_reporting(0);
header('Content-Type: application/json');

$requirements = [];
$passed = true;

// PHP 版本检测
$phpVersion = PHP_VERSION;
$phpRequired = '8.2.0';
$phpPassed = version_compare($phpVersion, $phpRequired, '>=');
$requirements[] = [
    'name' => 'PHP 版本',
    'value' => $phpVersion,
    'required' => $phpRequired,
    'passed' => $phpPassed
];
if (!$phpPassed) $passed = false;

// 扩展检测
$requiredExtensions = [
    'pdo_mysql' => 'PDO MySQL 扩展',
    'mbstring' => 'Multibyte String 扩展',
    'openssl' => 'OpenSSL 扩展',
    'json' => 'JSON 扩展',
    'curl' => 'cURL 扩展',
    'fileinfo' => 'Fileinfo 扩展'
];

foreach ($requiredExtensions as $ext => $name) {
    $extPassed = extension_loaded($ext);
    $requirements[] = [
        'name' => $name,
        'value' => $extPassed ? '已安装' : '未安装',
        'required' => '必须',
        'passed' => $extPassed
    ];
    if (!$extPassed) $passed = false;
}

// 目录权限检测
$requiredDirs = [
    '../storage' => 'storage 目录',
    '../bootstrap/cache' => 'bootstrap/cache 目录'
];

foreach ($requiredDirs as $dir => $name) {
    $dirPath = __DIR__ . '/' . $dir;
    $writable = is_writable($dirPath);
    $requirements[] = [
        'name' => $name,
        'value' => $writable ? '可写' : '不可写',
        'required' => '可写',
        'passed' => $writable
    ];
    if (!$writable) $passed = false;
}

// Composer 检测
$composerJson = __DIR__ . '/../composer.json';
$composerExists = file_exists($composerJson);
$requirements[] = [
    'name' => 'Composer 配置',
    'value' => $composerExists ? '存在' : '不存在',
    'required' => '存在',
    'passed' => $composerExists
];
if (!$composerExists) $passed = false;

echo json_encode([
    'passed' => $passed,
    'requirements' => $requirements,
    'php_version' => $phpVersion,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? '未知',
    'current_dir' => __DIR__
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
