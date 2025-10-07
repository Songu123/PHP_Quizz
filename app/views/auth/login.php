<?php require_once APP . '/views/partials/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white text-center">
                <h4 class="mb-0">
                    <i class="fas fa-sign-in-alt me-2"></i>Đăng Nhập
                </h4>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo URLROOT; ?>/auth/login" method="POST">
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
                            placeholder="Nhập mật khẩu"
                            required
                        >
                        <div class="invalid-feedback">
                            <?php echo $password_err; ?>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Đăng Nhập
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <p class="mb-0">Chưa có tài khoản? 
                        <a href="<?php echo URLROOT; ?>/auth/register" class="text-primary text-decoration-none">
                            <strong>Đăng ký ngay</strong>
                        </a>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Demo account info -->
        <div class="card mt-3 bg-light">
            <div class="card-body text-center">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Demo: admin@example.com / password123
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
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
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
</style>

<?php require_once APP . '/views/partials/footer.php'; ?>