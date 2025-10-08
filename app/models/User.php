<?php
/**
 * User Model - Model quản lý người dùng
 */
class User extends Model {
    
    protected $table = 'users';
    
    /**
     * Tìm user theo email
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        return $this->db->query($sql, ['email' => $email])->fetch();
    }
    
    /**
     * Tìm user theo username
     */
    public function findByUsername($username) {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username";
        return $this->db->query($sql, ['username' => $username])->fetch();
    }
    
    /**
     * Tạo user mới
     */
    public function createUser($data) {
        // Hash password
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return $this->create($data);
    }
    
    /**
     * Xác thực user
     */
    public function authenticate($username, $password) {
        $user = $this->findByUsername($username);
        
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Đăng ký user mới
     */
    public function register($data) {
        // Tạo username từ email (phần trước @)
        $username = explode('@', $data['email'])[0];
        
        // Loại bỏ ký tự đặc biệt, chỉ giữ chữ và số
        $username = preg_replace('/[^a-zA-Z0-9]/', '', $username);
        
        // Nếu username đã tồn tại, thêm số random
        $originalUsername = $username;
        $counter = 1;
        while ($this->findByUsername($username)) {
            $username = $originalUsername . $counter;
            $counter++;
        }
        
        $sql = "INSERT INTO {$this->table} (username, full_name, email, password, role, created_at) 
                VALUES (:username, :full_name, :email, :password, :role, NOW())";
        
        $params = [
            'username' => $username,
            'full_name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'student' // Mặc định role là student
        ];
        
        if ($this->db->query($sql, $params)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Tìm user theo email để kiểm tra tồn tại
     */
    public function findUserByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $result = $this->db->query($sql, ['email' => $email]);
        
        if ($result->rowCount() > 0) {
            return $result->fetch();
        } else {
            return false;
        }
    }
    
    /**
     * Đăng nhập user
     */
    public function login($email, $password) {
        $row = $this->findUserByEmail($email);
        
        if ($row) {
            $hashed_password = $row->password;
            if (password_verify($password, $hashed_password)) {
                return $row;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    /**
     * Tìm user theo Google ID
     */
    public function findByGoogleId($googleId) {
        $sql = "SELECT * FROM {$this->table} WHERE google_id = :google_id";
        $result = $this->db->query($sql, ['google_id' => $googleId]);
        
        if ($result->rowCount() > 0) {
            return $result->fetch();
        }
        return false;
    }
    
    /**
     * Tạo hoặc cập nhật user từ Google
     */
    public function createOrUpdateFromGoogle($googleUser) {
        // Kiểm tra xem user đã tồn tại chưa (theo email hoặc google_id)
        $existingUser = $this->findUserByEmail($googleUser['email']);
        
        if ($existingUser) {
            // Cập nhật google_id nếu chưa có
            if (empty($existingUser->google_id)) {
                $sql = "UPDATE {$this->table} SET google_id = :google_id, avatar = :avatar WHERE id = :id";
                $this->db->query($sql, [
                    'google_id' => $googleUser['google_id'],
                    'avatar' => $googleUser['picture'],
                    'id' => $existingUser->id
                ]);
            }
            return $existingUser;
        }
        
        // Tạo user mới
        // Tạo username từ email (phần trước @)
        $username = explode('@', $googleUser['email'])[0];
        
        // Loại bỏ ký tự đặc biệt, chỉ giữ chữ và số
        $username = preg_replace('/[^a-zA-Z0-9]/', '', $username);
        
        // Nếu username rỗng sau khi lọc, dùng "user" + random
        if (empty($username)) {
            $username = 'user' . rand(1000, 9999);
        }
        
        // Nếu username đã tồn tại, thêm số random
        $originalUsername = $username;
        $counter = 1;
        while ($this->findByUsername($username)) {
            $username = $originalUsername . $counter;
            $counter++;
        }
        
        $sql = "INSERT INTO {$this->table} (username, full_name, email, google_id, avatar, role, created_at) 
                VALUES (:username, :full_name, :email, :google_id, :avatar, :role, NOW())";
        
        $params = [
            'username' => $username,
            'full_name' => $googleUser['name'],
            'email' => $googleUser['email'],
            'google_id' => $googleUser['google_id'],
            'avatar' => $googleUser['picture'],
            'role' => 'student' // Mặc định role là student
        ];
        
        if ($this->db->query($sql, $params)) {
            return $this->findUserByEmail($googleUser['email']);
        }
        
        return false;
    }
    
    /**
     * Cập nhật mật khẩu user
     */
    public function updatePassword($email, $newPassword) {
        // Hash password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $sql = "UPDATE {$this->table} SET password = :password WHERE email = :email";
        $params = [
            'password' => $hashedPassword,
            'email' => $email
        ];
        
        return $this->db->query($sql, $params);
    }
}