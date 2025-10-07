<?php
/**
 * Class Controller - Lớp controller cơ sở
 */
class Controller {
    
    /**
     * Load model
     */
    public function model($model) {
        require_once APP . '/models/' . $model . '.php';
        return new $model();
    }
    
    /**
     * Load view
     */
    public function view($view, $data = []) {
        // Chuyển array thành variables
        extract($data);
        
        require_once APP . '/views/' . $view . '.php';
    }
    
    /**
     * Redirect
     */
    public function redirect($url) {
        header('Location: ' . $url);
        exit();
    }
    
    /**
     * Set session message
     */
    public function setMessage($message, $type = 'info') {
        $_SESSION['message'] = [
            'text' => $message,
            'type' => $type
        ];
    }
    
    /**
     * Get session message
     */
    public function getMessage() {
        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            unset($_SESSION['message']);
            return $message;
        }
        return null;
    }
}