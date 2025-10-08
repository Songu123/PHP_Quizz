-- Migration: Password Reset Tokens
-- Tạo bảng lưu mã reset password

USE quizz_loq;

-- Tạo bảng password_resets
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(6) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    INDEX idx_email (email),
    INDEX idx_token (token),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Xóa token cũ hơn 24 giờ (cleanup)
DELETE FROM password_resets WHERE expires_at < NOW();

-- Kiểm tra
DESCRIBE password_resets;
