-- Migration để thêm hỗ trợ Google OAuth
-- Chạy script này để cập nhật database

USE quizz_loq;

-- Thêm cột google_id để lưu Google user ID
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS google_id VARCHAR(255) UNIQUE AFTER email;

-- Thêm cột avatar để lưu ảnh đại diện từ Google
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS avatar TEXT AFTER google_id;

-- Cho phép password NULL (cho user đăng nhập bằng Google)
ALTER TABLE users 
MODIFY COLUMN password VARCHAR(255) NULL;

-- Thêm default value cho role
ALTER TABLE users 
MODIFY COLUMN role ENUM('admin','teacher','student') NOT NULL DEFAULT 'student';

-- Thêm index cho google_id để tăng tốc độ tìm kiếm
CREATE INDEX IF NOT EXISTS idx_google_id ON users(google_id);

-- Kiểm tra cấu trúc bảng sau khi thay đổi
DESCRIBE users;
