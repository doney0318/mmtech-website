<?php
/**
 * Laravel 11 安装执行器
 */
error_reporting(0);
header('Content-Type: application/json');
set_time_limit(300); // 5分钟超时

$rawBody = file_get_contents('php://input');
$jsonInput = json_decode($rawBody, true);

// 兼容 JSON / x-www-form-urlencoded / multipart/form-data
$input = is_array($jsonInput) ? $jsonInput : [];
if (empty($input) && !empty($_POST)) {
    $input = $_POST;
}
if (empty($input) && is_string($rawBody) && $rawBody !== '') {
    parse_str($rawBody, $parsedBody);
    if (is_array($parsedBody) && !empty($parsedBody)) {
        $input = $parsedBody;
    }
}
$projectRoot = realpath(__DIR__ . '/../..');

if ($projectRoot === false) {
    echo json_encode(['success' => false, 'message' => '无法定位项目根目录']);
    exit;
}

// 读取临时配置
$tempConfigFile = __DIR__ . '/.db_config_temp.json';
if (file_exists($tempConfigFile)) {
    $tempConfig = json_decode(file_get_contents($tempConfigFile), true);
    if ($tempConfig && isset($tempConfig['tested_at']) && (time() - $tempConfig['tested_at']) < 300) {
        $db = $tempConfig;
    } else {
        $db = $input['db'] ?? [
            'db_host' => $input['db_host'] ?? null,
            'db_port' => $input['db_port'] ?? null,
            'db_name' => $input['db_name'] ?? null,
            'db_user' => $input['db_user'] ?? null,
            'db_pass' => $input['db_pass'] ?? '',
            'db_prefix' => $input['db_prefix'] ?? 'mm_',
        ];
    }
} else {
    $db = $input['db'] ?? [
        'db_host' => $input['db_host'] ?? null,
        'db_port' => $input['db_port'] ?? null,
        'db_name' => $input['db_name'] ?? null,
        'db_user' => $input['db_user'] ?? null,
        'db_pass' => $input['db_pass'] ?? '',
        'db_prefix' => $input['db_prefix'] ?? 'mm_',
    ];
}

$admin = $input['admin'] ?? [
    'admin_username' => $input['admin_username'] ?? null,
    'admin_password' => $input['admin_password'] ?? null,
    'admin_email' => $input['admin_email'] ?? null,
];

$missing = [];
if (empty($db['db_host'])) {
    $missing[] = 'db_host';
}
if (empty($db['db_port'])) {
    $missing[] = 'db_port';
}
if (empty($db['db_name'])) {
    $missing[] = 'db_name';
}
if (empty($db['db_user'])) {
    $missing[] = 'db_user';
}
if (empty($admin['admin_username'])) {
    $missing[] = 'admin_username';
}
if (empty($admin['admin_password'])) {
    $missing[] = 'admin_password';
}
if (empty($admin['admin_email'])) {
    $missing[] = 'admin_email';
}

if (!empty($missing)) {
    echo json_encode([
        'success' => false,
        'message' => '缺少必需的配置信息：' . implode(', ', $missing),
        'debug' => [
            'content_type' => $_SERVER['CONTENT_TYPE'] ?? '',
            'has_raw_body' => !empty($rawBody),
            'input_keys' => array_keys($input),
        ],
    ]);
    exit;
}

try {
    // 步骤 1: 生成 APP_KEY
    $envFile = $projectRoot . '/.env';
    $envExampleFile = $projectRoot . '/.env.example';

    if (!file_exists($envFile)) {
        copy($envExampleFile, $envFile);
    }
    
    // 生成 Laravel 兼容的 APP_KEY（base64: + 32 bytes）
    $appKey = 'base64:' . base64_encode(random_bytes(32));
    
    // 步骤 2: 写入 .env 配置
    $envContent = file_get_contents($envExampleFile);
    $envContent = str_replace([
        'APP_KEY=',
        'DB_HOST=127.0.0.1',
        'DB_PORT=3306',
        'DB_DATABASE=mmtech_laravel',
        'DB_USERNAME=root',
        'DB_PASSWORD=',
        'APP_URL=http://localhost',
        'MMTECH_INSTALLED=false'
    ], [
        'APP_KEY=' . $appKey,
        'DB_HOST=' . $db['db_host'],
        'DB_PORT=' . $db['db_port'],
        'DB_DATABASE=' . $db['db_name'],
        'DB_USERNAME=' . $db['db_user'],
        'DB_PASSWORD=' . $db['db_pass'],
        'APP_URL=' . ((isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'https') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')),
        'MMTECH_INSTALLED=true'
    ], $envContent);
    
    file_put_contents($envFile, $envContent);
    
    // 步骤 3: 数据库连接和迁移
    $dsn = "mysql:host={$db['db_host']};port={$db['db_port']};charset=utf8mb4";
    $pdo = new PDO($dsn, $db['db_user'], $db['db_pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $pdo->exec("USE `{$db['db_name']}`");
    
    // 步骤 4: 执行数据库迁移（手动执行SQL）
    $migrationSQL = getMigrationSQL($db['db_prefix']);
    $pdo->exec($migrationSQL);
    
    // 步骤 5: 创建超级管理员（使用 Hash 加密）
    $passwordHash = password_hash($admin['admin_password'], PASSWORD_DEFAULT);
    $currentTime = date('Y-m-d H:i:s');
    
    $insertAdmin = "INSERT INTO `{$db['db_prefix']}admin` 
        (username, password, email, nickname, role_id, status, created_at, updated_at) 
        VALUES (?, ?, ?, ?, 1, 1, ?, ?)";
    
    $stmt = $pdo->prepare($insertAdmin);
    $stmt->execute([
        $admin['admin_username'],
        $passwordHash,
        $admin['admin_email'],
        '超级管理员',
        $currentTime,
        $currentTime
    ]);
    
    // 步骤 6: 插入基础配置数据
    insertBasicConfigs($pdo, $db['db_prefix']);
    
    // 步骤 7: 插入导航菜单
    insertNavigationMenu($pdo, $db['db_prefix']);
    
    // 步骤 8: 创建示例数据
    insertSampleData($pdo, $db['db_prefix']);
    
    // 步骤 9: 创建安装锁文件
    $lockFile = $projectRoot . '/storage/installed';
    $storageDir = $projectRoot . '/storage';
    if (!is_dir($storageDir)) {
        mkdir($storageDir, 0755, true);
    }
    file_put_contents($lockFile, json_encode([
        'installed_at' => time(),
        'version' => '1.0.0',
        'admin_username' => $admin['admin_username']
    ]));
    
    // 步骤 10: 设置目录权限
    $pathsToSet = [
        $projectRoot . '/storage',
        $projectRoot . '/bootstrap/cache'
    ];

    foreach ($pathsToSet as $fullPath) {
        if (is_dir($fullPath)) {
            setDirectoryPermissions($fullPath);
        }
    }
    
    // 清理临时配置文件
    if (file_exists($tempConfigFile)) {
        @unlink($tempConfigFile);
    }
    
    echo json_encode([
        'success' => true,
        'message' => '安装完成！Laravel 11 系统已成功部署',
        'details' => [
            'database_imported' => true,
            'admin_created' => true,
            'config_written' => true,
            'permissions_set' => true,
            'app_key_generated' => true,
            'lock_file_created' => true
        ],
        'admin_info' => [
            'username' => $admin['admin_username'],
            'email' => $admin['admin_email']
        ],
        'next_steps' => [
            '删除 /public/install/ 目录',
            '访问后台: /admin/login',
            '访问前台: /'
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => '安装失败：' . $e->getMessage(),
        'error_type' => get_class($e),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
}

/**
 * 获取数据库迁移 SQL
 */
function getMigrationSQL($prefix) {
    return "
    -- 管理员表
    CREATE TABLE IF NOT EXISTS `{$prefix}admin` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `username` varchar(50) NOT NULL COMMENT '用户名',
        `password` varchar(255) NOT NULL COMMENT '密码',
        `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
        `nickname` varchar(50) DEFAULT NULL COMMENT '昵称',
        `role_id` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '角色ID',
        `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1正常 0禁用',
        `last_login_ip` varchar(45) DEFAULT NULL COMMENT '最后登录IP',
        `last_login_time` timestamp NULL DEFAULT NULL COMMENT '最后登录时间',
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `deleted_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `mm_admin_username_unique` (`username`),
        KEY `mm_admin_status_index` (`status`),
        KEY `mm_admin_role_id_index` (`role_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    -- 网站配置表
    CREATE TABLE IF NOT EXISTS `{$prefix}config` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `key` varchar(100) NOT NULL COMMENT '配置键',
        `value_zh` text COMMENT '中文配置值',
        `value_en` text COMMENT '英文配置值',
        `group` varchar(50) NOT NULL DEFAULT 'base' COMMENT '分组',
        `type` varchar(20) NOT NULL DEFAULT 'text' COMMENT '类型',
        `title_zh` varchar(100) NOT NULL COMMENT '中文标题',
        `title_en` varchar(100) NOT NULL COMMENT '英文标题',
        `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `mm_config_key_unique` (`key`),
        KEY `mm_config_group_index` (`group`),
        KEY `mm_config_sort_index` (`sort`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    -- 服务项目表
    CREATE TABLE IF NOT EXISTS `{$prefix}service` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `slug` varchar(100) NOT NULL COMMENT 'URL标识',
        `title_zh` varchar(200) NOT NULL COMMENT '中文标题',
        `title_en` varchar(200) NOT NULL COMMENT '英文标题',
        `description_zh` text COMMENT '中文描述',
        `description_en` text COMMENT '英文描述',
        `content_zh` text COMMENT '中文内容',
        `content_en` text COMMENT '英文内容',
        `icon` varchar(100) DEFAULT NULL COMMENT '图标',
        `image` varchar(255) DEFAULT NULL COMMENT '图片',
        `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1显示 0隐藏',
        `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
        `views` int(11) NOT NULL DEFAULT '0' COMMENT '浏览量',
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `mm_service_slug_unique` (`slug`),
        KEY `mm_service_status_index` (`status`),
        KEY `mm_service_sort_index` (`sort`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    -- 项目案例表
    CREATE TABLE IF NOT EXISTS `{$prefix}case` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `title_zh` varchar(200) NOT NULL COMMENT '中文标题',
        `title_en` varchar(200) NOT NULL COMMENT '英文标题',
        `description_zh` text COMMENT '中文描述',
        `description_en` text COMMENT '英文描述',
        `content_zh` text COMMENT '中文内容',
        `content_en` text COMMENT '英文内容',
        `cover_image` varchar(255) DEFAULT NULL COMMENT '封面图',
        `images` json DEFAULT NULL COMMENT '案例图片',
        `client` varchar(100) DEFAULT NULL COMMENT '客户名称',
        `industry` varchar(100) DEFAULT NULL COMMENT '所属行业',
        `project_date` date DEFAULT NULL COMMENT '项目日期',
        `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1显示 0隐藏',
        `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
        `views` int(11) NOT NULL DEFAULT '0' COMMENT '浏览量',
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `mm_case_status_index` (`status`),
        KEY `mm_case_sort_index` (`sort`),
        KEY `mm_case_project_date_index` (`project_date`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    -- 技术文章表
    CREATE TABLE IF NOT EXISTS `{$prefix}article` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `slug` varchar(100) NOT NULL COMMENT 'URL标识',
        `title_zh` varchar(200) NOT NULL COMMENT '中文标题',
        `title_en` varchar(200) NOT NULL COMMENT '英文标题',
        `excerpt_zh` text COMMENT '中文摘要',
        `excerpt_en` text COMMENT '英文摘要',
        `content_zh` text COMMENT '中文内容',
        `content_en` text COMMENT '英文内容',
        `cover_image` varchar(255) DEFAULT NULL COMMENT '封面图',
        `author` varchar(100) DEFAULT NULL COMMENT '作者',
        `category_id` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
        `tags` json DEFAULT NULL COMMENT '标签',
        `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1发布 0草稿',
        `published_at` timestamp NULL DEFAULT NULL COMMENT '发布时间',
        `views` int(11) NOT NULL DEFAULT '0' COMMENT '浏览量',
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `mm_article_slug_unique` (`slug`),
        KEY `mm_article_status_index` (`status`),
        KEY `mm_article_category_id_index` (`category_id`),
        KEY `mm_article_published_at_index` (`published_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    -- 留言咨询表
    CREATE TABLE IF NOT EXISTS `{$prefix}inquiry` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL COMMENT '姓名',
        `email` varchar(100) NOT NULL COMMENT '邮箱',
        `phone` varchar(20) DEFAULT NULL COMMENT '电话',
        `company` varchar(200) DEFAULT NULL COMMENT '公司',
        `message` text NOT NULL COMMENT '留言内容',
        `reply` text COMMENT '回复内容',
        `status` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0未处理 1已处理',
        `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP地址',
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `mm_inquiry_status_index` (`status`),
        KEY `mm_inquiry_created_at_index` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    -- 导航菜单表
    CREATE TABLE IF NOT EXISTS `{$prefix}navigation` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `name_zh` varchar(100) NOT NULL COMMENT '中文名称',
        `name_en` varchar(100) NOT NULL COMMENT '英文名称',
        `url` varchar(255) NOT NULL COMMENT '链接地址',
        `icon` varchar(100) DEFAULT NULL COMMENT '图标',
        `type` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '类型：1前台 2后台',
        `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父级ID',
        `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
        `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1显示 0隐藏',
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `mm_navigation_type_index` (`type`),
        KEY `mm_navigation_parent_id_index` (`parent_id`),
        KEY `mm_navigation_sort_index` (`sort`),
        KEY `mm_navigation_status_index` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
}

/**
 * 插入基础配置数据
 */
function insertBasicConfigs($pdo, $prefix) {
    $configs = [
        ['site_title', 'MMTech - AI 应用开发与技术创新', 'MMTech - AI Application Development', 'base', 'text', '网站标题', 'Site Title'],
        ['site_description', '专注于 AI 应用开发、网站开发、小程序开发、APP 开发的高新技术企业', 'Focus on AI application development, website development, mini-program and APP development', 'base', 'textarea', '网站描述', 'Site Description'],
        ['site_keywords', 'AI 开发,人工智能,网站开发,小程序开发,APP 开发', 'AI Development, Artificial Intelligence, Website Development, Mini-program, APP Development', 'base', 'text', '网站关键词', 'Site Keywords'],
        ['company_name', 'MMTech 科技有限公司', 'MMTech Technology Co., Ltd.', 'base', 'text', '公司名称', 'Company Name'],
        ['contact_email', 'contact@mmtech.ltd', 'contact@mmtech.ltd', 'base', 'text', '联系邮箱', 'Contact Email'],
        ['contact_phone', '+86-138-0000-0000', '+86-138-0000-0000', 'base', 'text', '联系电话', 'Contact Phone'],
        ['company_address', '上海市浦东新区张江高科技园区', 'Pudong New Area, Zhangjiang Hi-Tech Park, Shanghai', 'base', 'text', '公司地址', 'Company Address'],
        ['icp_number', '沪ICP备2026000000号', 'ICP Registration Number', 'base', 'text', 'ICP备案号', 'ICP Number'],
        ['analytics_code', '', '', 'base', 'textarea', '统计代码', 'Analytics Code']
    ];
    
    foreach ($configs as $config) {
        $stmt = $pdo->prepare("INSERT INTO `{$prefix}config` (`key`, `value_zh`, `value_en`, `group`, `type`, `title_zh`, `title_en`) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value_zh` = VALUES(`value_zh`), `value_en` = VALUES(`value_en`)");
        $stmt->execute($config);
    }
}

/**
 * 插入导航菜单
 */
function insertNavigationMenu($pdo, $prefix) {
    $menus = [
        ['首页', 'Home', '/', 'home', 1, 0, 1],
        ['关于我们', 'About Us', '/about', 'info-circle', 1, 0, 2],
        ['服务项目', 'Services', '/services', 'cog', 1, 0, 3],
        ['项目案例', 'Cases', '/cases', 'briefcase', 1, 0, 4],
        ['新闻动态', 'Blog', '/blog', 'newspaper', 1, 0, 5],
        ['联系我们', 'Contact', '/contact', 'phone', 1, 0, 6]
    ];
    
    foreach ($menus as $menu) {
        $stmt = $pdo->prepare("INSERT INTO `{$prefix}navigation` (`name_zh`, `name_en`, `url`, `icon`, `type`, `parent_id`, `sort`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute($menu);
    }
}

/**
 * 插入示例数据
 */
function insertSampleData($pdo, $prefix) {
    // 示例服务
    $services = [
        ['ai-development', 'AI 应用开发', 'AI Application Development', '使用先进的人工智能技术，为企业打造智能化解决方案', 'Develop intelligent solutions for enterprises using advanced AI technology', '<p>AI 应用开发服务详情...</p>', '<p>AI application development details...</p>', 'brain', '', 1, 1],
        ['web-development', '网站开发', 'Web Development', '响应式网站设计，提供出色的用户体验', 'Responsive web design providing excellent user experience', '<p>网站开发服务详情...</p>', '<p>Web development details...</p>', 'globe', '', 1, 2],
        ['mobile-development', '移动应用开发', 'Mobile App Development', '原生APP和跨平台应用开发', 'Native and cross-platform app development', '<p>移动应用开发详情...</p>', '<p>Mobile app development details...</p>', 'mobile', '', 1, 3]
    ];
    
    foreach ($services as $service) {
        $stmt = $pdo->prepare("INSERT INTO `{$prefix}service` (`slug`, `title_zh`, `title_en`, `description_zh`, `description_en`, `content_zh`, `content_en`, `icon`, `image`, `status`, `sort`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute($service);
    }
    
    // 示例文章
    $articles = [
        ['laravel-11-release', 'Laravel 11 正式发布', 'Laravel 11 Released', 'Laravel 11 带来了许多新特性...', 'Laravel 11 brings many new features...', '<p>文章内容...</p>', '<p>Article content...</p>', '', 'MMTech 团队', 1, '["Laravel", "PHP"]', '2026-03-01 10:00:00'],
        ['ai-trends-2026', '2026 AI 发展趋势', 'AI Trends 2026', '2026年人工智能的发展趋势...', 'AI development trends in 2026...', '<p>文章内容...</p>', '<p>Article content...</p>', '', 'MMTech 团队', 1, '["AI", "Trends"]', '2026-03-01 15:00:00']
    ];
    
    foreach ($articles as $article) {
        $stmt = $pdo->prepare("INSERT INTO `{$prefix}article` (`slug`, `title_zh`, `title_en`, `excerpt_zh`, `excerpt_en`, `content_zh`, `content_en`, `cover_image`, `author`, `category_id`, `tags`, `published_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute($article);
    }
}

/**
 * 递归设置目录权限
 */
function setDirectoryPermissions($dir) {
    if (is_dir($dir)) {
        chmod($dir, 0755);
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $fullPath = $dir . '/' . $file;
                if (is_dir($fullPath)) {
                    setDirectoryPermissions($fullPath);
                } else {
                    chmod($fullPath, 0644);
                }
            }
        }
    }
}
