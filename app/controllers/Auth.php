<?php
/**
 * Auth Controller - Xử lý đăng ký, đăng nhập
 */
class Auth extends Controller {
    
    private $userModel;
    
    public function __construct() {
        // Khởi tạo session nếu chưa có
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Load model User
        $this->userModel = $this->model('User');
    }
    
    /**
     * Trang đăng nhập
     */
    public function login() {
        // Nếu đã đăng nhập, chuyển về trang chủ
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT);
            exit();
        }
<<<<<<< HEAD
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Xử lý form đăng nhập
=======

        $data = [
            'title' => 'Đăng Nhập',
            'email' => '',
            'password' => '',
            'email_err' => '',
            'password_err' => ''
        ];

        // Xử lý form đăng nhập nếu là POST request
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
>>>>>>> da146757a543b40ea68cd15ebeaea8cc0cc75c5d
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data['email'] = trim($_POST['email'] ?? '');
            $data['password'] = trim($_POST['password'] ?? '');
            
            // Validate email
            if (empty($data['email'])) {
                $data['email_err'] = 'Vui lòng nhập email';
            }
            
            // Validate password 
            if (empty($data['password'])) {
                $data['password_err'] = 'Vui lòng nhập mật khẩu';
            }
            
            // Kiểm tra user tồn tại
            if (empty($data['email_err']) && empty($data['password_err'])) {
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);
                
                if ($loggedInUser) {
                    // Tạo session
                    $this->createUserSession($loggedInUser);
                    return;
                } else {
                    $data['password_err'] = 'Email hoặc mật khẩu không đúng';
                }
            }
        }

        // Load view
        $this->view('auth/login', $data);
    }
    
    /**
     * Trang đăng ký
     */
    public function register() {
        // Nếu đã đăng nhập, chuyển về trang chủ
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT);
            exit();
        }
<<<<<<< HEAD
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Xử lý form đăng ký
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            // Xử lý form đăng ký
=======

        $data = [
            'title' => 'Đăng Ký',
            'name' => '',
            'email' => '',
            'password' => '',
            'confirm_password' => '',
            'name_err' => '',
            'email_err' => '',
            'password_err' => '',
            'confirm_password_err' => ''
        ];

        // Xử lý form đăng ký nếu là POST request
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
>>>>>>> da146757a543b40ea68cd15ebeaea8cc0cc75c5d
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data['name'] = trim($_POST['name'] ?? '');
            $data['email'] = trim($_POST['email'] ?? '');
            $data['password'] = trim($_POST['password'] ?? '');
            $data['confirm_password'] = trim($_POST['confirm_password'] ?? '');
            
            // Validate name
            if (empty($data['name'])) {
                $data['name_err'] = 'Vui lòng nhập họ tên';
            }
            
            // Validate email
            if (empty($data['email'])) {
                $data['email_err'] = 'Vui lòng nhập email';
            } elseif ($this->userModel->findUserByEmail($data['email'])) {
                $data['email_err'] = 'Email đã được sử dụng';
            }
            
            // Validate password
            if (empty($data['password'])) {
                $data['password_err'] = 'Vui lòng nhập mật khẩu';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Mật khẩu phải có ít nhất 6 ký tự';
            }
            
            // Validate confirm password
            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Vui lòng xác nhận mật khẩu';
            } elseif ($data['password'] != $data['confirm_password']) {
                $data['confirm_password_err'] = 'Mật khẩu xác nhận không khớp';
            }
            
            // Make sure errors are empty
            if (empty($data['name_err']) && empty($data['email_err']) && 
                empty($data['password_err']) && empty($data['confirm_password_err'])) {
                
                // Hash password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                
                // Register user
                if ($this->userModel->register($data)) {
                    $_SESSION['message'] = [
                        'type' => 'success',
                        'text' => 'Đăng ký thành công! Vui lòng đăng nhập.'
                    ];
                    header('Location: ' . URLROOT . '/auth/login');
                    exit();
                } else {
                    die('Có lỗi xảy ra');
                }
            }
        }

        // Load view
        $this->view('auth/register', $data);
    }
    
    /**
     * Đăng xuất
     */
    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        session_destroy();
        session_start();
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Bạn đã đăng xuất thành công!'
        ];
        header('Location: ' . URLROOT . '/auth/login');
        exit();
    }
    
    /**
     * Tạo session cho user
     */
    public function createUserSession($user) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->full_name;
        header('Location: ' . URLROOT);
        exit();
    }
    
    /**
     * Kiểm tra đã đăng nhập
     */
    public function isLoggedIn() {
        if (isset($_SESSION['user_id'])) {
            return true;
        } else {
            return false;
        }
    }
}