<?php
/**
 * Google OAuth Configuration
 * 
 * HƯỚNG DẪN LẤY CLIENT ID VÀ CLIENT SECRET:
 * 
 * 1. Truy cập: https://console.cloud.google.com/
 * 2. Tạo project mới hoặc chọn project có sẵn
 * 3. Vào "APIs & Services" > "Credentials"
 * 4. Click "Create Credentials" > "OAuth client ID"
 * 5. Chọn "Web application"
 * 6. Thêm Authorized redirect URIs:
 *    - http://localhost/doan_mon/auth/googlecallback
 *    - http://localhost:80/doan_mon/auth/googlecallback
 * 7. Copy Client ID và Client Secret vào file .env (KHÔNG push lên Git!)
 */

// Google OAuth Configuration
// ⚠️ SECURITY: Sử dụng environment variables thay vì hardcode
// Tạo file .env.local để lưu credentials (file này không push lên Git)
define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: 'YOUR_GOOGLE_CLIENT_ID_HERE');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: 'YOUR_GOOGLE_CLIENT_SECRET_HERE');
// Đảm bảo redirect URI khớp với Google Console
define('GOOGLE_REDIRECT_URI', 'http://localhost/doan_mon/public/auth/googlecallback');

// Google OAuth URLs
define('GOOGLE_AUTH_URL', 'https://accounts.google.com/o/oauth2/v2/auth');
define('GOOGLE_TOKEN_URL', 'https://oauth2.googleapis.com/token');
define('GOOGLE_USERINFO_URL', 'https://www.googleapis.com/oauth2/v2/userinfo');

// OAuth Scopes
define('GOOGLE_OAUTH_SCOPE', [
    'https://www.googleapis.com/auth/userinfo.email',
    'https://www.googleapis.com/auth/userinfo.profile'
]);
