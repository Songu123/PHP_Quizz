<?php
/**
 * Home Controller - Controller mặc định
 */
class Home extends Controller {
    
    public function __construct() {
        // Khởi tạo session nếu chưa có
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Trang chủ
     */
    public function index() {
        $data = [
            'title' => 'Trang Chủ',
            'message' => 'Chào mừng đến với ứng dụng MVC PHP!'
        ];
        
        $this->view('home/index', $data);
    }
    
    /**
     * Trang giới thiệu
     */
    public function about() {
        $data = [
            'title' => 'Giới Thiệu',
            'message' => 'Đây là trang giới thiệu về ứng dụng'
        ];
        
        $this->view('home/about', $data);
    }
}