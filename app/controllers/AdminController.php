<?php
/**
 * Admin Controller - Controller quản lý hệ thống
 */
class AdminController extends Controller {
    
    private $userModel;
    private $subjectModel;
    private $questionModel;
    private $examModel;
    private $examAttemptModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->subjectModel = new Subject();
        $this->questionModel = new Question();
        $this->examModel = new Exam();
        $this->examAttemptModel = new ExamAttempt();
        
        // Kiểm tra đăng nhập và quyền admin
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }
        
        $user = $this->userModel->findById($_SESSION['user_id']);
        if ($user->role !== 'admin') {
            header('Location: /dashboard');
            exit;
        }
    }
    
    /**
     * Dashboard admin
     */
    public function dashboard() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        // Lấy thống kê tổng quan
        $stats = $this->getSystemStats();
        
        // Lấy hoạt động gần đây
        $recentActivities = $this->getRecentActivities();
        
        $this->view('admin/dashboard', [
            'user' => $user,
            'stats' => $stats,
            'recentActivities' => $recentActivities
        ]);
    }
    
    /**
     * Quản lý người dùng
     */
    public function users() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        $role = $_GET['role'] ?? null;
        $status = $_GET['status'] ?? null;
        
        // Lấy danh sách người dùng
        $conditions = [];
        if ($role) $conditions['role'] = $role;
        if ($status) $conditions['status'] = $status;
        
        $users = $this->userModel->findWhere($conditions, 'created_at DESC');
        
        $this->view('admin/users', [
            'user' => $user,
            'users' => $users,
            'selectedRole' => $role,
            'selectedStatus' => $status
        ]);
    }
    
    /**
     * Tạo người dùng mới
     */
    public function createUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'password' => $_POST['password'],
                'full_name' => trim($_POST['full_name']),
                'role' => $_POST['role'],
                'status' => $_POST['status'] ?? 'active'
            ];
            
            // Validate
            $errors = $this->validateUserData($data);
            if (empty($errors)) {
                if ($this->userModel->createUser($data)) {
                    $this->redirect('/admin/users', 'Tạo người dùng thành công!', 'success');
                    return;
                } else {
                    $errors['general'] = 'Có lỗi xảy ra khi tạo người dùng!';
                }
            }
            
            $this->view('admin/create_user', [
                'user' => $this->userModel->findById($_SESSION['user_id']),
                'errors' => $errors,
                'data' => $data
            ]);
        } else {
            $this->view('admin/create_user', [
                'user' => $this->userModel->findById($_SESSION['user_id'])
            ]);
        }
    }
    
    /**
     * Chỉnh sửa người dùng
     */
    public function editUser($id) {
        $targetUser = $this->userModel->findById($id);
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if (!$targetUser) {
            $this->redirect('/admin/users', 'Người dùng không tồn tại!', 'error');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'full_name' => trim($_POST['full_name']),
                'role' => $_POST['role'],
                'status' => $_POST['status']
            ];
            
            // Nếu có password mới
            if (!empty($_POST['password'])) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }
            
            if ($this->userModel->update($id, $data)) {
                $this->redirect('/admin/users', 'Cập nhật người dùng thành công!', 'success');
                return;
            } else {
                $error = 'Có lỗi xảy ra khi cập nhật!';
            }
        }
        
        $this->view('admin/edit_user', [
            'user' => $user,
            'targetUser' => $targetUser,
            'error' => $error ?? null
        ]);
    }
    
    /**
     * Xóa người dùng
     */
    public function deleteUser($id) {
        $targetUser = $this->userModel->findById($id);
        
        if (!$targetUser) {
            $this->redirect('/admin/users', 'Người dùng không tồn tại!', 'error');
            return;
        }
        
        // Không cho phép xóa chính mình
        if ($id == $_SESSION['user_id']) {
            $this->redirect('/admin/users', 'Không thể xóa chính mình!', 'error');
            return;
        }
        
        if ($this->userModel->delete($id)) {
            $this->redirect('/admin/users', 'Xóa người dùng thành công!', 'success');
        } else {
            $this->redirect('/admin/users', 'Có lỗi xảy ra!', 'error');
        }
    }
    
    /**
     * Quản lý môn học
     */
    public function subjects() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        $subjects = $this->subjectModel->findAll();
        
        // Thêm thông tin giáo viên và thống kê
        foreach ($subjects as $subject) {
            $teacher = $this->userModel->findById($subject->teacher_id);
            $subject->teacher_name = $teacher ? $teacher->full_name : 'Chưa có';
            $subject->question_count = $this->subjectModel->getQuestionCount($subject->id);
        }
        
        $this->view('admin/subjects', [
            'user' => $user,
            'subjects' => $subjects
        ]);
    }
    
    /**
     * Báo cáo thống kê
     */
    public function reports() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        // Thống kê theo thời gian
        $timeRange = $_GET['range'] ?? '30'; // 30 ngày mặc định
        $stats = $this->getDetailedStats($timeRange);
        
        $this->view('admin/reports', [
            'user' => $user,
            'stats' => $stats,
            'timeRange' => $timeRange
        ]);
    }
    
    /**
     * Cài đặt hệ thống
     */
    public function settings() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Xử lý cập nhật cài đặt
            $settings = [
                'site_name' => $_POST['site_name'] ?? 'Quiz System',
                'site_description' => $_POST['site_description'] ?? '',
                'allow_registration' => isset($_POST['allow_registration']),
                'default_exam_duration' => intval($_POST['default_exam_duration']) ?: 60,
                'max_attempts_per_exam' => intval($_POST['max_attempts_per_exam']) ?: 1
            ];
            
            // Lưu cài đặt vào session hoặc file config
            $_SESSION['settings'] = $settings;
            $this->redirect('/admin/settings', 'Cập nhật cài đặt thành công!', 'success');
            return;
        }
        
        $settings = $_SESSION['settings'] ?? [
            'site_name' => 'Quiz System',
            'site_description' => 'Hệ thống thi trắc nghiệm online',
            'allow_registration' => true,
            'default_exam_duration' => 60,
            'max_attempts_per_exam' => 1
        ];
        
        $this->view('admin/settings', [
            'user' => $user,
            'settings' => $settings
        ]);
    }
    
    /**
     * Backup dữ liệu
     */
    public function backup() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->createBackup();
            return;
        }
        
        $this->view('admin/backup', [
            'user' => $user
        ]);
    }
    
    /**
     * Lấy thống kê tổng quan hệ thống
     */
    private function getSystemStats() {
        $db = new Database();
        
        // Tổng số người dùng
        $sql = "SELECT COUNT(*) as total, 
                       SUM(CASE WHEN role = 'student' THEN 1 ELSE 0 END) as students,
                       SUM(CASE WHEN role = 'teacher' THEN 1 ELSE 0 END) as teachers,
                       SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_users
                FROM users";
        $userStats = $db->query($sql)->fetch();
        
        // Tổng số môn học
        $sql = "SELECT COUNT(*) as total, 
                       SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active
                FROM subjects";
        $subjectStats = $db->query($sql)->fetch();
        
        // Tổng số câu hỏi
        $sql = "SELECT COUNT(*) as total,
                       SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active
                FROM questions";
        $questionStats = $db->query($sql)->fetch();
        
        // Tổng số đề thi
        $sql = "SELECT COUNT(*) as total,
                       SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                       SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
                FROM exams";
        $examStats = $db->query($sql)->fetch();
        
        // Tổng số lượt thi
        $sql = "SELECT COUNT(*) as total,
                       SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                       AVG(CASE WHEN status = 'completed' THEN percentage ELSE NULL END) as avg_score
                FROM exam_attempts";
        $attemptStats = $db->query($sql)->fetch();
        
        return [
            'users' => $userStats,
            'subjects' => $subjectStats,
            'questions' => $questionStats,
            'exams' => $examStats,
            'attempts' => $attemptStats
        ];
    }
    
    /**
     * Lấy hoạt động gần đây
     */
    private function getRecentActivities() {
        $db = new Database();
        
        // Lượt thi gần đây
        $sql = "SELECT ea.*, e.title as exam_title, u.full_name as student_name
                FROM exam_attempts ea
                JOIN exams e ON ea.exam_id = e.id
                JOIN users u ON ea.student_id = u.id
                ORDER BY ea.created_at DESC
                LIMIT 10";
        
        return $db->query($sql)->fetchAll();
    }
    
    /**
     * Lấy thống kê chi tiết theo thời gian
     */
    private function getDetailedStats($days) {
        $db = new Database();
        
        // Thống kê đăng ký người dùng theo ngày
        $sql = "SELECT DATE(created_at) as date, COUNT(*) as count
                FROM users 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                GROUP BY DATE(created_at)
                ORDER BY date DESC";
        $userRegistrations = $db->query($sql, ['days' => $days])->fetchAll();
        
        // Thống kê thi theo ngày
        $sql = "SELECT DATE(created_at) as date, COUNT(*) as count
                FROM exam_attempts 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                AND status = 'completed'
                GROUP BY DATE(created_at)
                ORDER BY date DESC";
        $examAttempts = $db->query($sql, ['days' => $days])->fetchAll();
        
        return [
            'user_registrations' => $userRegistrations,
            'exam_attempts' => $examAttempts
        ];
    }
    
    /**
     * Validate dữ liệu người dùng
     */
    private function validateUserData($data) {
        $errors = [];
        
        if (empty($data['username'])) {
            $errors['username'] = 'Username không được để trống!';
        } elseif ($this->userModel->findByUsername($data['username'])) {
            $errors['username'] = 'Username đã tồn tại!';
        }
        
        if (empty($data['email'])) {
            $errors['email'] = 'Email không được để trống!';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ!';
        } elseif ($this->userModel->findByEmail($data['email'])) {
            $errors['email'] = 'Email đã tồn tại!';
        }
        
        if (empty($data['password'])) {
            $errors['password'] = 'Password không được để trống!';
        } elseif (strlen($data['password']) < 6) {
            $errors['password'] = 'Password phải có ít nhất 6 ký tự!';
        }
        
        if (empty($data['full_name'])) {
            $errors['full_name'] = 'Họ tên không được để trống!';
        }
        
        if (!in_array($data['role'], ['admin', 'teacher', 'student'])) {
            $errors['role'] = 'Role không hợp lệ!';
        }
        
        return $errors;
    }
    
    /**
     * Tạo backup dữ liệu
     */
    private function createBackup() {
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "backup_quiz_system_{$timestamp}.sql";
        
        // Tạo header cho backup file
        $backup = "-- Quiz System Database Backup\n";
        $backup .= "-- Created: " . date('Y-m-d H:i:s') . "\n\n";
        
        // Backup các bảng
        $tables = [
            'users', 'subjects', 'questions', 'question_options',
            'exams', 'exam_questions', 'exam_attempts', 'student_answers'
        ];
        
        $db = new Database();
        
        foreach ($tables as $table) {
            $backup .= "-- Table: {$table}\n";
            $backup .= "DROP TABLE IF EXISTS `{$table}`;\n";
            
            // Lấy cấu trúc bảng
            $sql = "SHOW CREATE TABLE `{$table}`";
            $result = $db->query($sql)->fetch();
            $backup .= $result->{'Create Table'} . ";\n\n";
            
            // Lấy dữ liệu
            $sql = "SELECT * FROM `{$table}`";
            $rows = $db->query($sql)->fetchAll();
            
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $values = array_map(function($value) {
                        return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                    }, (array)$row);
                    
                    $backup .= "INSERT INTO `{$table}` VALUES (" . implode(', ', $values) . ");\n";
                }
            }
            $backup .= "\n";
        }
        
        // Download file
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($backup));
        
        echo $backup;
        exit;
    }
}