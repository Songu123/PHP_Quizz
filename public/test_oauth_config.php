<?php
/**
 * Test OAuth Configuration
 */
session_start();
require_once '../app/config/config.php';
require_once '../app/config/google_oauth.php';

echo "<!DOCTYPE html><html><head>";
echo "<title>OAuth Configuration Test</title>";
echo "<style>body{font-family:Arial;padding:30px;background:#f5f5f5;} .box{background:white;padding:20px;margin:20px 0;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);} .error{background:#ffebee;border-left:4px solid #f44336;} .success{background:#e8f5e9;border-left:4px solid #4caf50;} .warning{background:#fff3e0;border-left:4px solid #ff9800;} code{background:#f5f5f5;padding:2px 8px;border-radius:3px;font-family:monospace;} pre{background:#263238;color:#aed581;padding:15px;border-radius:5px;overflow-x:auto;}</style>";
echo "</head><body>";

echo "<h1>🔍 Google OAuth Configuration Test</h1>";

// 1. Check environment variables
echo "<div class='box'>";
echo "<h2>1. Environment Variables</h2>";

$clientId = getenv('GOOGLE_CLIENT_ID');
$clientSecret = getenv('GOOGLE_CLIENT_SECRET');

if ($clientId && $clientId !== 'YOUR_GOOGLE_CLIENT_ID_HERE') {
    echo "<div class='success'>✅ GOOGLE_CLIENT_ID loaded from .env.local<br>";
    echo "Value: <code>" . substr($clientId, 0, 30) . "...</code></div>";
} else {
    echo "<div class='error'>❌ GOOGLE_CLIENT_ID not loaded or invalid<br>";
    echo "Current value: <code>" . ($clientId ?: 'NULL') . "</code></div>";
}

if ($clientSecret && $clientSecret !== 'YOUR_GOOGLE_CLIENT_SECRET_HERE') {
    echo "<div class='success'>✅ GOOGLE_CLIENT_SECRET loaded from .env.local<br>";
    echo "Value: <code>GOCSPX-***" . substr($clientSecret, -5) . "</code></div>";
} else {
    echo "<div class='error'>❌ GOOGLE_CLIENT_SECRET not loaded or invalid<br>";
    echo "Current value: <code>" . ($clientSecret ?: 'NULL') . "</code></div>";
}
echo "</div>";

// 2. Check constants
echo "<div class='box'>";
echo "<h2>2. Defined Constants</h2>";
echo "<strong>GOOGLE_CLIENT_ID:</strong> <code>" . (defined('GOOGLE_CLIENT_ID') ? substr(GOOGLE_CLIENT_ID, 0, 30) . '...' : 'NOT DEFINED') . "</code><br>";
echo "<strong>GOOGLE_CLIENT_SECRET:</strong> <code>" . (defined('GOOGLE_CLIENT_SECRET') ? 'GOCSPX-***' . substr(GOOGLE_CLIENT_SECRET, -5) : 'NOT DEFINED') . "</code><br>";
echo "<strong>GOOGLE_REDIRECT_URI:</strong> <code>" . (defined('GOOGLE_REDIRECT_URI') ? GOOGLE_REDIRECT_URI : 'NOT DEFINED') . "</code><br>";
echo "</div>";

// 3. Check .env.local file
echo "<div class='box'>";
echo "<h2>3. .env.local File Check</h2>";
$envPath = dirname(__DIR__) . '/.env.local';
if (file_exists($envPath)) {
    echo "<div class='success'>✅ File exists: <code>$envPath</code></div>";
    $content = file_get_contents($envPath);
    $lines = explode("\n", $content);
    echo "<strong>File content (first 10 lines):</strong><pre>";
    foreach (array_slice($lines, 0, 10) as $line) {
        if (strpos($line, 'SECRET') !== false) {
            $line = preg_replace('/GOCSPX-[^\s]+/', 'GOCSPX-***', $line);
        }
        echo htmlspecialchars($line) . "\n";
    }
    echo "</pre>";
} else {
    echo "<div class='error'>❌ File not found: <code>$envPath</code></div>";
}
echo "</div>";

// 4. Test OAuth URL generation
echo "<div class='box'>";
echo "<h2>4. OAuth URL Generation Test</h2>";

try {
    require_once '../app/helpers/GoogleOAuth.php';
    $oauth = new GoogleOAuth();
    $authUrl = $oauth->getAuthUrl();
    
    if ($authUrl && strpos($authUrl, 'accounts.google.com') !== false) {
        echo "<div class='success'>✅ OAuth URL generated successfully</div>";
        echo "<strong>Auth URL:</strong><br>";
        echo "<textarea style='width:100%;height:80px;'>" . $authUrl . "</textarea><br><br>";
        
        // Parse URL to check parameters
        $parsedUrl = parse_url($authUrl);
        parse_str($parsedUrl['query'], $params);
        
        echo "<strong>URL Parameters:</strong><br>";
        echo "• client_id: <code>" . (isset($params['client_id']) ? substr($params['client_id'], 0, 30) . '...' : '❌ MISSING') . "</code><br>";
        echo "• redirect_uri: <code>" . (isset($params['redirect_uri']) ? urldecode($params['redirect_uri']) : '❌ MISSING') . "</code><br>";
        echo "• response_type: <code>" . (isset($params['response_type']) ? $params['response_type'] : '❌ MISSING') . "</code><br>";
        echo "• scope: <code>" . (isset($params['scope']) ? $params['scope'] : '❌ MISSING') . "</code><br>";
        echo "• state: <code>" . (isset($params['state']) ? 'Set ✅' : '❌ MISSING') . "</code><br>";
        
    } else {
        echo "<div class='error'>❌ Failed to generate OAuth URL</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
}
echo "</div>";

// 5. Common issues
echo "<div class='box warning'>";
echo "<h2>5. Common Issues & Solutions</h2>";
echo "<h3>❌ Máy chủ không thể xử lý yêu cầu</h3>";
echo "<p><strong>Nguyên nhân thường gặp:</strong></p>";
echo "<ol>";
echo "<li><strong>Client ID/Secret không đúng:</strong> Kiểm tra lại trong Google Console</li>";
echo "<li><strong>Redirect URI không khớp:</strong> Phải giống CHÍNH XÁC với Google Console</li>";
echo "<li><strong>Credentials đã bị revoke:</strong> Tạo OAuth Client ID mới</li>";
echo "<li><strong>Project chưa enable APIs:</strong> Enable Google+ API hoặc People API</li>";
echo "<li><strong>.env.local không được load:</strong> Kiểm tra env_loader.php</li>";
echo "</ol>";

echo "<h3>✅ Giải pháp:</h3>";
echo "<ol>";
echo "<li>Vào <a href='https://console.cloud.google.com/apis/credentials' target='_blank'>Google Console</a></li>";
echo "<li>Xóa OAuth Client ID cũ</li>";
echo "<li>Tạo OAuth Client ID mới</li>";
echo "<li>Redirect URI: <code>" . GOOGLE_REDIRECT_URI . "</code></li>";
echo "<li>Copy Client ID và Secret mới vào .env.local</li>";
echo "<li>Reload page này để test</li>";
echo "</ol>";
echo "</div>";

// 6. Actions
echo "<div style='text-align:center;margin:30px 0;'>";
echo "<a href='?refresh=1' style='display:inline-block;padding:12px 24px;background:#4285f4;color:white;text-decoration:none;border-radius:5px;margin:5px;'>🔄 Refresh Test</a>";
if (isset($authUrl)) {
    echo "<a href='$authUrl' style='display:inline-block;padding:12px 24px;background:#34a853;color:white;text-decoration:none;border-radius:5px;margin:5px;'>🔐 Test Google Login</a>";
}
echo "<a href='../auth/login' style='display:inline-block;padding:12px 24px;background:#11998e;color:white;text-decoration:none;border-radius:5px;margin:5px;'>◀️ Back to Login</a>";
echo "</div>";

echo "</body></html>";
?>
