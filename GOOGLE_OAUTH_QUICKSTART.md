# Quick Start - Test Google OAuth

## Kiể3. Tạo OAuth credentials
4. Add redirect URI: `http://localhost/doan_mon/auth/googlecallback`tra trước khi test

### 1. Kiểm tra files đã tồn tại
```bash
# Windows PowerShell
Test-Path app\config\google_oauth.php
Test-Path app\helpers\GoogleOAuth.php
Test-Path database\google_oauth_migration.sql
```

Tất cả phải trả về `True`

### 2. Kiểm tra database

```sql
-- Mở phpMyAdmin hoặc MySQL CLI
USE quizz_loq;

-- Kiểm tra bảng users có các cột cần thiết chưa
DESCRIBE users;
```

Phải có các cột:
- `google_id` (VARCHAR 255, UNIQUE, NULL)
- `avatar` (TEXT, NULL)
- `password` (VARCHAR 255, **NULL** - quan trọng!)

Nếu chưa có, chạy:
```bash
# Windows PowerShell (trong thư mục doan_mon)
Get-Content database\google_oauth_migration.sql | mysql -u root -p quizz_loq
```

### 3. Cấu hình Google OAuth

**Đọc file**: `GOOGLE_OAUTH_SETUP.md` để biết chi tiết

**TL;DR**:
1. Vào https://console.cloud.google.com/
2. Tạo project mới
3. Enable Google+ API
4. Tạo OAuth credentials
5. Add redirect URI: `http://localhost/doan_mon/auth/google-callback`
6. Copy Client ID và Client Secret vào `app/config/google_oauth.php`

### 4. Test ngay

1. Khởi động XAMPP (Apache + MySQL)
2. Truy cập: http://localhost/doan_mon/auth/login
3. Click nút **"Google"** (màu đỏ)
4. Nếu mọi thứ OK:
   - Sẽ chuyển đến trang login Google
   - Chọn tài khoản
   - Cho phép quyền truy cập
   - Tự động đăng nhập và về trang chủ

## Debug nếu có lỗi

### Bật error reporting

Thêm vào file `public/index.php` (dòng đầu tiên):
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Kiểm tra session

Thêm vào `app/controllers/Auth.php` trong method `googleCallback()`:
```php
// Sau dòng: $result = $googleOAuth->handleCallback($code, $state);
echo '<pre>';
var_dump($result);
echo '</pre>';
exit();
```

### Test GoogleOAuth helper

Tạo file test `test_google_oauth.php` trong thư mục `public/`:
```php
<?php
require_once '../app/config/config.php';
require_once '../app/config/google_oauth.php';
require_once '../app/helpers/GoogleOAuth.php';

session_start();

$oauth = new GoogleOAuth();
$authUrl = $oauth->getAuthUrl();

echo "Auth URL: " . $authUrl . "<br><br>";
echo "<a href='$authUrl'>Click để test Google login</a>";
```

Truy cập: http://localhost/doan_mon/public/test_google_oauth.php

## Kiểm tra kết quả

### Sau khi đăng nhập thành công

1. Kiểm tra session:
```php
// Thêm vào trang home
<?php
session_start();
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
?>
```

Phải có:
- `user_id`
- `user_email`
- `user_name`

2. Kiểm tra database:
```sql
SELECT id, full_name, email, google_id, avatar 
FROM users 
WHERE google_id IS NOT NULL;
```

Phải thấy user mới với:
- `google_id`: Có giá trị (ID từ Google)
- `avatar`: Link ảnh đại diện
- `password`: NULL hoặc empty

## Các route quan trọng

| Route | Method | Chức năng |
|-------|--------|-----------|
| `/auth/login` | GET | Trang đăng nhập |
| `/auth/login` | POST | Xử lý đăng nhập thường |
| `/auth/googlelogin` | GET | Chuyển đến Google OAuth |
| `/auth/googlecallback` | GET | Nhận callback từ Google |
| `/auth/logout` | GET | Đăng xuất |

## Flow hoàn chỉnh

```
User
 │
 ├─> Click "Google" button
 │   (href="/auth/googlelogin")
 │
 ├─> Auth::googlelogin()
 │   ├─> GoogleOAuth::getAuthUrl()
 │   └─> Redirect to Google
 │
 ├─> Google Login Page
 │   ├─> User cho phép quyền
 │   └─> Redirect to /auth/googlecallback?code=XXX&state=YYY
 │
 ├─> Auth::googlecallback()
 │   ├─> GoogleOAuth::handleCallback()
 │   │   ├─> Verify state
 │   │   ├─> Exchange code → access token
 │   │   └─> Get user info from Google API
 │   │
 │   ├─> User::createOrUpdateFromGoogle()
 │   │   ├─> Check email exists?
 │   │   ├─> Yes: Update google_id + avatar
 │   │   └─> No: Create new user
 │   │
 │   ├─> Auth::createUserSession()
 │   └─> Redirect to home
 │
 └─> User logged in!
```

## Xóa test user

Nếu muốn test lại từ đầu:
```sql
-- Xóa user đăng nhập bằng Google
DELETE FROM users WHERE google_id IS NOT NULL;

-- Hoặc reset google_id
UPDATE users SET google_id = NULL, avatar = NULL WHERE email = 'your@email.com';
```

## Các file đã thay đổi

| File | Thay đổi |
|------|----------|
| `app/config/google_oauth.php` | **NEW** - Constants cho OAuth |
| `app/helpers/GoogleOAuth.php` | **NEW** - OAuth helper class |
| `app/controllers/Auth.php` | **UPDATED** - Thêm googleLogin() và googleCallback() |
| `app/models/User.php` | **UPDATED** - Thêm findByGoogleId() và createOrUpdateFromGoogle() |
| `app/views/auth/login.php` | **UPDATED** - Google button thành link |
| `database/google_oauth_migration.sql` | **NEW** - Migration script |

## Done! 🎉

Giờ bạn có thể đăng nhập bằng Google rồi!
