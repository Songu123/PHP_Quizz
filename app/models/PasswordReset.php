<?php
/**
 * PasswordReset Model
 * Xử lý reset password với mã code qua email
 */
class PasswordReset extends Model {
    
    protected $table = 'password_resets';
    
    /**
     * Tạo mã reset mới
     */
    public function createResetToken($email) {
        // Tạo mã code 6 số
        $token = sprintf("%06d", mt_rand(100000, 999999));
        
        // Xóa các token cũ của email này (nếu có)
        $this->deleteTokensByEmail($email);
        
        // Token hết hạn sau 15 phút - Sử dụng DATE_ADD trong SQL để tránh lỗi timezone
        $sql = "INSERT INTO {$this->table} (email, token, created_at, expires_at) 
                VALUES (:email, :token, NOW(), DATE_ADD(NOW(), INTERVAL 15 MINUTE))";
        
        $params = [
            'email' => $email,
            'token' => $token
        ];
        
        if ($this->db->query($sql, $params)) {
            return $token;
        }
        
        return false;
    }
    
    /**
     * Xác thực mã token
     */
    public function verifyToken($email, $token) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE email = :email 
                AND token = :token 
                AND used = 0 
                AND expires_at > NOW()";
        
        $result = $this->db->query($sql, [
            'email' => $email,
            'token' => $token
        ]);
        
        if ($result->rowCount() > 0) {
            return $result->fetch();
        }
        
        return false;
    }
    
    /**
     * Đánh dấu token đã sử dụng
     */
    public function markTokenAsUsed($email, $token) {
        $sql = "UPDATE {$this->table} 
                SET used = 1 
                WHERE email = :email AND token = :token";
        
        return $this->db->query($sql, [
            'email' => $email,
            'token' => $token
        ]);
    }
    
    /**
     * Xóa tất cả token của email
     */
    public function deleteTokensByEmail($email) {
        $sql = "DELETE FROM {$this->table} WHERE email = :email";
        return $this->db->query($sql, ['email' => $email]);
    }
    
    /**
     * Kiểm tra token có hết hạn không
     */
    public function isTokenExpired($email, $token) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE email = :email 
                AND token = :token 
                AND expires_at < NOW()";
        
        $result = $this->db->query($sql, [
            'email' => $email,
            'token' => $token
        ]);
        
        return $result->rowCount() > 0;
    }
    
    /**
     * Lấy thời gian còn lại của token (phút)
     */
    public function getTokenTimeLeft($email, $token) {
        $sql = "SELECT TIMESTAMPDIFF(MINUTE, NOW(), expires_at) as minutes_left 
                FROM {$this->table} 
                WHERE email = :email AND token = :token AND used = 0";
        
        $result = $this->db->query($sql, [
            'email' => $email,
            'token' => $token
        ]);
        
        if ($result->rowCount() > 0) {
            $row = $result->fetch();
            return max(0, $row->minutes_left);
        }
        
        return 0;
    }
    
    /**
     * Xóa token hết hạn (cleanup)
     */
    public function cleanupExpiredTokens() {
        $sql = "DELETE FROM {$this->table} WHERE expires_at < NOW()";
        return $this->db->query($sql);
    }
    
    /**
     * Kiểm tra token chi tiết (với thông tin thời gian còn lại)
     */
    public function verifyTokenWithDetails($email, $token) {
        $sql = "SELECT *, 
                TIMESTAMPDIFF(SECOND, NOW(), expires_at) as seconds_left,
                TIMESTAMPDIFF(MINUTE, NOW(), expires_at) as minutes_left
                FROM {$this->table} 
                WHERE email = :email AND token = :token AND used = 0 
                LIMIT 1";
        
        $result = $this->db->query($sql, [
            'email' => $email,
            'token' => $token
        ]);
        
        if ($result->rowCount() > 0) {
            return $result->fetch();
        }
        
        return false;
    }
}
