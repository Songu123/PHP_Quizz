<?php
/**
 * Test file để xem redirect URI Google đang nhận
 */
session_start();
require_once '../app/config/config.php';
require_once '../app/config/google_oauth.php';
require_once '../app/helpers/GoogleOAuth.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>OAuth Debug</title>";
echo "<style>body{font-family:Arial;padding:20px;} .box{background:#f0f0f0;padding:15px;margin:10px 0;border-radius:5px;} .error{background:#ffebee;border:1px solid #c62828;} .success{background:#e8f5e9;border:1px solid #2e7d32;} code{background:#fff;padding:2px 5px;border:1px solid #ddd;}</style>";
echo "</head><body>";

echo "<h1>🔍 Google OAuth Debug Tool</h1>";

// 1. Hiển thị config hiện tại
echo "<div class='box'>";
echo "<h2>1. Current Configuration</h2>";
echo "<strong>URLROOT:</strong> <code>" . URLROOT . "</code><br>";
echo "<strong>Redirect URI (in code):</strong> <code>" . GOOGLE_REDIRECT_URI . "</code><br>";
echo "<strong>Client ID:</strong> <code>" . GOOGLE_CLIENT_ID . "</code><br>";
echo "</div>";

// 2. Generate OAuth URL và phân tích
$oauth = new GoogleOAuth();
$authUrl = $oauth->getAuthUrl();
$parsedUrl = parse_url($authUrl);
parse_str($parsedUrl['query'], $queryParams);

echo "<div class='box'>";
echo "<h2>2. OAuth URL Analysis</h2>";
echo "<strong>Full Auth URL:</strong><br>";
echo "<textarea style='width:100%;height:60px;'>" . $authUrl . "</textarea><br><br>";
echo "<strong>Redirect URI being sent to Google:</strong><br>";
echo "<code style='font-size:16px;color:#c62828;'>" . urldecode($queryParams['redirect_uri']) . "</code>";
echo "</div>";

// 3. Hướng dẫn sửa
echo "<div class='box error'>";
echo "<h2>3. ❌ Error: redirect_uri_mismatch</h2>";
echo "<p><strong>Nguyên nhân:</strong> Redirect URI trong Google Console không khớp với URI trong code.</p>";
echo "<p><strong>Giải pháp:</strong></p>";
echo "<ol>";
echo "<li>Vào <a href='https://console.cloud.google.com/apis/credentials' target='_blank'>Google Console Credentials</a></li>";
echo "<li>Click vào OAuth client ID của bạn</li>";
echo "<li>Trong phần <strong>Authorized redirect URIs</strong>, thêm chính xác URI này:<br>";
echo "<code style='font-size:16px;background:#fff;padding:10px;display:block;margin:10px 0;'>" . GOOGLE_REDIRECT_URI . "</code>";
echo "</li>";
echo "<li>Click <strong>SAVE</strong></li>";
echo "<li>Đợi 5-10 giây rồi thử lại</li>";
echo "</ol>";
echo "</div>";

// 4. Copy-paste ready
echo "<div class='box success'>";
echo "<h2>4. ✅ Copy URI này để paste vào Google Console</h2>";
echo "<input type='text' value='" . GOOGLE_REDIRECT_URI . "' onclick='this.select();document.execCommand(\"copy\");alert(\"Đã copy!\");' style='width:100%;padding:10px;font-size:16px;' readonly>";
echo "<p style='color:#2e7d32;'><small>Click vào ô trên để tự động copy URI</small></p>";
echo "</div>";

// 5. Các URI có thể cần
echo "<div class='box'>";
echo "<h2>5. 📋 Các Redirect URI bạn có thể cần thêm</h2>";
echo "<p>Thêm TẤT CẢ các URI sau vào Google Console để tránh lỗi:</p>";

$possibleUris = [
    'http://localhost/doan_mon/public/auth/googlecallback',
    'http://localhost:80/doan_mon/public/auth/googlecallback',
    'http://localhost/doan_mon/auth/googlecallback',
    'http://127.0.0.1/doan_mon/public/auth/googlecallback',
];

echo "<ul>";
foreach ($possibleUris as $uri) {
    $isCurrent = ($uri === GOOGLE_REDIRECT_URI) ? " <strong>(ĐANG DÙNG)</strong>" : "";
    echo "<li><code>$uri</code>$isCurrent</li>";
}
echo "</ul>";
echo "</div>";

// 6. Test button
echo "<div class='box'>";
echo "<h2>6. 🧪 Test OAuth Flow</h2>";
echo "<p>Sau khi cập nhật Google Console, click nút dưới để test:</p>";
echo "<a href='$authUrl' style='display:inline-block;background:#4285f4;color:white;padding:15px 30px;text-decoration:none;border-radius:5px;font-weight:bold;'>🔐 Test Google Login</a>";
echo "<p><small>Nếu vẫn lỗi, chụp screenshot Google Console và báo lại.</small></p>";
echo "</div>";

echo "</body></html>";
?>
