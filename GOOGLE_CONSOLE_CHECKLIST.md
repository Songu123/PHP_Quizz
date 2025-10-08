# ✅ GOOGLE CONSOLE SETUP CHECKLIST

## Lỗi 400 thường do Google Console chưa cấu hình đúng!

### 📋 KIỂM TRA TỪNG BƯỚC:

---

## 1️⃣ OAUTH CONSENT SCREEN

Vào: https://console.cloud.google.com/apis/credentials/consent

### Cần kiểm tra:

#### A. Publishing Status
```
✅ PHẢI LÀ: Testing
❌ KHÔNG: In Production (chưa verify)
```

#### B. Test Users (QUAN TRỌNG NHẤT!)
```
Status: PHẢI có email của bạn trong danh sách

Cách thêm:
1. Scroll xuống phần "Test users"
2. Click "+ ADD USERS"
3. Nhập email Google của bạn
4. Click "SAVE"
```

❌ **NẾU KHÔNG CÓ EMAIL TRONG TEST USERS → LỖI 400!**

#### C. Scopes
```
✅ PHẢI CÓ:
- https://www.googleapis.com/auth/userinfo.email
- https://www.googleapis.com/auth/userinfo.profile

Cách kiểm tra:
1. Click "EDIT APP"
2. Đi đến bước "Scopes"
3. Click "ADD OR REMOVE SCOPES"
4. Tìm và chọn 2 scopes trên
5. Click "UPDATE"
6. Click "SAVE AND CONTINUE"
```

---

## 2️⃣ OAUTH CLIENT ID

Vào: https://console.cloud.google.com/apis/credentials

### Cần kiểm tra:

#### A. Authorized Redirect URIs
```
PHẢI CHÍNH XÁC:
http://localhost/doan_mon/public/auth/googlecallback

❌ KHÔNG:
- http://localhost/doan_mon/auth/googlecallback (thiếu /public/)
- http://localhost/doan_mon/public/auth/googlecallback/ (thừa / cuối)
- http://localhost:80/doan_mon/public/auth/googlecallback (có :80)
```

#### B. Application Type
```
✅ PHẢI LÀ: Web application
❌ KHÔNG: Desktop, Mobile, etc.
```

---

## 3️⃣ CREDENTIALS STATUS

### Kiểm tra Credentials có bị revoke không:

```
1. Vào OAuth Client ID của bạn
2. Xem status có bị disabled không
3. Nếu bị disable → TẠO MỚI
```

### Credentials đã tạo:
```
Client ID: 340426364237-pf1lbteg6jsqqacj36rkufrv9fduabvr
Project: Kiểm tra xem có đúng project không
```

---

## 4️⃣ ENABLED APIS

Vào: https://console.cloud.google.com/apis/library

### Các API cần enable:

```
✅ Google+ API (hoặc People API)
   - Tìm "Google+ API"
   - Click vào
   - Click "ENABLE"
```

---

## 🧪 TEST CHECKLIST

Sau khi cấu hình xong, test theo thứ tự:

### 1. Test Configuration
```
http://localhost/doan_mon/public/oauth_diagnostic.php
```
Kiểm tra:
- [ ] All checks passed
- [ ] OAuth URL generated
- [ ] Client ID correct

### 2. Test Google Console
```
1. Copy OAuth URL từ diagnostic tool
2. Paste vào browser
3. Đăng nhập Google
```

**Kết quả mong đợi:**
- ✅ Hiển thị consent screen
- ✅ Có các permissions: email, profile
- ✅ Redirect về /auth/googlecallback

**Nếu lỗi 400:**
- ❌ Email chưa có trong Test users
- ❌ Redirect URI không khớp
- ❌ Credentials bị revoke

### 3. Test Full Flow
```
http://localhost/doan_mon/public/auth/login
```
Click nút Google → Đăng nhập → Redirect về trang chủ

---

## 🔧 COMMON FIXES

### Lỗi: "Access blocked: This app's request is invalid"
**Fix:**
1. Thêm email vào Test users
2. Đảm bảo Publishing status = Testing

### Lỗi: "redirect_uri_mismatch"
**Fix:**
1. Kiểm tra lại Redirect URI trong Google Console
2. Phải giống CHÍNH XÁC: `http://localhost/doan_mon/public/auth/googlecallback`

### Lỗi: "invalid_client"
**Fix:**
1. Client ID hoặc Secret sai
2. Kiểm tra .env.local
3. Xóa cache browser (Ctrl+Shift+Del)

### Lỗi: "access_denied"
**Fix:**
1. User từ chối quyền truy cập
2. Hoặc email không có trong Test users

---

## 📸 SCREENSHOTS CẦN KIỂM TRA

Chụp screenshot các trang sau để debug:

1. **OAuth Consent Screen:**
   - Publishing status
   - Test users section
   - Scopes section

2. **OAuth Client ID:**
   - Authorized redirect URIs
   - Application type

3. **Diagnostic Tool:**
   - All checks result
   - OAuth URL

---

## ⏱️ THỜI GIAN CHỜ

Sau khi thay đổi Google Console:
```
✓ Credentials mới: Hiệu lực ngay
✓ Redirect URI: Đợi 5-10 giây
✓ Test users: Hiệu lực ngay
✓ Scopes: Đợi vài giây
```

**Sau mỗi thay đổi:**
1. Đợi 10 giây
2. Clear browser cache
3. Test lại

---

## 🆘 VẪN KHÔNG ĐƯỢC?

Nếu đã làm TẤT CẢ các bước trên mà vẫn lỗi:

1. **Tạo Project mới:**
   - Tạo Google Cloud Project hoàn toàn mới
   - Setup OAuth từ đầu
   - Có thể project cũ bị issue

2. **Kiểm tra Browser:**
   - Thử Incognito mode
   - Thử browser khác
   - Clear tất cả cookies/cache

3. **Debug với Diagnostic Tool:**
   ```
   http://localhost/doan_mon/public/oauth_diagnostic.php
   ```
   Chụp screenshot kết quả

4. **Test trực tiếp OAuth URL:**
   - Copy OAuth URL từ diagnostic tool
   - Paste vào browser
   - Xem lỗi cụ thể là gì

---

## 📞 CONTACT

Nếu cần help, cung cấp:
1. Screenshot OAuth Consent Screen (Test users section)
2. Screenshot OAuth Client ID (Redirect URIs)
3. Screenshot Diagnostic Tool results
4. Error message chính xác từ Google
