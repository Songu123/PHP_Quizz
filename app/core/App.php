<?php
/**
 * Class App - Lớp chính điều khiển ứng dụng
 */
class App {
    protected $controller = 'Home';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();
        
        // Kiểm tra controller
        if (isset($url[0])) {
            $controllerName = ucfirst($url[0]);
            if (file_exists(APP . '/controllers/' . $controllerName . '.php')) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }
        
        // Require controller
        require_once APP . '/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;
        
        // Kiểm tra method
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }
        
        // Lấy parameters
        $this->params = $url ? array_values($url) : [];
        
        // Gọi method với parameters
        call_user_func_array([$this->controller, $this->method], $this->params);
    }
    
    public function parseUrl() {
        if (isset($_GET['url'])) {
            return $url = explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}