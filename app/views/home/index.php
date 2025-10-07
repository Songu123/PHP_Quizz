<?php require_once APP . '/views/partials/header.php'; ?>

<div class="jumbotron bg-gradient text-white p-5 rounded position-relative overflow-hidden">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); opacity: 0.9;"></div>
    <div class="position-relative">
        <?php if (isset($_SESSION['user_id'])): ?>
            <h1 class="display-4">
                <i class="fas fa-home me-3"></i>
                Chào mừng trở lại, <?php echo $_SESSION['user_name']; ?>!
            </h1>
            <p class="lead">Bạn đã đăng nhập thành công vào hệ thống.</p>
            <hr class="my-4">
            <p>Khám phá các tính năng của ứng dụng và quản lý tài khoản của bạn.</p>
            <div class="d-flex gap-3 flex-wrap">
                <a class="btn btn-light btn-lg" href="<?php echo URLROOT; ?>/user/profile" role="button">
                    <i class="fas fa-user me-2"></i>Hồ sơ của tôi
                </a>
                <a class="btn btn-outline-light btn-lg" href="<?php echo URLROOT; ?>/home/about" role="button">
                    <i class="fas fa-info-circle me-2"></i>Tìm hiểu thêm
                </a>
            </div>
        <?php else: ?>
            <h1 class="display-4">
                <i class="fas fa-rocket me-3"></i>
                Chào mừng bạn!
            </h1>
            <p class="lead"><?php echo $message; ?></p>
            <hr class="my-4">
            <p>Đăng ký tài khoản để trải nghiệm đầy đủ các tính năng của ứng dụng.</p>
            <div class="d-flex gap-3 flex-wrap">
                <a class="btn btn-light btn-lg" href="<?php echo URLROOT; ?>/auth/register" role="button">
                    <i class="fas fa-user-plus me-2"></i>Đăng ký ngay
                </a>
                <a class="btn btn-outline-light btn-lg" href="<?php echo URLROOT; ?>/auth/login" role="button">
                    <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-database fa-3x text-primary"></i>
                </div>
                <h5 class="card-title">Model</h5>
                <p class="card-text">Lớp Model xử lý dữ liệu và logic nghiệp vụ, tương tác với database một cách hiệu quả và an toàn.</p>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="#" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>Khám phá
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-eye fa-3x text-success"></i>
                </div>
                <h5 class="card-title">View</h5>
                <p class="card-text">Lớp View hiển thị giao diện người dùng đẹp mắt và trình bày dữ liệu một cách trực quan, dễ hiểu.</p>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="#" class="btn btn-success btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>Khám phá
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-cogs fa-3x text-warning"></i>
                </div>
                <h5 class="card-title">Controller</h5>
                <p class="card-text">Lớp Controller điều khiển luồng xử lý, kết nối Model và View, xử lý logic ứng dụng.</p>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="#" class="btn btn-warning btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>Khám phá
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!isset($_SESSION['user_id'])): ?>
<!-- Section cho người dùng chưa đăng nhập -->
<div class="row mt-5">
    <div class="col-12">
        <div class="card bg-light border-0">
            <div class="card-body text-center py-5">
                <h3 class="mb-4">
                    <i class="fas fa-users text-primary me-2"></i>
                    Tham gia cộng đồng
                </h3>
                <p class="lead mb-4">Đăng ký tài khoản để trải nghiệm đầy đủ tính năng</p>
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-3 fa-2x"></i>
                                    <div class="text-start">
                                        <h6 class="mb-1">Miễn phí hoàn toàn</h6>
                                        <small class="text-muted">Không mất phí đăng ký</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-shield-alt text-success me-3 fa-2x"></i>
                                    <div class="text-start">
                                        <h6 class="mb-1">Bảo mật cao</h6>
                                        <small class="text-muted">Thông tin được mã hóa</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.jumbotron {
    border-radius: 15px;
    border: none;
}

.card {
    transition: all 0.3s ease;
    border-radius: 12px;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
}

.btn {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>

<?php require_once APP . '/views/partials/footer.php'; ?>