<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OAuth Diagnostic Tool</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #667eea;
            text-align: center;
            margin-bottom: 30px;
            font-size: 32px;
        }
        .section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 5px solid #667eea;
        }
        .success {
            border-left-color: #4caf50;
            background: #e8f5e9;
        }
        .error {
            border-left-color: #f44336;
            background: #ffebee;
        }
        .warning {
            border-left-color: #ff9800;
            background: #fff3e0;
        }
        h2 {
            color: #667eea;
            font-size: 22px;
            margin-bottom: 15px;
        }
        code {
            background: #263238;
            color: #aed581;
            padding: 2px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 10px;
            margin: 15px 0;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin: 10px 5px;
            transition: all 0.3s;
            font-weight: bold;
        }
        .btn:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .btn-success {
            background: #4caf50;
        }
        .btn-success:hover {
            background: #45a049;
        }
        .oauth-url {
            background: #263238;
            color: #aed581;
            padding: 15px;
            border-radius: 8px;
            word-break: break-all;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }
        .checklist {
            list-style: none;
            padding: 0;
        }
        .checklist li {
            padding: 10px;
            margin: 5px 0;
            background: white;
            border-radius: 5px;
            border-left: 3px solid #ddd;
        }
        .checklist li.ok {
            border-left-color: #4caf50;
        }
        .checklist li.fail {
            border-left-color: #f44336;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 OAuth Diagnostic Tool</h1>
        
        <?php
        session_start();
        require_once '../app/config/config.php';
        require_once '../app/config/google_oauth.php';
        
        $checks = [];
        $allOk = true;
        
        // Check 1: Environment variables
        echo "<div class='section'>";
        echo "<h2>1. Environment Variables</h2>";
        
        $clientId = getenv('GOOGLE_CLIENT_ID');
        $clientSecret = getenv('GOOGLE_CLIENT_SECRET');
        
        if ($clientId && strlen($clientId) > 30) {
            echo "<div class='success'>✅ GOOGLE_CLIENT_ID loaded successfully</div>";
            echo "<div class='info-grid'>";
            echo "<div class='info-label'>Value:</div>";
            echo "<div><code>" . substr($clientId, 0, 40) . "...</code></div>";
            echo "</div>";
            $checks['env_client_id'] = true;
        } else {
            echo "<div class='error'>❌ GOOGLE_CLIENT_ID not loaded or invalid</div>";
            $checks['env_client_id'] = false;
            $allOk = false;
        }
        
        if ($clientSecret && strlen($clientSecret) > 20) {
            echo "<div class='success'>✅ GOOGLE_CLIENT_SECRET loaded successfully</div>";
            echo "<div class='info-grid'>";
            echo "<div class='info-label'>Value:</div>";
            echo "<div><code>GOCSPX-***" . substr($clientSecret, -8) . "</code></div>";
            echo "</div>";
            $checks['env_client_secret'] = true;
        } else {
            echo "<div class='error'>❌ GOOGLE_CLIENT_SECRET not loaded or invalid</div>";
            $checks['env_client_secret'] = false;
            $allOk = false;
        }
        echo "</div>";
        
        // Check 2: Constants
        echo "<div class='section'>";
        echo "<h2>2. OAuth Constants</h2>";
        
        if (defined('GOOGLE_CLIENT_ID') && GOOGLE_CLIENT_ID !== 'YOUR_GOOGLE_CLIENT_ID_HERE') {
            echo "<div class='success'>✅ GOOGLE_CLIENT_ID constant defined</div>";
            echo "<div class='info-grid'>";
            echo "<div class='info-label'>Value:</div>";
            echo "<div><code>" . substr(GOOGLE_CLIENT_ID, 0, 40) . "...</code></div>";
            echo "</div>";
            $checks['const_client_id'] = true;
        } else {
            echo "<div class='error'>❌ GOOGLE_CLIENT_ID constant not properly set</div>";
            $checks['const_client_id'] = false;
            $allOk = false;
        }
        
        echo "<div class='info-grid'>";
        echo "<div class='info-label'>Redirect URI:</div>";
        echo "<div><code>" . GOOGLE_REDIRECT_URI . "</code></div>";
        echo "</div>";
        
        echo "</div>";
        
        // Check 3: OAuth URL Generation
        echo "<div class='section'>";
        echo "<h2>3. OAuth URL Generation</h2>";
        
        try {
            require_once '../app/helpers/GoogleOAuth.php';
            $oauth = new GoogleOAuth();
            $authUrl = $oauth->getAuthUrl();
            
            if ($authUrl && strpos($authUrl, 'accounts.google.com') !== false) {
                echo "<div class='success'>✅ OAuth URL generated successfully</div>";
                
                // Parse URL
                $parsedUrl = parse_url($authUrl);
                parse_str($parsedUrl['query'], $params);
                
                echo "<h3 style='margin-top:15px;color:#667eea;'>URL Parameters:</h3>";
                echo "<ul class='checklist'>";
                
                // Check client_id
                if (isset($params['client_id']) && !empty($params['client_id'])) {
                    echo "<li class='ok'>✅ <strong>client_id:</strong> " . substr($params['client_id'], 0, 40) . "...</li>";
                    $checks['url_client_id'] = true;
                } else {
                    echo "<li class='fail'>❌ <strong>client_id:</strong> MISSING</li>";
                    $checks['url_client_id'] = false;
                    $allOk = false;
                }
                
                // Check redirect_uri
                if (isset($params['redirect_uri'])) {
                    $redirectUri = urldecode($params['redirect_uri']);
                    echo "<li class='ok'>✅ <strong>redirect_uri:</strong> " . $redirectUri . "</li>";
                    $checks['url_redirect_uri'] = true;
                } else {
                    echo "<li class='fail'>❌ <strong>redirect_uri:</strong> MISSING</li>";
                    $checks['url_redirect_uri'] = false;
                    $allOk = false;
                }
                
                // Check response_type
                if (isset($params['response_type']) && $params['response_type'] === 'code') {
                    echo "<li class='ok'>✅ <strong>response_type:</strong> " . $params['response_type'] . "</li>";
                    $checks['url_response_type'] = true;
                } else {
                    echo "<li class='fail'>❌ <strong>response_type:</strong> MISSING or INVALID</li>";
                    $checks['url_response_type'] = false;
                    $allOk = false;
                }
                
                // Check scope
                if (isset($params['scope'])) {
                    echo "<li class='ok'>✅ <strong>scope:</strong> " . $params['scope'] . "</li>";
                    $checks['url_scope'] = true;
                } else {
                    echo "<li class='fail'>❌ <strong>scope:</strong> MISSING</li>";
                    $checks['url_scope'] = false;
                    $allOk = false;
                }
                
                // Check state
                if (isset($params['state'])) {
                    echo "<li class='ok'>✅ <strong>state:</strong> Present (CSRF protection)</li>";
                    $checks['url_state'] = true;
                } else {
                    echo "<li class='fail'>❌ <strong>state:</strong> MISSING (security risk!)</li>";
                    $checks['url_state'] = false;
                }
                
                echo "</ul>";
                
                echo "<h3 style='margin-top:20px;color:#667eea;'>Full OAuth URL:</h3>";
                echo "<div class='oauth-url'>" . htmlspecialchars($authUrl) . "</div>";
                
                $checks['oauth_url'] = true;
            } else {
                echo "<div class='error'>❌ Failed to generate OAuth URL</div>";
                $checks['oauth_url'] = false;
                $allOk = false;
            }
        } catch (Exception $e) {
            echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
            $checks['oauth_url'] = false;
            $allOk = false;
        }
        
        echo "</div>";
        
        // Check 4: Google Console Setup Requirements
        echo "<div class='section warning'>";
        echo "<h2>4. ⚠️ Google Console Checklist</h2>";
        echo "<p><strong>Để tránh lỗi 400, đảm bảo trong Google Console:</strong></p>";
        echo "<ul class='checklist'>";
        echo "<li>📌 OAuth Consent Screen: Publishing status = <strong>Testing</strong></li>";
        echo "<li>📌 Test Users: Đã thêm email của bạn vào danh sách</li>";
        echo "<li>📌 Scopes: Đã thêm userinfo.email và userinfo.profile</li>";
        echo "<li>📌 Authorized redirect URIs: <code>" . GOOGLE_REDIRECT_URI . "</code></li>";
        echo "<li>📌 Credentials: Client ID mới (không phải cái bị lộ trên GitHub)</li>";
        echo "</ul>";
        echo "</div>";
        
        // Summary
        echo "<div class='section " . ($allOk ? 'success' : 'error') . "'>";
        echo "<h2>5. Summary</h2>";
        
        $passCount = count(array_filter($checks));
        $totalCount = count($checks);
        
        echo "<p style='font-size:20px;font-weight:bold;'>";
        echo "Passed: $passCount / $totalCount checks";
        echo "</p>";
        
        if ($allOk) {
            echo "<p style='margin-top:15px;'>✅ All checks passed! OAuth should work correctly.</p>";
            echo "<p style='margin-top:10px;'>⚠️ If you still get error 400, the issue is in Google Console setup:</p>";
            echo "<ol style='margin-left:20px;margin-top:10px;'>";
            echo "<li>Email chưa được thêm vào <strong>Test users</strong></li>";
            echo "<li><strong>Redirect URI</strong> không khớp CHÍNH XÁC</li>";
            echo "<li>Credentials đã bị revoke/disable</li>";
            echo "</ol>";
        } else {
            echo "<p style='margin-top:15px;'>❌ Some checks failed. Please fix the issues above.</p>";
        }
        
        echo "</div>";
        
        // Actions
        echo "<div style='text-align:center;margin-top:30px;'>";
        echo "<a href='?refresh=1' class='btn'>🔄 Refresh Test</a>";
        if ($allOk && isset($authUrl)) {
            echo "<a href='$authUrl' class='btn btn-success'>🔐 Test Google Login</a>";
        }
        echo "<a href='https://console.cloud.google.com/apis/credentials' target='_blank' class='btn'>⚙️ Google Console</a>";
        echo "<a href='../auth/login' class='btn'>◀️ Back to Login</a>";
        echo "</div>";
        ?>
    </div>
</body>
</html>
