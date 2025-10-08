<?php
/**
 * Debug OAuth Configuration
 */
require_once '../app/config/config.php';
require_once '../app/config/google_oauth.php';

echo "<h2>Google OAuth Debug Information</h2>";
echo "<hr>";

echo "<h3>1. Configuration</h3>";
echo "<strong>Client ID:</strong> " . GOOGLE_CLIENT_ID . "<br>";
echo "<strong>Client Secret:</strong> " . (GOOGLE_CLIENT_SECRET ? "***" . substr(GOOGLE_CLIENT_SECRET, -5) : "NOT SET") . "<br>";
echo "<strong>Redirect URI:</strong> " . GOOGLE_REDIRECT_URI . "<br>";
echo "<strong>URLROOT:</strong> " . URLROOT . "<br>";

echo "<hr>";
echo "<h3>2. Expected vs Actual</h3>";
echo "<strong>Expected Redirect URI in Google Console:</strong><br>";
echo "<code>http://localhost/doan_mon/public/auth/googlecallback</code><br><br>";
echo "<strong>Actual Redirect URI in Code:</strong><br>";
echo "<code>" . GOOGLE_REDIRECT_URI . "</code><br>";

if (GOOGLE_REDIRECT_URI === 'http://localhost/doan_mon/public/auth/googlecallback') {
    echo "<div style='color: green; font-weight: bold;'>✅ MATCH! Redirect URI is correct</div>";
} else {
    echo "<div style='color: red; font-weight: bold;'>❌ MISMATCH! Please update Google Console or config file</div>";
}

echo "<hr>";
echo "<h3>3. OAuth URL Generator</h3>";

require_once '../app/helpers/GoogleOAuth.php';
$oauth = new GoogleOAuth();
$authUrl = $oauth->getAuthUrl();

echo "<strong>Generated Auth URL:</strong><br>";
echo "<textarea style='width: 100%; height: 100px;'>" . $authUrl . "</textarea><br><br>";
echo "<a href='$authUrl' style='background: #4285f4; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test Google Login</a>";

echo "<hr>";
echo "<h3>4. Checklist</h3>";
echo "<ul>";
echo "<li>Client ID và Client Secret đã điền ✅</li>";
echo "<li>Database có cột google_id ✅</li>";
echo "<li>Redirect URI: " . (GOOGLE_REDIRECT_URI === 'http://localhost/doan_mon/public/auth/googlecallback' ? '✅' : '❌') . "</li>";
echo "</ul>";

echo "<hr>";
echo "<h3>5. Common Issues</h3>";
echo "<ol>";
echo "<li><strong>Yêu cầu không hợp lệ:</strong> Thêm email vào Test users trong OAuth consent screen</li>";
echo "<li><strong>redirect_uri_mismatch:</strong> Redirect URI không khớp với Google Console</li>";
echo "<li><strong>invalid_client:</strong> Client ID hoặc Secret sai</li>";
echo "</ol>";
?>
