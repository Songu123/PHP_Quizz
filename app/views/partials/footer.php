    </main>
    
    <!-- Footer -->
    <footer class="quiz-footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="footer-brand">
                        <i class="fas fa-brain"></i>
                        <span>QuizMaster</span>
                    </div>
                    <p class="footer-description">
                        Nền tảng thi trắc nghiệm online hàng đầu với hàng ngàn câu hỏi chất lượng cao. 
                        Nâng cao kiến thức và kỹ năng của bạn mỗi ngày!
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="footer-title">Tính năng</h6>
                    <ul class="footer-links">
                        <li><a href="<?php echo URLROOT; ?>/exams">Thi trực tuyến</a></li>
                        <li><a href="<?php echo URLROOT; ?>/subjects">Môn học</a></li>
                        <li><a href="<?php echo URLROOT; ?>/leaderboard">Xếp hạng</a></li>
                        <li><a href="<?php echo URLROOT; ?>/achievements">Thành tựu</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="footer-title">Hỗ trợ</h6>
                    <ul class="footer-links">
                        <li><a href="<?php echo URLROOT; ?>/help">Trung tâm trợ giúp</a></li>
                        <li><a href="<?php echo URLROOT; ?>/faq">FAQ</a></li>
                        <li><a href="<?php echo URLROOT; ?>/contact">Liên hệ</a></li>
                        <li><a href="<?php echo URLROOT; ?>/feedback">Phản hồi</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="footer-title">Chính sách</h6>
                    <ul class="footer-links">
                        <li><a href="<?php echo URLROOT; ?>/privacy">Bảo mật</a></li>
                        <li><a href="<?php echo URLROOT; ?>/terms">Điều khoản</a></li>
                        <li><a href="<?php echo URLROOT; ?>/cookie">Cookie</a></li>
                        <li><a href="<?php echo URLROOT; ?>/security">Bảo mật</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="footer-title">Ứng dụng</h6>
                    <div class="app-downloads">
                        <a href="#" class="app-download">
                            <i class="fab fa-apple"></i>
                            <div>
                                <small>Download on the</small>
                                <strong>App Store</strong>
                            </div>
                        </a>
                        <a href="#" class="app-download">
                            <i class="fab fa-google-play"></i>
                            <div>
                                <small>Get it on</small>
                                <strong>Google Play</strong>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            
            <hr class="footer-divider">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="copyright">
                        &copy; <?php echo date('Y'); ?> QuizMaster. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="footer-stats">
                        <span class="stat-item">
                            <i class="fas fa-users"></i>
                            10,000+ Người dùng
                        </span>
                        <span class="stat-item">
                            <i class="fas fa-question-circle"></i>
                            50,000+ Câu hỏi
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo URLROOT; ?>/js/main.js"></script>
    
    <!-- Particles Background -->
    <div id="particles-js"></div>
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    
    <script>
        // Initialize particles
        if (document.getElementById('particles-js')) {
            particlesJS('particles-js', {
                particles: {
                    number: { value: 50 },
                    color: { value: '#667eea' },
                    shape: { type: 'circle' },
                    opacity: { value: 0.1 },
                    size: { value: 3 },
                    line_linked: {
                        enable: true,
                        distance: 150,
                        color: '#667eea',
                        opacity: 0.1,
                        width: 1
                    },
                    move: {
                        enable: true,
                        speed: 2,
                        direction: 'none',
                        random: false,
                        straight: false,
                        out_mode: 'out',
                        attract: { enable: false }
                    }
                }
            });
        }
        
        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
        
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.quiz-navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>