<?php require_once APP . '/views/partials/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6">
                <div class="hero-content">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="welcome-back">
                            <span class="welcome-label">Chào mừng bạn đã trở lại!</span>
                            <h1 class="hero-title">
                                Xin chào, <span class="user-highlight"><?php echo $_SESSION['user_name']; ?></span>
                                <div class="hero-emoji">🎯</div>
                            </h1>
                            <p class="hero-subtitle">Sẵn sàng cho thử thách mới? Hãy bắt đầu với một bài quiz hấp dẫn!</p>
                            
                            <div class="hero-actions">
                                <a href="<?php echo URLROOT; ?>/exams" class="btn btn-quiz-primary btn-lg">
                                    <i class="fas fa-play me-2"></i>Thi Ngay
                                </a>
                                <a href="<?php echo URLROOT; ?>/student/dashboard" class="btn btn-quiz-outline btn-lg">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="welcome-new">
                            <span class="welcome-label">Chào mừng đến với</span>
                            <h1 class="hero-title">
                                QuizMaster
                                <div class="hero-emoji">🧠</div>
                            </h1>
                            <p class="hero-subtitle">
                                Nền tảng thi trắc nghiệm online hàng đầu. Thử thách bản thân với hàng nghìn câu hỏi từ dễ đến khó!
                            </p>
                            
                            <div class="hero-actions">
                                <a href="<?php echo URLROOT; ?>/auth/register" class="btn btn-quiz-primary btn-lg">
                                    <i class="fas fa-rocket me-2"></i>Bắt Đầu Ngay
                                </a>
                                <a href="<?php echo URLROOT; ?>/auth/login" class="btn btn-quiz-outline btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Đăng Nhập
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="hero-visual">
                    <div class="quiz-cards-demo">
                        <div class="quiz-card floating">
                            <div class="card-icon math">📊</div>
                            <h6>Toán học</h6>
                            <span class="questions-count">1,250 câu hỏi</span>
                        </div>
                        <div class="quiz-card floating delay-1">
                            <div class="card-icon science">🔬</div>
                            <h6>Khoa học</h6>
                            <span class="questions-count">980 câu hỏi</span>
                        </div>
                        <div class="quiz-card floating delay-2">
                            <div class="card-icon history">📚</div>
                            <h6>Lịch sử</h6>
                            <span class="questions-count">756 câu hỏi</span>
                        </div>
                        <div class="quiz-card floating delay-3">
                            <div class="card-icon language">🌍</div>
                            <h6>Ngôn ngữ</h6>
                            <span class="questions-count">1,890 câu hỏi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number" data-count="10000">0</div>
                    <div class="stat-label">Người dùng</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div class="stat-number" data-count="50000">0</div>
                    <div class="stat-label">Câu hỏi</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stat-number" data-count="5000">0</div>
                    <div class="stat-label">Đề thi</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-number" data-count="125000">0</div>
                    <div class="stat-label">Lượt thi</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title">Tại sao chọn QuizMaster?</h2>
            <p class="section-subtitle">Khám phá những tính năng tuyệt vời giúp bạn học tập hiệu quả</p>
        </div>
        
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon bg-primary">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h5 class="feature-title">Thi trực tuyến</h5>
                    <p class="feature-description">
                        Làm bài thi mọi lúc, mọi nơi với giao diện thân thiện và hệ thống chấm điểm tự động.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon bg-success">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5 class="feature-title">Theo dõi tiến độ</h5>
                    <p class="feature-description">
                        Xem báo cáo chi tiết về kết quả học tập và theo dõi sự tiến bộ của bản thân.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon bg-warning">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h5 class="feature-title">Hệ thống xếp hạng</h5>
                    <p class="feature-description">
                        Cạnh tranh với bạn bè và cộng đồng thông qua bảng xếp hạng và hệ thống thành tích.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon bg-info">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h5 class="feature-title">AI thông minh</h5>
                    <p class="feature-description">
                        Hệ thống AI đề xuất câu hỏi phù hợp với trình độ và sở thích của bạn.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon bg-danger">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h5 class="feature-title">Đa nền tảng</h5>
                    <p class="feature-description">
                        Truy cập từ máy tính, tablet hay điện thoại với trải nghiệm tối ưu trên mọi thiết bị.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon bg-purple">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5 class="feature-title">Bảo mật cao</h5>
                    <p class="feature-description">
                        Thông tin cá nhân và kết quả học tập được bảo vệ với công nghệ mã hóa tiên tiến.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!isset($_SESSION['user_id'])): ?>
<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-card">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h3 class="cta-title">Sẵn sàng bắt đầu hành trình học tập?</h3>
                    <p class="cta-description">
                        Tham gia cùng hàng nghìn người dùng khác và khám phá tiềm năng của bạn ngay hôm nay!
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="<?php echo URLROOT; ?>/auth/register" class="btn btn-quiz-primary btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Đăng ký miễn phí
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Popular Subjects Section -->
<section class="subjects-section">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title">Môn học phổ biến</h2>
            <p class="section-subtitle">Khám phá các môn học được yêu thích nhất</p>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="subject-card">
                    <div class="subject-image">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="subject-content">
                        <h6 class="subject-name">Toán học</h6>
                        <p class="subject-description">Từ cơ bản đến nâng cao</p>
                        <div class="subject-stats">
                            <span><i class="fas fa-question-circle"></i> 1,250 câu</span>
                            <span><i class="fas fa-users"></i> 2,341 người</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="subject-card">
                    <div class="subject-image">
                        <i class="fas fa-flask"></i>
                    </div>
                    <div class="subject-content">
                        <h6 class="subject-name">Hóa học</h6>
                        <p class="subject-description">Khám phá thế giới phân tử</p>
                        <div class="subject-stats">
                            <span><i class="fas fa-question-circle"></i> 980 câu</span>
                            <span><i class="fas fa-users"></i> 1,876 người</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="subject-card">
                    <div class="subject-image">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="subject-content">
                        <h6 class="subject-name">Địa lý</h6>
                        <p class="subject-description">Khám phá thế giới</p>
                        <div class="subject-stats">
                            <span><i class="fas fa-question-circle"></i> 756 câu</span>
                            <span><i class="fas fa-users"></i> 1,532 người</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="subject-card">
                    <div class="subject-image">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="subject-content">
                        <h6 class="subject-name">Văn học</h6>
                        <p class="subject-description">Nghệ thuật ngôn từ</p>
                        <div class="subject-stats">
                            <span><i class="fas fa-question-circle"></i> 1,890 câu</span>
                            <span><i class="fas fa-users"></i> 3,241 người</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once APP . '/views/partials/footer.php'; ?>