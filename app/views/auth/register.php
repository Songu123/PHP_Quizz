<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - QuizMaster</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="auth-wrapper register-wrapper">
    <div class="container">
        <div class="row min-vh-100 align-items-center justify-content-center py-5">
            <div class="col-12 col-sm-11 col-md-9 col-lg-7 col-xl-6 col-xxl-5">
                
                <!-- Register Card -->
                <div class="register-card">
                    <!-- Logo & Title -->
                    <div class="text-center mb-4">
                        <div class="logo-wrapper mb-3">
                            <div class="logo-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                        </div>
                        <h2 class="title">Tạo tài khoản mới</h2>
                        <p class="subtitle">Bắt đầu hành trình học tập của bạn ngay hôm nay</p>
                    </div>

                    <!-- Alert Messages -->
                    <?php if(!empty($data['name_err']) || !empty($data['email_err']) || !empty($data['password_err']) || !empty($data['confirm_password_err'])): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <div>Vui lòng kiểm tra lại thông tin đăng ký</div>
                    </div>
                    <?php endif; ?>

                    <!-- Register Form -->
                    <form action="<?php echo URLROOT; ?>/auth/register" method="POST" id="registerForm">
                        
                        <!-- Name Input -->
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-user me-2"></i>Họ và Tên</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                <input 
                                    type="text" 
                                    name="name" 
                                    class="form-control <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>"
                                    id="name"
                                    value="<?php echo $data['name']; ?>"
                                    placeholder="Nguyễn Văn A"
                                    required
                                >
                            </div>
                            <?php if(!empty($data['name_err'])): ?>
                            <small class="text-danger"><?php echo $data['name_err']; ?></small>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Email Input -->
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-envelope me-2"></i>Địa chỉ Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-at"></i></span>
                                <input 
                                    type="email" 
                                    name="email" 
                                    class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>"
                                    id="email"
                                    value="<?php echo $data['email']; ?>"
                                    placeholder="example@email.com"
                                    required
                                >
                            </div>
                            <?php if(!empty($data['email_err'])): ?>
                            <small class="text-danger"><?php echo $data['email_err']; ?></small>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Password Input -->
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-lock me-2"></i>Mật khẩu</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input 
                                    type="password" 
                                    name="password" 
                                    class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>"
                                    id="password"
                                    placeholder="Tối thiểu 6 ký tự"
                                    required
                                >
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <?php if(!empty($data['password_err'])): ?>
                            <small class="text-danger"><?php echo $data['password_err']; ?></small>
                            <?php endif; ?>
                            <!-- Password Strength -->
                            <div class="password-strength mt-2">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar" id="strengthBar" role="progressbar"></div>
                                </div>
                                <small class="text-muted" id="strengthText">Độ mạnh mật khẩu</small>
                            </div>
                        </div>
                        
                        <!-- Confirm Password Input -->
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-check-circle me-2"></i>Xác nhận Mật khẩu</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-shield-alt"></i></span>
                                <input 
                                    type="password" 
                                    name="confirm_password" 
                                    class="form-control <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>"
                                    id="confirm_password"
                                    placeholder="Nhập lại mật khẩu"
                                    required
                                >
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <?php if(!empty($data['confirm_password_err'])): ?>
                            <small class="text-danger"><?php echo $data['confirm_password_err']; ?></small>
                            <?php endif; ?>
                        </div>

                        <!-- Terms & Conditions -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="acceptTerms" required>
                            <label class="form-check-label" for="acceptTerms">
                                Tôi đồng ý với <a href="#">Điều khoản sử dụng</a> và <a href="#">Chính sách bảo mật</a>
                            </label>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-success btn-lg" id="registerBtn">
                                <i class="fas fa-user-plus me-2"></i>Tạo Tài Khoản
                            </button>
                        </div>

                        <!-- Divider -->
                        <div class="divider">
                            <span>Hoặc đăng ký với</span>
                        </div>

                        <!-- Social Login -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-danger w-100">
                                    <i class="fab fa-google me-1"></i> Google
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-primary w-100">
                                    <i class="fab fa-facebook-f me-1"></i> Facebook
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Login Link -->
                    <div class="text-center mt-3 pt-3 border-top">
                        <p class="mb-0">
                            Đã có tài khoản? 
                            <a href="<?php echo URLROOT; ?>/auth/login" class="fw-bold">Đăng nhập ngay</a>
                        </p>
                    </div>
                </div>
                
                <!-- Security Info -->
                <div class="text-center mt-3">
                    <div class="security-badges">
                        <span class="badge bg-light text-dark me-2">
                            <i class="fas fa-shield-alt text-success me-1"></i>Bảo mật SSL
                        </span>
                        <span class="badge bg-light text-dark me-2">
                            <i class="fas fa-lock text-success me-1"></i>Mã hóa dữ liệu
                        </span>
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-check-circle text-success me-1"></i>An toàn 100%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    min-height: 100vh;
}

.auth-wrapper {
    min-height: 100vh;
    position: relative;
}

.auth-wrapper::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
    background-size: cover;
    opacity: 0.3;
}

.register-card {
    background: white;
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    position: relative;
    z-index: 1;
}

.logo-wrapper {
    display: flex;
    justify-content: center;
}

.logo-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(17, 153, 142, 0.4);
    animation: bounce 2s infinite;
}

.logo-icon i {
    font-size: 2.5rem;
    color: white;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.subtitle {
    color: #6c757d;
    font-size: 0.95rem;
}

.form-label {
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
}

.input-group-text {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border: none;
    color: white;
}

.form-control {
    border: 2px solid #e9ecef;
    padding: 0.7rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s;
}

.form-control:focus {
    border-color: #11998e;
    box-shadow: 0 0 0 0.2rem rgba(17, 153, 142, 0.15);
}

.btn-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border: none;
    padding: 0.8rem;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(17, 153, 142, 0.4);
}

.divider {
    display: flex;
    align-items: center;
    text-align: center;
    margin: 1.5rem 0;
}

.divider::before,
.divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #dee2e6;
}

.divider span {
    padding: 0 1rem;
    color: #6c757d;
    font-size: 0.85rem;
}

.btn-outline-danger:hover {
    background: #dc3545;
    color: white;
}

.btn-outline-primary:hover {
    background: #0d6efd;
    color: white;
}

.security-badges {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.password-strength {
    margin-top: 0.5rem;
}

.progress-bar {
    transition: all 0.3s;
}

.alert {
    border-radius: 12px;
    border: none;
}

@media (max-width: 576px) {
    .register-card {
        padding: 2rem 1.5rem;
    }
    
    .title {
        font-size: 1.5rem;
    }
    
    .logo-icon {
        width: 70px;
        height: 70px;
    }
    
    .logo-icon i {
        font-size: 2rem;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
    
    if (toggleConfirmPassword) {
        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPasswordInput.type === 'password' ? 'text' : 'password';
            confirmPasswordInput.type = type;
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
    
    // Password strength checker
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    if (passwordInput && strengthBar) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 6) strength += 25;
            if (password.length >= 8) strength += 15;
            if (/[a-z]/.test(password)) strength += 15;
            if (/[A-Z]/.test(password)) strength += 15;
            if (/[0-9]/.test(password)) strength += 15;
            if (/[^a-zA-Z0-9]/.test(password)) strength += 15;
            
            strengthBar.style.width = strength + '%';
            strengthBar.classList.remove('bg-danger', 'bg-warning', 'bg-success');
            
            if (strength < 40) {
                strengthBar.classList.add('bg-danger');
                strengthText.textContent = 'Mật khẩu yếu';
            } else if (strength < 70) {
                strengthBar.classList.add('bg-warning');
                strengthText.textContent = 'Mật khẩu trung bình';
            } else {
                strengthBar.classList.add('bg-success');
                strengthText.textContent = 'Mật khẩu mạnh';
            }
        });
    }
    
    // Password match validation
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            if (this.value && this.value !== passwordInput.value) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }
});
</script>

</body>
</html>