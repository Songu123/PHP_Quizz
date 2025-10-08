<?php
/**
 * Google OAuth Helper Class
 * Xử lý authentication với Google OAuth 2.0
 */

class GoogleOAuth {
    
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    
    public function __construct() {
        $this->clientId = GOOGLE_CLIENT_ID;
        $this->clientSecret = GOOGLE_CLIENT_SECRET;
        $this->redirectUri = GOOGLE_REDIRECT_URI;
    }
    
    /**
     * Tạo URL để redirect user đến Google Login
     */
    public function getAuthUrl() {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => implode(' ', GOOGLE_OAUTH_SCOPE),
            'access_type' => 'online',
            'state' => $this->generateState()
        ];
        
        // Lưu state vào session để verify sau
        $_SESSION['oauth_state'] = $params['state'];
        
        return GOOGLE_AUTH_URL . '?' . http_build_query($params);
    }
    
    /**
     * Tạo random state để bảo mật
     */
    private function generateState() {
        return bin2hex(random_bytes(16));
    }
    
    /**
     * Verify state để tránh CSRF attack
     */
    public function verifyState($state) {
        if (!isset($_SESSION['oauth_state'])) {
            return false;
        }
        
        $valid = hash_equals($_SESSION['oauth_state'], $state);
        unset($_SESSION['oauth_state']);
        
        return $valid;
    }
    
    /**
     * Exchange authorization code for access token
     */
    public function getAccessToken($code) {
        $params = [
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code'
        ];
        
        $ch = curl_init(GOOGLE_TOKEN_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            return false;
        }
        
        $data = json_decode($response, true);
        return $data['access_token'] ?? false;
    }
    
    /**
     * Lấy thông tin user từ Google
     */
    public function getUserInfo($accessToken) {
        $ch = curl_init(GOOGLE_USERINFO_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            return false;
        }
        
        return json_decode($response, true);
    }
    
    /**
     * Xử lý toàn bộ flow OAuth
     */
    public function handleCallback($code, $state) {
        // Verify state
        if (!$this->verifyState($state)) {
            return [
                'success' => false,
                'error' => 'Invalid state parameter. Possible CSRF attack.'
            ];
        }
        
        // Get access token
        $accessToken = $this->getAccessToken($code);
        if (!$accessToken) {
            return [
                'success' => false,
                'error' => 'Failed to get access token from Google.'
            ];
        }
        
        // Get user info
        $userInfo = $this->getUserInfo($accessToken);
        if (!$userInfo) {
            return [
                'success' => false,
                'error' => 'Failed to get user information from Google.'
            ];
        }
        
        return [
            'success' => true,
            'user' => [
                'google_id' => $userInfo['id'],
                'email' => $userInfo['email'],
                'name' => $userInfo['name'],
                'picture' => $userInfo['picture'] ?? null,
                'verified_email' => $userInfo['verified_email'] ?? false
            ]
        ];
    }
}
