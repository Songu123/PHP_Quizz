# Đồ Án Môn - PHP MVC Framework

Ứng dụng PHP MVC được xây dựng từ đầu với cấu trúc rõ ràng và dễ mở rộng.

## Cấu trúc thư mục

```
doan_mon/
├── app/
│   ├── config/
│   │   └── config.php          # Cấu hình ứng dụng
│   ├── controllers/
│   │   └── Home.php            # Controller mặc định
│   ├── core/
│   │   ├── App.php             # Core application class
│   │   ├── Controller.php      # Base controller class
│   │   ├── Database.php        # Database connection class
│   │   └── Model.php           # Base model class
│   ├── models/
│   │   └── User.php            # User model mẫu
│   └── views/
│       ├── home/
│       │   ├── index.php       # Trang chủ
│       │   └── about.php       # Trang giới thiệu
│       └── partials/
│           ├── header.php      # Header template
│           └── footer.php      # Footer template
├── public/
│   ├── css/
│   │   └── style.css           # CSS chính
│   ├── js/
│   │   └── main.js             # JavaScript chính
│   ├── images/                 # Thư mục hình ảnh
│   ├── .htaccess               # URL rewriting
│   └── index.php               # Entry point
├── .htaccess                   # Root htaccess
└── README.md                   # File này
```

## Cài đặt

1. **Clone hoặc copy project vào thư mục XAMPP:**
   ```
   c:\xampp\htdocs\doan_mon\
   ```

2. **Cấu hình database trong `app/config/config.php`:**
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'doan_mon');
   ```

3. **Tạo database `doan_mon` trong phpMyAdmin**

4. **Khởi động XAMPP và truy cập:**
   ```
   http://localhost/doan_mon
   ```

## URL Routing

Framework sử dụng URL pattern đơn giản:
```
http://localhost/doan_mon/[controller]/[method]/[params]
```

Ví dụ:
- `http://localhost/doan_mon/` → Home::index()
- `http://localhost/doan_mon/home/about` → Home::about()
- `http://localhost/doan_mon/user/profile/123` → User::profile(123)

## Sử dụng

### Tạo Controller mới

```php
<?php
class MyController extends Controller {
    public function index() {
        $data = ['title' => 'My Page'];
        $this->view('my/index', $data);
    }
}
```

### Tạo Model mới

```php
<?php
class MyModel extends Model {
    protected $table = 'my_table';
    
    public function customMethod() {
        // Custom logic
    }
}
```


## Tính năng

- ✅ URL Routing tự động
- ✅ MVC Architecture
- ✅ Database PDO với prepared statements
- ✅ CRUD operations cơ bản
- ✅ Session management
- ✅ Flash messages
- ✅ Responsive Bootstrap UI
- ✅ Auto-loading classes

## Yêu cầu hệ thống

- PHP 7.4+
- MySQL 5.7+
- Apache với mod_rewrite
- XAMPP/WAMP/LAMP#