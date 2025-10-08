<?php
/**
 * Email Helper
 * Gửi email sử dụng SMTP (Gmail)
 */
class EmailHelper {
    
    private static $smtpHost;
    private static $smtpPort;
    private static $smtpUsername;
    private static $smtpPassword;
    private static $fromEmail;
    private static $fromName;
    
    /**
     * Khởi tạo cấu hình SMTP từ .env
     */
    private static function initConfig() {
        self::$smtpHost = getenv('MAIL_HOST') ?: 'smtp.gmail.com';
        self::$smtpPort = getenv('MAIL_PORT') ?: 587;
        self::$smtpUsername = getenv('MAIL_USERNAME');
        self::$smtpPassword = getenv('MAIL_PASSWORD');
        self::$fromEmail = getenv('MAIL_FROM_EMAIL');
        self::$fromName = getenv('MAIL_FROM_NAME') ?: 'Quiz Website';
    }
    
    /**
     * Gửi email reset password
     */
    public static function sendPasswordResetEmail($toEmail, $toName, $resetCode) {
        self::initConfig();
        
        $subject = "Mã xác thực đặt lại mật khẩu - Quiz Website";
        $message = self::getResetEmailTemplate($toName, $resetCode);
        
        return self::sendSMTPEmail($toEmail, $toName, $subject, $message);
    }
    
    /**
     * Gửi email qua SMTP
     */
    private static function sendSMTPEmail($to, $toName, $subject, $htmlBody) {
        try {
            // Kiểm tra cấu hình
            if (!self::$smtpUsername || !self::$smtpPassword) {
                error_log("Email configuration not found in .env.local");
                return false;
            }
            
            // Kết nối SMTP với TLS
            $socket = @stream_socket_client(
                'tcp://' . self::$smtpHost . ':' . self::$smtpPort,
                $errno,
                $errstr,
                30,
                STREAM_CLIENT_CONNECT,
                stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ])
            );
            
            if (!$socket) {
                error_log("SMTP Connection failed: $errstr ($errno)");
                return false;
            }
            
            // Set timeout
            stream_set_timeout($socket, 30);
            
            // Đọc response ban đầu (220)
            $response = self::readResponse($socket);
            if (!$response) return false;
            
            // EHLO
            fwrite($socket, "EHLO " . self::$smtpHost . "\r\n");
            $response = self::readResponse($socket);
            if (!$response) return false;
            
            // STARTTLS
            fwrite($socket, "STARTTLS\r\n");
            $response = self::readResponse($socket);
            if (!$response || strpos($response, '220') === false) {
                error_log("STARTTLS failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // Enable TLS encryption
            $crypto = stream_socket_enable_crypto(
                $socket,
                true,
                STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT
            );
            
            if (!$crypto) {
                error_log("TLS encryption failed");
                fclose($socket);
                return false;
            }
            
            // EHLO sau STARTTLS
            fwrite($socket, "EHLO " . self::$smtpHost . "\r\n");
            $response = self::readResponse($socket);
            if (!$response) return false;
            
            // AUTH LOGIN
            fwrite($socket, "AUTH LOGIN\r\n");
            $response = self::readResponse($socket);
            if (!$response || strpos($response, '334') === false) {
                error_log("AUTH LOGIN failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // Username
            fwrite($socket, base64_encode(self::$smtpUsername) . "\r\n");
            $response = self::readResponse($socket);
            if (!$response || strpos($response, '334') === false) {
                error_log("Username failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // Password
            fwrite($socket, base64_encode(self::$smtpPassword) . "\r\n");
            $response = self::readResponse($socket);
            if (!$response || strpos($response, '235') === false) {
                error_log("Authentication failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // MAIL FROM
            fwrite($socket, "MAIL FROM: <" . self::$fromEmail . ">\r\n");
            $response = self::readResponse($socket);
            if (!$response) return false;
            
            // RCPT TO
            fwrite($socket, "RCPT TO: <$to>\r\n");
            $response = self::readResponse($socket);
            if (!$response) return false;
            
            // DATA
            fwrite($socket, "DATA\r\n");
            $response = self::readResponse($socket);
            if (!$response || strpos($response, '354') === false) {
                error_log("DATA failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // Headers và Body
            $headers = "From: " . self::$fromName . " <" . self::$fromEmail . ">\r\n";
            $headers .= "To: $toName <$to>\r\n";
            $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "Content-Transfer-Encoding: 8bit\r\n";
            $headers .= "\r\n";
            
            fwrite($socket, $headers . $htmlBody . "\r\n.\r\n");
            $response = self::readResponse($socket);
            if (!$response || strpos($response, '250') === false) {
                error_log("Send failed: " . $response);
                fclose($socket);
                return false;
            }
            
            // QUIT
            fwrite($socket, "QUIT\r\n");
            fclose($socket);
            
            return true;
            
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Đọc response từ SMTP server
     */
    private static function readResponse($socket) {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            // Kiểm tra xem đã đọc hết response chưa (dòng cuối không có dấu -)
            if (isset($line[3]) && $line[3] == ' ') {
                break;
            }
        }
        return $response;
    }
    
    /**
     * Template email đẹp
     */
    private static function getResetEmailTemplate($name, $code) {
        $html = '
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 40px 30px;
        }
        .code-box {
            background: #f8f9fa;
            border: 2px dashed #667eea;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .code {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 8px;
            font-family: "Courier New", monospace;
        }
        .warning {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Đặt lại mật khẩu</h1>
        </div>
        
        <div class="content">
            <p>Xin chào <strong>' . htmlspecialchars($name) . '</strong>,</p>
            
            <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>
            
            <p><strong>Mã xác thực của bạn là:</strong></p>
            
            <div class="code-box">
                <div class="code">' . $code . '</div>
            </div>
            
            <p style="text-align: center; color: #666;">
                Mã này có hiệu lực trong <strong>15 phút</strong>
            </p>
            
            <div class="warning">
                <strong>⚠️ Lưu ý bảo mật:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Không chia sẻ mã này với bất kỳ ai</li>
                    <li>Nếu bạn không yêu cầu đặt lại mật khẩu, hãy bỏ qua email này</li>
                    <li>Mã chỉ sử dụng được một lần</li>
                </ul>
            </div>
            
            <p>Nếu bạn gặp vấn đề, vui lòng liên hệ với chúng tôi.</p>
            
            <p>Trân trọng,<br>
            <strong>Quiz Website Team</strong></p>
        </div>
        
        <div class="footer">
            <p>Email này được gửi tự động, vui lòng không trả lời.</p>
            <p>&copy; 2025 Quiz Website. All rights reserved.</p>
        </div>
    </div>
</body>
</html>';
        
        return $html;
    }
    
    /**
     * Email headers
     */
    private static function getEmailHeaders() {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: Quiz Website <noreply@quizwebsite.com>\r\n";
        $headers .= "Reply-To: support@quizwebsite.com\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        
        return $headers;
    }
    
    /**
     * Gửi email đơn giản
     */
    public static function sendSimpleEmail($to, $subject, $message) {
        $headers = self::getEmailHeaders();
        return mail($to, $subject, $message, $headers);
    }
}
