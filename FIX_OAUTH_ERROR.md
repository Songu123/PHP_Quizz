# 🔧 FIX: "Máy chủ không thể xử lý yêu cầu"

## ⚠️ NGUYÊN NHÂN CHÍNH

Credentials cũ (Client ID và Secret) đã bị **LỘ TRÊN GITHUB** và có thể đã bị:
- Google tự động revoke
- Hoặc không còn hoạt động do bảo mật

## ✅ GIẢI PHÁP - LÀM THEO TỪNG BƯỚC

### **Bước 1: Tạo OAuth Client ID MỚI**

1. Vào: https://console.cloud.google.com/apis/credentials

2. **XÓA OAuth Client cũ** (nếu có):
   - Tìm client ID: `340426364237-inse0rjt8ij7t1vlf0d5o4266dk23phc`
   - Click vào → Click **DELETE**

3. **Tạo mới**:
   - Click **"CREATE CREDENTIALS"** → **"OAuth client ID"**
   - Application type: **Web application**
   - Name: `Quiz Website OAuth 2025`
   
4. **Authorized redirect URIs** - Thêm CHÍNH XÁC:
   ```
   http://localhost/doan_mon/public/auth/googlecallback
   ```
   **LƯU Ý:**
   - ✅ Có `/public/`
   - ✅ Không có dấu `/` ở cuối
   - ✅ Chữ thường: `googlecallback`

5. Click **CREATE**

6. **COPY** Client ID và Client Secret mới

### **Bước 2: Cập nhật .env.local**

1. Mở file: `c:\xampp\htdocs\doan_mon\.env.local`

2. Thay thế bằng credentials MỚI:
   ```env
   GOOGLE_CLIENT_ID=YOUR_NEW_CLIENT_ID_HERE.apps.googleusercontent.com
   GOOGLE_CLIENT_SECRET=YOUR_NEW_CLIENT_SECRET_HERE
   ```

3. **SAVE FILE**

### **Bước 3: Cấu hình OAuth Consent Screen**

1. Vào: https://console.cloud.google.com/apis/credentials/consent

2. **Publishing status**: Chọn **"Testing"**

3. **Test users** (QUAN TRỌNG):
   - Click **"ADD USERS"**
   - Thêm email Google của bạn
   - Click **"SAVE"**

4. **Scopes**:
   - Click **"EDIT APP"** → **"Scopes"**
   - Đảm bảo có:
     - `.../auth/userinfo.email`
     - `.../auth/userinfo.profile`

### **Bước 4: Test lại**

1. Restart Apache trong XAMPP

2. Clear browser cache (Ctrl+Shift+Del)

3. Test configuration:
   ```
   http://localhost/doan_mon/public/test_oauth_config.php
   ```

4. Test login:
   ```
   http://localhost/doan_mon/public/auth/login
   ```

5. Click nút **Google**

## 📋 CHECKLIST

- [ ] Xóa OAuth Client ID cũ trong Google Console
- [ ] Tạo OAuth Client ID mới
- [ ] Redirect URI: `http://localhost/doan_mon/public/auth/googlecallback`
- [ ] Copy Client ID mới vào .env.local
- [ ] Copy Client Secret mới vào .env.local
- [ ] Thêm email vào Test users
- [ ] Kiểm tra Scopes
- [ ] Restart Apache
- [ ] Clear browser cache
- [ ] Test OAuth config
- [ ] Test Google login

## 🐛 NẾU VẪN LỖI

### Lỗi: "redirect_uri_mismatch"
**Fix:** Redirect URI trong Google Console phải CHÍNH XÁC:
```
http://localhost/doan_mon/public/auth/googlecallback
```

### Lỗi: "Yêu cầu không hợp lệ"
**Fix:** 
1. Email chưa được thêm vào Test users
2. Credentials chưa được cập nhật đúng
3. Project chưa enable Google+ API

### Lỗi: "invalid_client"
**Fix:** Client ID hoặc Secret sai
- Kiểm tra lại .env.local
- Không có khoảng trắng thừa
- Không có dấu ngoặc kép

## 📞 DEBUG

Test page:
- http://localhost/doan_mon/public/test_oauth_config.php

Page này sẽ cho biết:
- .env.local có được load không
- Client ID/Secret có đúng không
- OAuth URL có được tạo đúng không

## ⚠️ BẢO MẬT

**KHÔNG BAO GIỜ:**
- ❌ Push .env.local lên Git
- ❌ Commit Client ID/Secret trong code
- ❌ Share credentials công khai

**NÊN:**
- ✅ Sử dụng .env.local cho development
- ✅ .env.local đã có trong .gitignore
- ✅ Tạo credentials mới khi bị lộ
