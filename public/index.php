<?php
/**
 * Entry point cho ứng dụng MVC
 */

// Đặt đường dẫn gốc
define('ROOT', dirname(__DIR__));
define('APP', ROOT . '/app');

// Load cấu hình
require_once APP . '/config/config.php';

// Autoload classes
spl_autoload_register(function($class) {
    $file = APP . '/core/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Khởi tạo ứng dụng
$app = new App();