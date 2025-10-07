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
        $sql = "INSERT INTO {$this->table} (full_name, email, password, created_at) VALUES (:full_name, :email, :password, NOW())";
        
        $params = [
            'full_name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password']
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
}