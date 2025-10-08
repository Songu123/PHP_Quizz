<?php
/**
 * Environment Variables Loader
 * Load credentials from .env.local file
 */

function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        return false;
    }
    
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse line
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            $value = trim($value, '"\'');
            
            // Set environment variable
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
    
    return true;
}

// Load .env.local if exists (local development)
// dirname(__DIR__) từ app/config/ = app/
// dirname(dirname(__DIR__)) từ app/config/ = root/
$envPath = dirname(dirname(__DIR__)) . '/.env.local';
if (file_exists($envPath)) {
    loadEnv($envPath);
} else {
    // Fallback to .env if .env.local doesn't exist
    $envPath = dirname(dirname(__DIR__)) . '/.env';
    if (file_exists($envPath)) {
        loadEnv($envPath);
    }
}
