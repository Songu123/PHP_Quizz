# Hệ thống Đăng nhập & Đăng ký

## Tính năng đã thêm

### 🔐 Xác thực người dùng
- **Đăng ký tài khoản**: Form đăng ký với validation đầy đủ
- **Đăng nhập**: Xác thực email/password an toàn
- **Đăng xuất**: Xóa session và chuyển hướng
- **Quản lý session**: Theo dõi trạng thái đăng nhập

### 🎨 Giao diện đẹp mắt
- **Responsive Design**: Tương thích mọi thiết bị
- **Bootstrap 5**: Framework CSS hiện đại
- **Font Awesome**: Icon đẹp và chuyên nghiệp
- **Gradient Background**: Hiệu ứng màu sắc cuốn hút
- **Animation**: Hiệu ứng chuyển động mượt mà

### 📱 Trang chủ thông minh
- **Nội dung động**: Hiển thị khác nhau cho user đã/chưa đăng nhập
- **Welcome Message**: Chào mừng cá nhân hóa
- **Navigation**: Menu điều hướng thông minh
- **Call-to-Action**: Nút hành động rõ ràng

## Cấu trúc Files

```
app/
├── controllers/
│   ├── Auth.php          # Controller xử lý xác thực
│   └── Home.php          # Controller trang chủ
├── models/
│   └── User.php          # Model người dùng
├── views/
│   ├── auth/
│   │   ├── login.php     # Form đăng nhập
│   │   └── register.php  # Form đăng ký
│   ├── home/
│   │   └── index.php     # Trang chủ cập nhật
│   └── partials/
│       ├── header.php    # Header với navigation
│       └── footer.php    # Footer
public/
├── css/
│   └── style.css         # CSS tùy chỉnh
└── js/
    └── main.js           # JavaScript tương tác
```

## Routes mới

### Xác thực
- `GET /auth/login` - Hiển thị form đăng nhập
- `POST /auth/login` - Xử lý đăng nhập
- `GET /auth/register` - Hiển thị form đăng ký
- `POST /auth/register` - Xử lý đăng ký
- `GET /auth/logout` - Đăng xuất

### Trang chủ
- `GET /` - Trang chủ (nội dung thay đổi theo trạng thái đăng nhập)

## Database

### Bảng users
```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE,
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255),
  full_name VARCHAR(100),
  phone VARCHAR(20),
  role ENUM('admin','user') DEFAULT 'user',
  status ENUM('active','inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Tài khoản demo
- **Email**: admin@example.com
- **Password**: password123

## Tính năng bảo mật

### 🔒 Mã hóa mật khẩu
- Sử dụng `password_hash()` với `PASSWORD_DEFAULT`
- Verify với `password_verify()`

### 🛡️ Validation form
- Kiểm tra email hợp lệ
- Mật khẩu tối thiểu 6 ký tự
- Xác nhận mật khẩu khớp
- Kiểm tra email đã tồn tại

### 🔐 Session management
- Lưu trữ thông tin user trong session
- Kiểm tra đăng nhập cho các trang bảo mật
- Xóa session khi đăng xuất

## CSS Features

### 🎨 Design highlights
- **Card-based layout**: Thiết kế dạng thẻ hiện đại
- **Gradient backgrounds**: Nền gradient đẹp mắt
- **Hover effects**: Hiệu ứng hover mượt mà
- **Responsive grid**: Lưới responsive
- **Typography**: Font chữ đẹp và dễ đọc

### 📱 Mobile-friendly
- Breakpoints tối ưu cho mọi màn hình
- Touch-friendly buttons
- Readable font sizes
- Proper spacing

## JavaScript Features

### ⚡ User experience
- **Form validation**: Validation thời gian thực
- **Password strength**: Chỉ báo độ mạnh mật khẩu
- **Loading states**: Hiển thị trạng thái loading
- **Auto-dismiss alerts**: Tự động ẩn thông báo
- **Smooth animations**: Hiệu ứng mượt mà

### 🔧 Utilities
- Alert management
- Date/currency formatting
- Loading overlays
- Form enhancements

## Hướng dẫn sử dụng

### 1. Thiết lập database
```bash
# Import file database.sql vào MySQL
mysql -u root -p < database.sql
```

### 2. Cấu hình
Cập nhật `app/config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'quizz_loq');
define('URLROOT', 'http://localhost/doan_mon');
```

### 3. Truy cập
- Trang chủ: `http://localhost/doan_mon`
- Đăng nhập: `http://localhost/doan_mon/auth/login`
- Đăng ký: `http://localhost/doan_mon/auth/register`

## Tùy chỉnh

### Thêm field mới
1. Cập nhật database schema
2. Sửa form trong views
3. Cập nhật validation trong controller
4. Cập nhật model methods

### Thay đổi giao diện
1. Sửa CSS trong `public/css/style.css`
2. Cập nhật HTML trong views
3. Thêm JavaScript trong `public/js/main.js`

## Lưu ý

- Đảm bảo session được khởi tạo
- Kiểm tra cấu hình database
- Cập nhật URLROOT cho đúng với domain
- Bảo mật: Validate tất cả input từ user
- Performance: Tối ưu queries và assets

## Next Steps

### Tính năng có thể mở rộng
- [ ] Reset password
- [ ] Email verification
- [ ] Social login (Google, Facebook)
- [ ] User profile management
- [ ] Role-based permissions
- [ ] Two-factor authentication
- [ ] Activity logging
- [ ] API endpoints