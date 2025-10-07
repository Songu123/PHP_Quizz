<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' . SITENAME : SITENAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark quiz-navbar fixed-top">
        <div class="container">
            <a class="navbar-brand quiz-brand" href="<?php echo URLROOT; ?>">
                <i class="fas fa-brain quiz-icon"></i>
                <span class="brand-text">QuizMaster</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>">
                            <i class="fas fa-home me-1"></i>Trang Chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/exams">
                            <i class="fas fa-gamepad me-1"></i>Thi Ngay
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/subjects">
                            <i class="fas fa-book me-1"></i>Môn Học
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/leaderboard">
                            <i class="fas fa-trophy me-1"></i>Xếp Hạng
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- User đã đăng nhập -->
                        <li class="nav-item me-3">
                            <div class="user-stats d-flex align-items-center">
                                <span class="badge bg-warning me-2">
                                    <i class="fas fa-star me-1"></i>1250 XP
                                </span>
                                <span class="badge bg-info">
                                    <i class="fas fa-medal me-1"></i>Level 5
                                </span>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle user-dropdown" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <span class="user-name">Xin chào, <?php echo $_SESSION['user_name']; ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/user/profile">
                                    <i class="fas fa-user-edit me-2"></i>Hồ sơ
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/student/dashboard">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/student/history">
                                    <i class="fas fa-history me-2"></i>Lịch sử thi
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/auth/logout">
                                    <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- User chưa đăng nhập -->
                        <li class="nav-item">
                            <a class="nav-link btn-login" href="<?php echo URLROOT; ?>/auth/login">
                                <i class="fas fa-sign-in-alt me-1"></i>Đăng Nhập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-quiz-primary ms-2" href="<?php echo URLROOT; ?>/auth/register">
                                <i class="fas fa-user-plus me-1"></i>Đăng Ký
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="container">
                <div class="alert alert-<?php echo $_SESSION['message']['type']; ?> alert-dismissible fade show quiz-alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <?php echo $_SESSION['message']['text']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>