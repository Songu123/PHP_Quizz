<?php
/**
 * File cấu hình database
 */

// Application paths (only define if not already defined)
if (!defined('APP')) {
    define('APP', dirname(dirname(__FILE__)));
}

// Database configuration
define('DB_HOST', isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : 'localhost');
define('DB_USER', isset($_ENV['DB_USERNAME']) ? $_ENV['DB_USERNAME'] : 'root');
define('DB_PASS', isset($_ENV['DB_PASSWORD']) ? $_ENV['DB_PASSWORD'] : '');
define('DB_NAME', isset($_ENV['DB_DATABASE']) ? $_ENV['DB_DATABASE'] : 'quizz_loq');

// Application URL
define('URLROOT', isset($_ENV['URLROOT']) ? $_ENV['URLROOT'] : 'http://localhost/doan_mon/public');

// Site name
define('SITENAME', 'QuizMaster');

// App version
define('APPVERSION', '1.0.0');