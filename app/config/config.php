<?php
/**
 * File cấu hình database
 */

// Application paths
define('APP', dirname(dirname(__FILE__)));

// Database configuration
define('DB_HOST', isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : 'localhost');
define('DB_USER', isset($_ENV['DB_USERNAME']) ? $_ENV['DB_USERNAME'] : 'root');
define('DB_PASS', isset($_ENV['DB_PASSWORD']) ? $_ENV['DB_PASSWORD'] : '');
define('DB_NAME', isset($_ENV['DB_DATABASE']) ? $_ENV['DB_DATABASE'] : 'quizz_loq');

// Application URL
define('URLROOT', 'http://localhost/doan_mon');

// Site name
define('SITENAME', 'Đồ Án Môn');

// App version
define('APPVERSION', '1.0.0');