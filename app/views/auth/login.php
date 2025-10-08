<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - QuizMaster</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="auth-wrapper">
    <div class="container">
        <div class="row min-vh-100 align-items-center justify-content-center py-5">
            <div class="col-12 col-sm-11 col-md-9 col-lg-7 col-xl-6 col-xxl-5">
                
                <!-- Login Card -->
                <div class="login-card">
                    <!-- Logo & Title -->
                    <div class="text-center mb-4">
                        <div class="logo-wrapper mb-3">
                            <div class="logo-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                        </div>
                        <h2 class="title">Chào mừng trở lại!</h2>
                        <p class="subtitle">Đăng nhập để tiếp tục hành trình học tập</p>
                    </div>

                    <!-- Alert Messages -->
                    <?php if(!empty($data['email_err']) || !empty($data['password_err'])): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <div>Vui lòng kiểm tra lại thông tin đăng nhập</div>
                    </div>
                    <?php endif; ?>

                    <!-- Login Form -->
                    <form action="<?php echo URLROOT; ?>/auth/login" method="POST" id="loginForm">
                        
                        <!-- Email Input -->
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-envelope me-2"></i>Địa chỉ Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
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
                                    placeholder="••••••••"
                                    required
                                >
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <?php if(!empty($data['password_err'])): ?>
                            <small class="text-danger"><?php echo $data['password_err']; ?></small>
                            <?php endif; ?>
                        </div>

                        <!-- Remember & Forgot -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">Ghi nhớ đăng nhập</label>
                            </div>
                            <a href="#" class="link-primary">Quên mật khẩu?</a>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg" id="loginBtn">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng Nhập
                            </button>
                        </div>

                        <!-- Divider -->
                        <div class="divider">
                            <span>Hoặc đăng nhập với</span>
                        </div>

                        <!-- Social Login -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <a href="<?php echo URLROOT; ?>/auth/googlelogin" class="btn btn-outline-danger w-100">
                                    <i class="fab fa-google me-1"></i> Google
                                </a>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-primary w-100" disabled>
                                    <i class="fab fa-facebook-f me-1"></i> Facebook
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Register Link -->
                    <div class="text-center mt-3 pt-3 border-top">
                        <p class="mb-0">
                            Chưa có tài khoản? 
                            <a href="<?php echo URLROOT; ?>/auth/register" class="fw-bold">Đăng ký ngay</a>
                        </p>
                    </div>
                </div>
                
                <!-- Demo Info -->
                <div class="text-center mt-3">
                    <div class="demo-badge">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Demo:</strong> admin@example.com / password123
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

.login-card {
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    padding: 0.8rem;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
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

.demo-badge {
    background: rgba(255, 255, 255, 0.95);
    padding: 1rem 1.5rem;
    border-radius: 12px;
    display: inline-block;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    color: #6c757d;
    font-size: 0.9rem;
}

.demo-badge i {
    color: #667eea;
}

.alert {
    border-radius: 12px;
    border: none;
}

@media (max-width: 576px) {
    .login-card {
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
    const passwordInput = document.getElementById('password');
    
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
    
    // Remember me functionality
    const rememberCheckbox = document.getElementById('rememberMe');
    const emailInput = document.getElementById('email');
    
    // Load saved email
    const savedEmail = localStorage.getItem('rememberedEmail');
    if (savedEmail) {
        emailInput.value = savedEmail;
        rememberCheckbox.checked = true;
    }
    
    // Save email on form submit
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function() {
            if (rememberCheckbox.checked) {
                localStorage.setItem('rememberedEmail', emailInput.value);
            } else {
                localStorage.removeItem('rememberedEmail');
            }
        });
    }
});
</script>

</body>
</html>