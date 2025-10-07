# Hướng dẫn cấu hình sử dụng XAMPP MySQL

## Cấu hình hiện tại

Ứng dụng đã được cấu hình để sử dụng MySQL của XAMPP trên local machine thay vì database trong Docker container.

## Các bước thiết lập:

### 1. Khởi động XAMPP
- Mở XAMPP Control Panel
- Start **Apache** và **MySQL**
- Đảm bảo MySQL chạy trên port 3306 (mặc định)

### 2. Tạo database
- Truy cập phpMyAdmin: `http://localhost/phpmyadmin`
- Import file `database.sql` để tạo database và bảng
- Hoặc chạy SQL commands trong file `database.sql`

### 3. Kiểm tra cấu hình

**File: `app/config/config.php`**
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'quizz_loq');
```

### 4. Chạy ứng dụng

**Tùy chọn 1: Chạy trực tiếp qua XAMPP**
- Truy cập: `http://localhost/doan_mon`

**Tùy chọn 2: Chạy qua Docker (recommended)**
- Đảm bảo XAMPP MySQL đang chạy
- Chạy lệnh: `docker-compose up -d`
- Truy cập: `http://localhost:8080`
- phpMyAdmin qua Docker: `http://localhost:8081`

## Lưu ý quan trọng:

1. **XAMPP MySQL phải chạy trước** khi start Docker containers
2. Docker containers sẽ kết nối tới XAMPP MySQL qua `host.docker.internal`
3. Nếu gặp lỗi kết nối, kiểm tra:
   - XAMPP MySQL có đang chạy không
   - Port 3306 có bị chặn không
   - Firewall có cho phép kết nối không

## Cấu trúc Database:

- **Database**: `quizz_loq`
- **Tables**: `users`, `posts` (mẫu)
- **Default user**: 
  - Username: `admin`
  - Password: `password` (được hash)

## Troubleshooting:

**Lỗi "Connection refused":**
- Kiểm tra XAMPP MySQL đang chạy
- Kiểm tra port 3306 không bị chiếm dụng

**Lỗi "Access denied":**
- Kiểm tra username/password trong config.php
- Đảm bảo user `root` có quyền truy cập từ localhost

**Docker không kết nối được:**
- Thử thay `host.docker.internal` bằng IP local của máy
- Trên Linux: thêm `extra_hosts` trong docker-compose.yml