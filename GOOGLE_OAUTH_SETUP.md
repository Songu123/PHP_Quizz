# Hướng dẫn cấu hình Google OAuth 2.0

## Bước 1: Tạo Google Cloud Project

1. Truy cập [Google Cloud Console](https://console.cloud.google.com/)
2. Click **"Select a project"** ở phía trên → **"NEW PROJECT"**
3. Nhập tên project (ví dụ: "Quiz Website") → Click **"CREATE"**
4. Chờ vài giây để project được tạo

## Bước 2: Bật Google+ API

1. Trong Google Cloud Console, vào menu bên trái
2. Chọn **"APIs & Services"** → **"Library"**
3. Tìm kiếm **"Google+ API"**
4. Click vào **"Google+ API"** → Click **"ENABLE"**

## Bước 3: Tạo OAuth 2.0 Credentials

1. Vào **"APIs & Services"** → **"Credentials"**
2. Click **"CREATE CREDENTIALS"** → Chọn **"OAuth client ID"**
3. Nếu chưa cấu hình OAuth consent screen:
   - Click **"CONFIGURE CONSENT SCREEN"**
   - Chọn **"External"** → Click **"CREATE"**
   - Điền thông tin:
     - App name: `Quiz Website`
     - User support email: Email của bạn
     - Developer contact: Email của bạn
   - Click **"SAVE AND CONTINUE"**
   - Ở phần Scopes, click **"ADD OR REMOVE SCOPES"**
   - Chọn:
     - `.../auth/userinfo.email`
     - `.../auth/userinfo.profile`
   - Click **"UPDATE"** → **"SAVE AND CONTINUE"**
   - Test users: Thêm email test của bạn
   - Click **"SAVE AND CONTINUE"**

4. Quay lại **"Credentials"** → Click **"CREATE CREDENTIALS"** → **"OAuth client ID"**
5. Chọn Application type: **"Web application"**
6. Nhập tên: `Quiz Website OAuth`
7. **Authorized redirect URIs**, click **"ADD URI"** và nhập:
   ```
   http://localhost/doan_mon/auth/googlecallback
   ```
8. Click **"CREATE"**
9. Một popup sẽ hiện ra với **Client ID** và **Client Secret**
10. **QUAN TRỌNG**: Copy cả Client ID và Client Secret

## Bước 4: Cấu hình trong code

1. Mở file `app/config/google_oauth.php`
2. Thay thế các giá trị sau:

```php
// Thay YOUR_CLIENT_ID bằng Client ID vừa copy
define('GOOGLE_CLIENT_ID', 'YOUR_CLIENT_ID.apps.googleusercontent.com');

// Thay YOUR_CLIENT_SECRET bằng Client Secret vừa copy
define('GOOGLE_CLIENT_SECRET', 'YOUR_CLIENT_SECRET');
```

## Bước 5: Cập nhật Database

1. Mở phpMyAdmin hoặc MySQL client
2. Chọn database `quizz_loq`
3. Chạy file SQL: `database/google_oauth_migration.sql`

Hoặc chạy từ command line:
```bash
mysql -u root -p quizz_loq < database/google_oauth_migration.sql
```

Script này sẽ:
- Thêm cột `google_id` để lưu Google User ID
- Thêm cột `avatar` để lưu ảnh đại diện
- Cho phép cột `password` NULL (cho user đăng nhập bằng Google)
- Tạo index cho `google_id`

## Bước 6: Test Google Login

1. Khởi động XAMPP (Apache + MySQL)
2. Truy cập: `http://localhost/doan_mon/auth/login`
3. Click nút **"Google"**
4. Chọn tài khoản Google của bạn
5. Cho phép ứng dụng truy cập thông tin
6. Bạn sẽ được chuyển về trang chủ với session đã đăng nhập

## Cấu trúc Files

```
app/
├── config/
│   └── google_oauth.php          # Cấu hình OAuth constants
├── helpers/
│   └── GoogleOAuth.php            # Class xử lý OAuth flow
├── controllers/
│   └── Auth.php                   # Controller với googleLogin() và googleCallback()
├── models/
│   └── User.php                   # Model với findByGoogleId() và createOrUpdateFromGoogle()
└── views/
    └── auth/
        └── login.php              # Login page với Google button

database/
└── google_oauth_migration.sql     # SQL migration script
```

## Luồng hoạt động

1. **User click nút Google** → Gọi `/auth/google-login`
2. **Controller googleLogin()** → Tạo auth URL và redirect đến Google
3. **User đăng nhập Google** → Google redirect về `/auth/google-callback?code=...&state=...`
4. **Controller googleCallback()** → 
   - Verify state (CSRF protection)
   - Exchange code cho access token
   - Lấy user info từ Google
   - Tìm hoặc tạo user trong database
   - Tạo session và đăng nhập
5. **Redirect về trang chủ** → User đã đăng nhập

## Xử lý lỗi thường gặp

### Lỗi: "redirect_uri_mismatch"
**Nguyên nhân**: Redirect URI không khớp với cấu hình trong Google Console

**Giải pháp**: 
- Kiểm tra file `google_oauth.php`: `GOOGLE_REDIRECT_URI`
- Phải khớp chính xác với Authorized redirect URI trong Google Console
- Chú ý: có/không có dấu `/` cuối cùng

### Lỗi: "invalid_client"
**Nguyên nhân**: Client ID hoặc Client Secret không đúng

**Giải pháp**: 
- Kiểm tra lại Client ID và Client Secret trong `google_oauth.php`
- Copy lại từ Google Console nếu cần

### Lỗi: "access_denied"
**Nguyên nhân**: User từ chối cấp quyền hoặc email chưa được thêm vào Test users

**Giải pháp**: 
- Thêm email vào Test users trong OAuth consent screen
- Hoặc publish app (chỉ khi đã sẵn sàng production)

### Lỗi: Database - Column 'google_id' doesn't exist
**Nguyên nhân**: Chưa chạy migration script

**Giải pháp**: 
- Chạy file `database/google_oauth_migration.sql`

## Bảo mật

- ✅ State parameter được sử dụng để chống CSRF
- ✅ Client Secret không bao giờ được gửi về client
- ✅ Access token được xử lý server-side
- ✅ Session được sử dụng để lưu trữ thông tin user
- ⚠️ **QUAN TRỌNG**: Không commit file `google_oauth.php` với credentials thật lên Git
- ⚠️ **KHUYẾN NGHỊ**: Sử dụng environment variables cho production

## Production Checklist

Trước khi deploy lên production:

- [ ] Thay đổi Redirect URI thành domain thực
- [ ] Cập nhật Authorized redirect URIs trong Google Console
- [ ] Publish OAuth consent screen
- [ ] Sử dụng environment variables cho credentials
- [ ] Enable SSL/HTTPS
- [ ] Thêm error logging
- [ ] Test với nhiều tài khoản khác nhau

## Tài liệu tham khảo

- [Google OAuth 2.0 Documentation](https://developers.google.com/identity/protocols/oauth2)
- [Google API Console](https://console.developers.google.com/)
- [OAuth 2.0 Playground](https://developers.google.com/oauthplayground/)
