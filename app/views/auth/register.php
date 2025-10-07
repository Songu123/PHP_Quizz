<?php require_once APP . '/views/partials/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-success text-white text-center">
                <h4 class="mb-0">
                    <i class="fas fa-user-plus me-2"></i>Đăng Ký
                </h4>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo URLROOT; ?>/auth/register" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="fas fa-user me-1"></i>Họ và tên
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>"
                            id="name"
                            value="<?php echo $name; ?>"
                            placeholder="Nhập họ và tên"
                            required
                        >
                        <div class="invalid-feedback">
                            <?php echo $name_err; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-1"></i>Email
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"
                            id="email"
                            value="<?php echo $email; ?>"
                            placeholder="Nhập email của bạn"
                            required
                        >
                        <div class="invalid-feedback">
                            <?php echo $email_err; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-1"></i>Mật khẩu
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>"
                            id="password"
                            value="<?php echo $password; ?>"
                            placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)"
                            required
                        >
                        <div class="invalid-feedback">
                            <?php echo $password_err; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">
                            <i class="fas fa-lock me-1"></i>Xác nhận mật khẩu
                        </label>
                        <input 
                            type="password" 
                            name="confirm_password" 
                            class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>"
                            id="confirm_password"
                            value="<?php echo $confirm_password; ?>"
                            placeholder="Nhập lại mật khẩu"
                            required
                        >
                        <div class="invalid-feedback">
                            <?php echo $confirm_password_err; ?>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Đăng Ký
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <p class="mb-0">Đã có tài khoản? 
                        <a href="<?php echo URLROOT; ?>/auth/login" class="text-success text-decoration-none">
                            <strong>Đăng nhập</strong>
                        </a>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Thông tin bảo mật -->
        <div class="card mt-3 bg-light">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="fas fa-shield-alt text-success me-1"></i>
                    Bảo mật thông tin
                </h6>
                <small class="text-muted">
                    • Mật khẩu được mã hóa bảo mật<br>
                    • Thông tin cá nhân được bảo vệ<br>
                    • Email xác thực sẽ được gửi
                </small>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    border-bottom: none;
    padding: 1.5rem;
}

.form-control {
    border-radius: 10px;
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    transform: translateY(-1px);
}

.btn {
    border-radius: 10px;
    padding: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.form-label {
    font-weight: 600;
    color: #495057;
}

.card.bg-light {
    border: 1px solid #dee2e6;
}

.card.bg-light .card-title {
    margin-bottom: 0.5rem;
}
</style>

<?php require_once APP . '/views/partials/footer.php'; ?>