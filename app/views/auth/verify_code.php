<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Thực Mã - <?php echo SITENAME; ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        
        .verify-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
            animation: slideUp 0.5s ease;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .verify-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .verify-header i {
            font-size: 64px;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .verify-header h2 {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: 600;
        }
        
        .verify-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }
        
        .verify-body {
            padding: 40px 30px;
        }
        
        .email-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 25px;
        }
        
        .email-info i {
            color: #667eea;
            font-size: 20px;
            margin-right: 10px;
        }
        
        .email-info strong {
            color: #667eea;
        }
        
        .code-input {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 15px;
            padding: 20px;
            border: 3px solid #e0e0e0;
            border-radius: 15px;
            font-family: 'Courier New', monospace;
            transition: all 0.3s;
        }
        
        .code-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 5px rgba(102, 126, 234, 0.1);
        }
        
        .timer-box {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        
        .timer-box i {
            color: #ff9800;
            font-size: 20px;
            margin-right: 10px;
        }
        
        .timer {
            font-size: 24px;
            font-weight: bold;
            color: #ff9800;
            font-family: 'Courier New', monospace;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 14px;
            font-weight: 600;
            font-size: 16px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-outline-secondary {
            border: 2px solid #e0e0e0;
            color: #666;
            font-weight: 500;
            padding: 14px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .btn-outline-secondary:hover {
            border-color: #667eea;
            color: #667eea;
            background: transparent;
        }
        
        .text-danger {
            font-size: 13px;
            margin-top: 10px;
            text-align: center;
        }
        
        .resend-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        
        .resend-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .resend-link a:hover {
            color: #764ba2;
        }
        
        /* Success message */
        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="verify-header">
            <i class="fas fa-shield-alt"></i>
            <h2>Xác Thực Mã</h2>
            <p>Nhập mã 6 chữ số đã được gửi đến email</p>
        </div>
        
        <div class="verify-body">
            <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message']['type']; ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php 
                        echo $_SESSION['message']['text']; 
                        unset($_SESSION['message']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="email-info">
                <i class="fas fa-envelope"></i>
                Mã đã được gửi đến: <strong><?php echo $data['email']; ?></strong>
            </div>
            
            <form action="<?php echo URLROOT; ?>/auth/verifycode" method="POST">
                <!-- Code Input -->
                <div class="mb-3">
                    <input 
                        type="text" 
                        name="code" 
                        class="form-control code-input <?php echo (!empty($data['code_err'])) ? 'is-invalid' : ''; ?>"
                        maxlength="6"
                        pattern="[0-9]{6}"
                        placeholder="000000"
                        required
                        autofocus
                        id="codeInput"
                    >
                    <?php if(!empty($data['code_err'])): ?>
                        <div class="text-danger">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            <?php echo $data['code_err']; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Timer -->
                <div class="timer-box">
                    <i class="fas fa-clock"></i>
                    Mã có hiệu lực trong: <span class="timer" id="timer">15:00</span>
                </div>
                
                <!-- Submit Button -->
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-check me-2"></i>Xác Nhận Mã
                    </button>
                </div>
                
                <!-- Resend Link -->
                <div class="resend-link">
                    <p class="mb-2">Không nhận được mã?</p>
                    <a href="<?php echo URLROOT; ?>/auth/resendcode">
                        <i class="fas fa-redo me-2"></i>Gửi lại mã
                    </a>
                    <span class="mx-2">|</span>
                    <a href="<?php echo URLROOT; ?>/auth/forgotpassword">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto format code input (chỉ cho phép số)
        document.getElementById('codeInput').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        // Countdown timer (15 phút)
        let timeLeft = 15 * 60; // 15 minutes in seconds
        const timerElement = document.getElementById('timer');
        
        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                timerElement.textContent = 'Hết hạn!';
                timerElement.style.color = '#f44336';
                clearInterval(timerInterval);
            }
            
            timeLeft--;
        }
        
        const timerInterval = setInterval(updateTimer, 1000);
        updateTimer(); // Run immediately
    </script>
</body>
</html>
