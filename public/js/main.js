// QuizMaster - Modern Quiz Game JavaScript

document.addEventListener('DOMContentLoaded', function() {
    
    // =============== NAVBAR SCROLL EFFECT ===============
    function initNavbarScrollEffect() {
        const navbar = document.querySelector('.quiz-navbar');
        if (navbar) {
            let scrollTimer = null;
            
            window.addEventListener('scroll', function() {
                if (scrollTimer) {
                    clearTimeout(scrollTimer);
                }
                
                scrollTimer = setTimeout(function() {
                    if (window.scrollY > 100) {
                        navbar.classList.add('scrolled');
                    } else {
                        navbar.classList.remove('scrolled');
                    }
                }, 10);
            });
        }
    }

    // =============== STATS COUNTER ANIMATION ===============
    function initStatsCounter() {
        const counters = document.querySelectorAll('.stat-number');
        
        function animateCounter(counter) {
            const target = parseInt(counter.getAttribute('data-count') || counter.textContent.replace(/[^\d]/g, ''));
            const duration = 2000; // 2 seconds
            const increment = target / (duration / 16); // 60fps
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                
                // Format number with commas
                const formatted = Math.floor(current).toLocaleString();
                counter.textContent = formatted + (counter.textContent.includes('+') ? '+' : '');
            }, 16);
        }
        
        // Intersection Observer for counter animation
        const observerOptions = {
            threshold: 0.5,
            rootMargin: '0px 0px -100px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                    entry.target.classList.add('animated');
                    animateCounter(entry.target);
                }
            });
        }, observerOptions);
        
        counters.forEach(counter => {
            observer.observe(counter);
        });
    }

    // =============== PARTICLES.JS CONFIGURATION ===============
    function initParticles() {
        if (typeof particlesJS !== 'undefined') {
            particlesJS('particles-js', {
                "particles": {
                    "number": {
                        "value": 80,
                        "density": {
                            "enable": true,
                            "value_area": 800
                        }
                    },
                    "color": {
                        "value": "#667eea"
                    },
                    "shape": {
                        "type": "circle",
                        "stroke": {
                            "width": 0,
                            "color": "#000000"
                        }
                    },
                    "opacity": {
                        "value": 0.5,
                        "random": false,
                        "anim": {
                            "enable": false,
                            "speed": 1,
                            "opacity_min": 0.1,
                            "sync": false
                        }
                    },
                    "size": {
                        "value": 3,
                        "random": true,
                        "anim": {
                            "enable": false,
                            "speed": 40,
                            "size_min": 0.1,
                            "sync": false
                        }
                    },
                    "line_linked": {
                        "enable": true,
                        "distance": 150,
                        "color": "#667eea",
                        "opacity": 0.4,
                        "width": 1
                    },
                    "move": {
                        "enable": true,
                        "speed": 6,
                        "direction": "none",
                        "random": false,
                        "straight": false,
                        "out_mode": "out",
                        "bounce": false,
                        "attract": {
                            "enable": false,
                            "rotateX": 600,
                            "rotateY": 1200
                        }
                    }
                },
                "interactivity": {
                    "detect_on": "canvas",
                    "events": {
                        "onhover": {
                            "enable": true,
                            "mode": "repulse"
                        },
                        "onclick": {
                            "enable": true,
                            "mode": "push"
                        },
                        "resize": true
                    },
                    "modes": {
                        "grab": {
                            "distance": 400,
                            "line_linked": {
                                "opacity": 1
                            }
                        },
                        "bubble": {
                            "distance": 400,
                            "size": 40,
                            "duration": 2,
                            "opacity": 8,
                            "speed": 3
                        },
                        "repulse": {
                            "distance": 200,
                            "duration": 0.4
                        },
                        "push": {
                            "particles_nb": 4
                        },
                        "remove": {
                            "particles_nb": 2
                        }
                    }
                },
                "retina_detect": true
            });
        }
    }

    // =============== SMOOTH SCROLLING ===============
    function initSmoothScrolling() {
        const links = document.querySelectorAll('a[href^="#"]');
        
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    e.preventDefault();
                    
                    const headerOffset = 80; // Account for fixed navbar
                    const elementPosition = targetElement.offsetTop;
                    const offsetPosition = elementPosition - headerOffset;
                    
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    // =============== FADE IN ANIMATION ===============
    function initFadeInAnimation() {
        const elements = document.querySelectorAll('.fade-in-up, .card, .feature-card, .subject-card');
        
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    entry.target.classList.add('animated');
                }
            });
        }, observerOptions);
        
        elements.forEach((element, index) => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            element.style.transition = `all 0.8s ease-out ${index * 0.1}s`;
            observer.observe(element);
        });
    }

    // =============== QUIZ CARD HOVER EFFECTS ===============
    function initQuizCardEffects() {
        const cards = document.querySelectorAll('.quiz-card, .feature-card, .subject-card, .stat-card');
        
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    }

    // =============== LOADING STATES ===============
    function initLoadingStates() {
        const buttons = document.querySelectorAll('.btn');
        
        buttons.forEach(button => {
            if (!button.dataset.originalText) {
                button.dataset.originalText = button.innerHTML;
            }
            
            button.addEventListener('click', function(e) {
                if (this.classList.contains('btn-loading')) return;
                
                // Add loading state for form submissions
                if (this.type === 'submit' || this.closest('form')) {
                    this.classList.add('btn-loading');
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
                    this.disabled = true;
                    
                    // Remove loading state after 3 seconds (adjust as needed)
                    setTimeout(() => {
                        this.classList.remove('btn-loading');
                        this.innerHTML = this.dataset.originalText;
                        this.disabled = false;
                    }, 3000);
                }
            });
        });
    }

    // =============== NOTIFICATION SYSTEM ===============
    function createNotification(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show notification`;
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            border: none;
        `;
        
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'}"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after duration
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, duration);
        
        return notification;
    }

    // =============== FORM VALIDATION ENHANCEMENTS ===============
    function initFormValidation() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            const inputs = form.querySelectorAll('input, textarea, select');
            
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(this);
                });
                
                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        validateField(this);
                    }
                });
            });
            
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                inputs.forEach(input => {
                    if (!validateField(input)) {
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    createNotification('Vui lòng kiểm tra lại thông tin trong form', 'danger');
                }
            });
        });
    }

    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let message = '';
        
        // Required validation
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            message = 'Trường này là bắt buộc';
        }
        
        // Email validation
        if (field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                message = 'Vui lòng nhập email hợp lệ';
            }
        }
        
        // Password validation
        if (field.type === 'password' && value) {
            if (value.length < 6) {
                isValid = false;
                message = 'Mật khẩu phải có ít nhất 6 ký tự';
            }
        }
        
        // Update field appearance
        field.classList.toggle('is-invalid', !isValid);
        field.classList.toggle('is-valid', isValid && value);
        
        // Update or remove feedback message
        let feedback = field.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.remove();
        }
        
        if (!isValid) {
            const feedbackDiv = document.createElement('div');
            feedbackDiv.className = 'invalid-feedback';
            feedbackDiv.textContent = message;
            field.parentNode.insertBefore(feedbackDiv, field.nextSibling);
        }
        
        return isValid;
    }

    // =============== PASSWORD STRENGTH INDICATOR ===============
    function initPasswordStrength() {
        const passwordInputs = document.querySelectorAll('input[type="password"][name="password"]');
        
        passwordInputs.forEach(function(input) {
            input.addEventListener('input', function() {
                const password = this.value;
                let strengthBar = this.parentNode.querySelector('.password-strength');
                
                if (!strengthBar && password.length > 0) {
                    const strengthHTML = `
                        <div class="password-strength mt-2">
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small class="strength-text text-muted"></small>
                        </div>
                    `;
                    this.parentNode.insertAdjacentHTML('beforeend', strengthHTML);
                    strengthBar = this.parentNode.querySelector('.password-strength');
                }
                
                if (strengthBar) {
                    const strength = calculatePasswordStrength(password);
                    const strengthBarElement = strengthBar.querySelector('.progress-bar');
                    const strengthText = strengthBar.querySelector('.strength-text');
                    
                    if (password.length > 0) {
                        strengthBarElement.style.width = strength.percentage + '%';
                        strengthBarElement.className = `progress-bar bg-${strength.color}`;
                        strengthText.textContent = strength.text;
                        strengthText.className = `strength-text text-${strength.color}`;
                    } else {
                        strengthBar.remove();
                    }
                }
            });
        });
    }

    // Password strength calculator
    function calculatePasswordStrength(password) {
        let score = 0;
        let text = '';
        let color = 'danger';
        
        if (password.length >= 6) score += 1;
        if (password.length >= 8) score += 1;
        if (/[A-Z]/.test(password)) score += 1;
        if (/[a-z]/.test(password)) score += 1;
        if (/[0-9]/.test(password)) score += 1;
        if (/[^A-Za-z0-9]/.test(password)) score += 1;
        
        switch (score) {
            case 0:
            case 1:
            case 2:
                text = 'Yếu';
                color = 'danger';
                break;
            case 3:
            case 4:
                text = 'Trung bình';
                color = 'warning';
                break;
            case 5:
            case 6:
                text = 'Mạnh';
                color = 'success';
                break;
        }
        
        return {
            percentage: (score / 6) * 100,
            text: text,
            color: color
        };
    }

    // =============== PASSWORD TOGGLE ===============
    function initPasswordToggle() {
        const passwordToggles = document.querySelectorAll('[data-toggle-password]');
        
        passwordToggles.forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const targetId = this.getAttribute('data-toggle-password');
                const target = document.getElementById(targetId);
                
                if (target) {
                    if (target.type === 'password') {
                        target.type = 'text';
                        this.innerHTML = '<i class="fas fa-eye-slash"></i>';
                    } else {
                        target.type = 'password';
                        this.innerHTML = '<i class="fas fa-eye"></i>';
                    }
                }
            });
        });
    }

    // =============== AUTO DISMISS ALERTS ===============
    function initAlertDismissal() {
        const alerts = document.querySelectorAll('.alert:not(.notification)');
        
        alerts.forEach(function(alert) {
            setTimeout(function() {
                if (alert.classList.contains('alert-dismissible')) {
                    const bsAlert = new bootstrap.Alert(alert);
                    if (bsAlert) {
                        bsAlert.close();
                    }
                }
            }, 5000);
        });
    }

    // =============== CONFIRM DELETE ACTIONS ===============
    function initConfirmActions() {
        const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
        
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                if (!confirm('Bạn có chắc chắn muốn xóa?')) {
                    e.preventDefault();
                }
            });
        });
    }

    // =============== RESPONSIVE UTILITIES ===============
    function initResponsiveUtilities() {
        function handleResize() {
            const windowWidth = window.innerWidth;
            
            // Adjust hero cards on mobile
            const heroCards = document.querySelectorAll('.quiz-card');
            if (windowWidth <= 768) {
                heroCards.forEach(card => {
                    card.style.position = 'relative';
                    card.style.display = 'none';
                });
            } else {
                heroCards.forEach(card => {
                    card.style.position = 'absolute';
                    card.style.display = 'block';
                });
            }
        }
        
        window.addEventListener('resize', handleResize);
        handleResize(); // Initial call
    }

    // =============== AUTO-SAVE FUNCTIONALITY ===============
    function initAutoSave() {
        const forms = document.querySelectorAll('form[data-autosave]');
        
        forms.forEach(form => {
            const inputs = form.querySelectorAll('input, textarea, select');
            
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    const formData = new FormData(form);
                    const data = Object.fromEntries(formData);
                    
                    localStorage.setItem(`autosave_${form.id}`, JSON.stringify(data));
                });
            });
            
            // Load saved data
            const savedData = localStorage.getItem(`autosave_${form.id}`);
            if (savedData) {
                const data = JSON.parse(savedData);
                Object.keys(data).forEach(key => {
                    const field = form.querySelector(`[name="${key}"]`);
                    if (field) {
                        field.value = data[key];
                    }
                });
            }
            
            // Clear autosave on submit
            form.addEventListener('submit', function() {
                localStorage.removeItem(`autosave_${form.id}`);
            });
        });
    }

    // =============== INITIALIZE ALL FUNCTIONS ===============
    initNavbarScrollEffect();
    initStatsCounter();
    initParticles();
    initSmoothScrolling();
    initFadeInAnimation();
    initQuizCardEffects();
    initLoadingStates();
    initFormValidation();
    initPasswordStrength();
    initPasswordToggle();
    initAlertDismissal();
    initConfirmActions();
    initResponsiveUtilities();
    initAutoSave();

    // =============== GLOBAL UTILITIES ===============
    window.QuizMaster = {
        notification: createNotification,
        validateField: validateField,
        showLoading: showLoading,
        hideLoading: hideLoading
    };

    // Show welcome message if user is logged in
    const userWelcome = document.querySelector('.welcome-label');
    if (userWelcome) {
        setTimeout(() => {
            createNotification('Chào mừng đến với QuizMaster! 🎉', 'success');
        }, 1000);
    }

    console.log('🎮 QuizMaster JavaScript đã khởi tạo thành công!');
});

// =============== UTILITY FUNCTIONS ===============
function showLoading() {
    const loadingHTML = `
        <div id="loading-overlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.9); z-index: 9999;">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <div class="mt-2">Đang xử lý...</div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', loadingHTML);
}

function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.remove();
    }
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

function formatDate(date) {
    return new Intl.DateTimeFormat('vi-VN', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }).format(new Date(date));
}

// =============== SERVICE WORKER REGISTRATION (PWA) ===============
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js')
            .then(function(registration) {
                console.log('ServiceWorker registration successful');
            })
            .catch(function(err) {
                console.log('ServiceWorker registration failed');
            });
    });
}