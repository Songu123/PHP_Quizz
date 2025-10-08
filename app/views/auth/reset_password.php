<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Lại Mật Khẩu - <?php echo SITENAME; ?></title>
    
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
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            padding: 20px;
        }
        
        .reset-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 550px;
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
        
        .reset-header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .reset-header i {
            font-size: 64px;
            margin-bottom: 20px;
            animation: rotate 3s infinite;
        }
        
        @keyframes rotate {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-10deg); }
            75% { transform: rotate(10deg); }
        }
        
        .reset-header h2 {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: 600;
        }
        
        .reset-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }
        
        .reset-body {
            padding: 40px 30px;
        }
        
        .success-badge {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .success-badge i {
            color: #4caf50;
            font-size: 20px;
            margin-right: 10px;
        }
        
        .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-control {
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #11998e;
            box-shadow: 0 0 0 3px rgba(17, 153, 142, 0.1);
        }
        
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        
        .btn-toggle {
            background: transparent;
            border: none;
            border-left: 2px solid #e0e0e0;
            padding: 0 15px;
            cursor: pointer;
            color: #666;
        }
        
        .btn-toggle:hover {
            color: #11998e;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            padding: 14px;
            font-weight: 600;
            font-size: 16px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(17, 153, 142, 0.4);
        }
        
        .text-danger {
            font-size: 13px;
            margin-top: 5px;
        }
        
        .password-strength {
            margin-top: 10px;
        }
        
        .strength-bar {
            height: 5px;
            border-radius: 3px;
            transition: all 0.3s;
        }
        
        .strength-text {
            font-size: 12px;
            margin-top: 5px;
            font-weight: 500;
        }
        
        .password-requirements {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
            font-size: 13px;
        }
        
        .requirement {
            padding: 5px 0;
            color: #666;
        }
        
        .requirement i {
            margin-right: 8px;
            color: #e0e0e0;
        }
        
        .requirement.met i {
            color: #4caf50;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <i class="fas fa-key"></i>
            <h2>Đặt Lại Mật Khẩu</h2>
            <p>Tạo mật khẩu mới cho tài khoản của bạn</p>
        </div>
        
        <div class="reset-body">
            <div class="success-badge">
                <i class="fas fa-check-circle"></i>
                <strong>Xác thực thành công!</strong> Bây giờ bạn có thể đặt mật khẩu mới.
            </div>
            
            <form action="<?php echo URLROOT; ?>/auth/resetpassword" method="POST">
                <!-- New Password -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-lock me-2"></i>Mật Khẩu Mới
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-key"></i>
                        </span>
                        <input 
                            type="password" 
                            name="password" 
                            class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>"
                            id="password"
                            placeholder="Tối thiểu 6 ký tự"
                            required
                        >
                        <button class="btn-toggle" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <?php if(!empty($data['password_err'])): ?>
                        <small class="text-danger">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            <?php echo $data['password_err']; ?>
                        </small>
                    <?php endif; ?>
                    
                    <!-- Password Strength -->
                    <div class="password-strength" id="strengthBar" style="display:none;">
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar" id="strengthProgress" style="width: 0%;"></div>
                        </div>
                        <div class="strength-text" id="strengthText"></div>
                    </div>
                </div>
                
                <!-- Confirm Password -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-lock me-2"></i>Xác Nhận Mật Khẩu
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-check"></i>
                        </span>
                        <input 
                            type="password" 
                            name="confirm_password" 
                            class="form-control <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>"
                            id="confirmPassword"
                            placeholder="Nhập lại mật khẩu"
                            required
                        >
                        <button class="btn-toggle" type="button" id="toggleConfirm">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <?php if(!empty($data['confirm_password_err'])): ?>
                        <small class="text-danger">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            <?php echo $data['confirm_password_err']; ?>
                        </small>
                    <?php endif; ?>
                </div>
                
                <!-- Password Requirements -->
                <div class="password-requirements">
                    <strong><i class="fas fa-shield-alt me-2"></i>Yêu cầu mật khẩu:</strong>
                    <div class="requirement" id="req-length">
                        <i class="fas fa-circle"></i>Tối thiểu 6 ký tự
                    </div>
                    <div class="requirement" id="req-match">
                        <i class="fas fa-circle"></i>Mật khẩu xác nhận khớp
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                        <i class="fas fa-check-circle me-2"></i>Đặt Lại Mật Khẩu
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        document.getElementById('toggleConfirm').addEventListener('click', function() {
            const password = document.getElementById('confirmPassword');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Password strength checker
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirmPassword');
        const strengthBar = document.getElementById('strengthBar');
        const strengthProgress = document.getElementById('strengthProgress');
        const strengthText = document.getElementById('strengthText');
        const reqLength = document.getElementById('req-length');
        const reqMatch = document.getElementById('req-match');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            strengthBar.style.display = 'block';
            
            // Check length
            if (password.length >= 6) {
                reqLength.classList.add('met');
            } else {
                reqLength.classList.remove('met');
            }
            
            // Calculate strength
            let strength = 0;
            if (password.length >= 6) strength += 25;
            if (password.length >= 8) strength += 25;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 25;
            if (/[0-9]/.test(password)) strength += 25;
            
            strengthProgress.style.width = strength + '%';
            
            if (strength <= 25) {
                strengthProgress.className = 'progress-bar bg-danger';
                strengthText.textContent = 'Yếu';
                strengthText.style.color = '#dc3545';
            } else if (strength <= 50) {
                strengthProgress.className = 'progress-bar bg-warning';
                strengthText.textContent = 'Trung bình';
                strengthText.style.color = '#ffc107';
            } else if (strength <= 75) {
                strengthProgress.className = 'progress-bar bg-info';
                strengthText.textContent = 'Tốt';
                strengthText.style.color = '#0dcaf0';
            } else {
                strengthProgress.className = 'progress-bar bg-success';
                strengthText.textContent = 'Mạnh';
                strengthText.style.color = '#198754';
            }
            
            checkMatch();
        });
        
        confirmInput.addEventListener('input', checkMatch);
        
        function checkMatch() {
            if (confirmInput.value && passwordInput.value === confirmInput.value) {
                reqMatch.classList.add('met');
            } else {
                reqMatch.classList.remove('met');
            }
        }
    </script>
</body>
</html>
