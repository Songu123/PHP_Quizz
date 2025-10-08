<?php
/**
 * VÍ DỤ SỬ DỤNG DATABASE SINGLETON PATTERN
 * File này demo cách sử dụng Database class với Singleton pattern
 */

require_once 'app/config/config.php';
require_once 'app/core/Database.php';

echo "<h2>Demo Singleton Pattern - Database Connection</h2>";

// ============================================
// 1. LẤY INSTANCE DUY NHẤT
// ============================================
echo "<h3>1. Lấy Database Instance (Singleton)</h3>";

$db1 = Database::getInstance();
$db2 = Database::getInstance();

// Kiểm tra cả 2 biến đều trỏ đến cùng 1 instance
if ($db1 === $db2) {
    echo "✅ <strong>ĐÚNG:</strong> \$db1 và \$db2 là cùng một instance!<br>";
    echo "Object ID của \$db1: " . spl_object_id($db1) . "<br>";
    echo "Object ID của \$db2: " . spl_object_id($db2) . "<br><br>";
} else {
    echo "❌ Có lỗi: Không phải singleton<br><br>";
}

// ============================================
// 2. KHÔNG THỂ TẠO INSTANCE MỚI BẰNG NEW
// ============================================
echo "<h3>2. Không thể tạo instance bằng 'new'</h3>";
try {
    // Điều này sẽ gây lỗi vì constructor là private
    // $db3 = new Database(); // Uncommment để test
    echo "✅ Constructor là private, không thể new trực tiếp<br><br>";
} catch (Error $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "<br><br>";
}

// ============================================
// 3. KHÔNG THỂ CLONE
// ============================================
echo "<h3>3. Không thể clone instance</h3>";
try {
    // $db3 = clone $db1; // Uncommment để test
    echo "✅ Method __clone() là private, không thể clone<br><br>";
} catch (Error $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "<br><br>";
}

// ============================================
// 4. SỬ DỤNG DATABASE ĐỂ QUERY
// ============================================
echo "<h3>4. Sử dụng Database để query</h3>";

try {
    $db = Database::getInstance();
    
    // Ví dụ 1: Query đơn giản
    echo "<strong>Ví dụ 1: Lấy tất cả users</strong><br>";
    $db->query("SELECT * FROM users LIMIT 5");
    $users = $db->fetchAll();
    
    echo "Số lượng users: " . count($users) . "<br>";
    foreach ($users as $user) {
        echo "- ID: {$user->id}, Name: {$user->name}, Email: {$user->email}<br>";
    }
    echo "<br>";
    
    // Ví dụ 2: Query với parameters (cách 1 - truyền trực tiếp)
    echo "<strong>Ví dụ 2: Lấy user theo ID (params trực tiếp)</strong><br>";
    $db->query("SELECT * FROM users WHERE id = ?", [1]);
    $user = $db->fetch();
    
    if ($user) {
        echo "User found: {$user->name} ({$user->email})<br><br>";
    }
    
    // Ví dụ 3: Query với bind parameters (cách 2 - bind từng param)
    echo "<strong>Ví dụ 3: Lấy user theo email (bind riêng)</strong><br>";
    $db->query("SELECT * FROM users WHERE email = :email");
    $db->bind(':email', 'admin@example.com');
    $db->execute();
    $user = $db->fetch();
    
    if ($user) {
        echo "User found: {$user->name} ({$user->email})<br><br>";
    }
    
    // Ví dụ 4: Insert data
    echo "<strong>Ví dụ 4: Insert user mới</strong><br>";
    $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $params = [
        'Test User ' . time(),
        'test' . time() . '@example.com',
        password_hash('password123', PASSWORD_DEFAULT),
        'student'
    ];
    
    $db->query($sql, $params);
    
    if ($db->rowCount() > 0) {
        $lastId = $db->lastInsertId();
        echo "✅ Insert thành công! User ID: {$lastId}<br><br>";
    }
    
    // Ví dụ 5: Update data
    echo "<strong>Ví dụ 5: Update user</strong><br>";
    $sql = "UPDATE users SET name = :name WHERE id = :id";
    $db->query($sql);
    $db->bind(':name', 'Updated Name');
    $db->bind(':id', 1);
    $db->execute();
    
    echo "Số dòng được update: " . $db->rowCount() . "<br><br>";
    
    // Ví dụ 6: Transaction
    echo "<strong>Ví dụ 6: Sử dụng Transaction</strong><br>";
    
    try {
        $db->beginTransaction();
        
        // Insert user
        $db->query("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)", [
            'Transaction User',
            'transaction' . time() . '@example.com',
            password_hash('password123', PASSWORD_DEFAULT),
            'student'
        ]);
        $userId = $db->lastInsertId();
        
        // Có thể thêm các query khác...
        
        $db->commit();
        echo "✅ Transaction thành công! User ID: {$userId}<br><br>";
        
    } catch (Exception $e) {
        $db->rollBack();
        echo "❌ Transaction failed: " . $e->getMessage() . "<br><br>";
    }
    
    // Ví dụ 7: Sử dụng PDO connection trực tiếp
    echo "<strong>Ví dụ 7: Lấy PDO connection để sử dụng trực tiếp</strong><br>";
    $pdo = $db->getConnection();
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    echo "Tổng số users trong database: {$result->total}<br><br>";
    
} catch (Exception $e) {
    echo "❌ <strong>Lỗi:</strong> " . $e->getMessage() . "<br><br>";
}

// ============================================
// 5. LỢI ÍCH CỦA SINGLETON PATTERN
// ============================================
echo "<h3>5. Lợi ích của Singleton Pattern</h3>";
echo "<ul>";
echo "<li>✅ <strong>Tiết kiệm tài nguyên:</strong> Chỉ có 1 kết nối database duy nhất trong toàn bộ ứng dụng</li>";
echo "<li>✅ <strong>Quản lý dễ dàng:</strong> Không lo việc tạo nhiều connection không cần thiết</li>";
echo "<li>✅ <strong>Thread-safe:</strong> Đảm bảo không có xung đột khi nhiều phần của code cùng truy cập</li>";
echo "<li>✅ <strong>Global access point:</strong> Có thể truy cập instance từ bất kỳ đâu trong code</li>";
echo "<li>✅ <strong>Lazy initialization:</strong> Instance chỉ được tạo khi cần thiết (lần đầu gọi getInstance())</li>";
echo "</ul>";

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Singleton Pattern Demo</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h2 {
            color: #333;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        h3 {
            color: #667eea;
            margin-top: 30px;
        }
        code {
            background: #272822;
            color: #f8f8f2;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .code-block {
            background: #272822;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            margin: 10px 0;
        }
        ul {
            line-height: 2;
        }
    </style>
</head>
<body>
    
<h3>6. Cách sử dụng trong Model</h3>
<div class="code-block">
<pre>
class User extends Model {
    protected $table = 'users';
    
    public function findByEmail($email) {
        // Lấy database instance (singleton)
        $db = Database::getInstance();
        
        // Query
        $db->query("SELECT * FROM {$this->table} WHERE email = :email");
        $db->bind(':email', $email);
        
        return $db->fetch();
    }
    
    public function getAllUsers() {
        $db = Database::getInstance();
        $db->query("SELECT * FROM {$this->table}");
        return $db->fetchAll();
    }
}
</pre>
</div>

<h3>7. Best Practices</h3>
<ul>
    <li>✅ Luôn sử dụng <code>Database::getInstance()</code> thay vì tạo instance mới</li>
    <li>✅ Sử dụng prepared statements với bind để tránh SQL injection</li>
    <li>✅ Sử dụng transactions cho các thao tác phức tạp (insert/update nhiều bảng)</li>
    <li>✅ Luôn handle exceptions khi thao tác với database</li>
    <li>✅ Trong production, nên log lỗi thay vì hiển thị trực tiếp</li>
</ul>

</body>
</html>
