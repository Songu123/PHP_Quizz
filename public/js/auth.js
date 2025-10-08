/**
 * Authentication Pages JavaScript
 * Handle login and register page interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // =============== TOGGLE PASSWORD VISIBILITY ===============
    const toggleButtons = document.querySelectorAll('.toggle-password');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
    
    // =============== FORM SUBMIT LOADING STATE ===============
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('loginBtn');
            const btnContent = submitBtn.querySelector('.btn-content');
            const btnLoader = submitBtn.querySelector('.btn-loader');
            
            btnContent.classList.add('d-none');
            btnLoader.classList.remove('d-none');
            submitBtn.disabled = true;
        });
    }
    
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('registerBtn');
            const btnContent = submitBtn.querySelector('.btn-content');
            const btnLoader = submitBtn.querySelector('.btn-loader');
            
            // Check if terms are accepted
            const termsCheckbox = document.getElementById('acceptTerms');
            if (termsCheckbox && !termsCheckbox.checked) {
                e.preventDefault();
                alert('Vui lòng đồng ý với điều khoản sử dụng!');
                return;
            }
            
            btnContent.classList.add('d-none');
            btnLoader.classList.remove('d-none');
            submitBtn.disabled = true;
        });
    }
    
    // =============== PASSWORD STRENGTH CHECKER ===============
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    if (passwordInput && strengthBar && strengthText) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            
            // Remove all strength classes
            strengthBar.classList.remove('weak', 'medium', 'strong');
            
            if (password.length === 0) {
                strengthBar.style.width = '0%';
                strengthText.textContent = 'Độ mạnh mật khẩu';
                strengthText.className = 'strength-text text-muted';
                return;
            }
            
            if (strength < 40) {
                strengthBar.classList.add('weak');
                strengthText.textContent = 'Mật khẩu yếu';
                strengthText.className = 'strength-text text-danger';
            } else if (strength < 70) {
                strengthBar.classList.add('medium');
                strengthText.textContent = 'Mật khẩu trung bình';
                strengthText.className = 'strength-text text-warning';
            } else {
                strengthBar.classList.add('strong');
                strengthText.textContent = 'Mật khẩu mạnh';
                strengthText.className = 'strength-text text-success';
            }
        });
    }
    
    function calculatePasswordStrength(password) {
        let strength = 0;
        
        // Length check
        if (password.length >= 6) strength += 20;
        if (password.length >= 8) strength += 10;
        if (password.length >= 10) strength += 10;
        
        // Contains lowercase
        if (/[a-z]/.test(password)) strength += 15;
        
        // Contains uppercase
        if (/[A-Z]/.test(password)) strength += 15;
        
        // Contains numbers
        if (/[0-9]/.test(password)) strength += 15;
        
        // Contains special characters
        if (/[^a-zA-Z0-9]/.test(password)) strength += 15;
        
        return strength;
    }
    
    // =============== PASSWORD MATCH VALIDATION ===============
    const confirmPasswordInput = document.getElementById('confirm_password');
    
    if (passwordInput && confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            if (this.value && this.value !== passwordInput.value) {
                this.classList.add('is-invalid');
                
                // Create or update error message
                let feedback = this.parentElement.querySelector('.invalid-feedback');
                if (!feedback) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    this.parentElement.appendChild(feedback);
                }
                feedback.innerHTML = '<i class="fas fa-times-circle me-1"></i>Mật khẩu không khớp';
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }
    
    // =============== INPUT FOCUS ANIMATION ===============
    const inputs = document.querySelectorAll('.custom-input');
    
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('input-focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('input-focused');
        });
    });
    
    // =============== SOCIAL LOGIN HANDLERS ===============
    const googleBtn = document.querySelector('.btn-google');
    const facebookBtn = document.querySelector('.btn-facebook');
    
    if (googleBtn) {
        googleBtn.addEventListener('click', function() {
            showNotification('Chức năng đăng nhập Google đang được phát triển!', 'info');
        });
    }
    
    if (facebookBtn) {
        facebookBtn.addEventListener('click', function() {
            showNotification('Chức năng đăng nhập Facebook đang được phát triển!', 'info');
        });
    }
    
    // =============== AUTO-HIDE ALERTS ===============
    const alerts = document.querySelectorAll('.custom-alert');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } else {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }
        }, 5000);
    });
    
    // =============== EMAIL VALIDATION ===============
    const emailInput = document.getElementById('email');
    
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                this.classList.add('is-invalid');
                
                let feedback = this.parentElement.querySelector('.invalid-feedback');
                if (!feedback) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    this.parentElement.appendChild(feedback);
                }
                feedback.innerHTML = '<i class="fas fa-times-circle me-1"></i>Email không hợp lệ';
            } else if (email) {
                this.classList.remove('is-invalid');
            }
        });
    }
    
    // =============== NOTIFICATION HELPER ===============
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <strong>${type === 'info' ? 'Thông báo' : 'Lỗi'}!</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    // =============== PREVENT MULTIPLE SUBMISSIONS ===============
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                
                // Re-enable after 3 seconds in case of errors
                setTimeout(() => {
                    submitBtn.disabled = false;
                    const btnContent = submitBtn.querySelector('.btn-content');
                    const btnLoader = submitBtn.querySelector('.btn-loader');
                    if (btnContent && btnLoader) {
                        btnContent.classList.remove('d-none');
                        btnLoader.classList.add('d-none');
                    }
                }, 3000);
            }
        });
    });
    
    // =============== REMEMBER ME FUNCTIONALITY ===============
    const rememberCheckbox = document.getElementById('rememberMe');
    const emailField = document.getElementById('email');
    
    if (rememberCheckbox && emailField) {
        // Load saved email on page load
        const savedEmail = localStorage.getItem('rememberedEmail');
        if (savedEmail) {
            emailField.value = savedEmail;
            rememberCheckbox.checked = true;
        }
        
        // Save email when checkbox is checked
        if (loginForm) {
            loginForm.addEventListener('submit', function() {
                if (rememberCheckbox.checked) {
                    localStorage.setItem('rememberedEmail', emailField.value);
                } else {
                    localStorage.removeItem('rememberedEmail');
                }
            });
        }
    }
    
    // =============== KEYBOARD SHORTCUTS ===============
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + Enter to submit form
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            const activeForm = document.querySelector('form');
            if (activeForm) {
                activeForm.requestSubmit();
            }
        }
    });
    
    // =============== INITIALIZE AOS (Animate On Scroll) ===============
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-out',
            once: true
        });
    }
});

// =============== SHOW PASSWORD REQUIREMENTS ===============
function showPasswordRequirements() {
    const passwordInput = document.getElementById('password');
    
    if (passwordInput && !document.getElementById('passwordRequirements')) {
        const requirements = document.createElement('div');
        requirements.id = 'passwordRequirements';
        requirements.className = 'mt-2 small text-muted';
        requirements.innerHTML = `
            <div><i class="fas fa-info-circle me-1"></i>Yêu cầu mật khẩu:</div>
            <ul class="mb-0 ps-4">
                <li>Tối thiểu 6 ký tự</li>
                <li>Nên chứa chữ hoa và chữ thường</li>
                <li>Nên chứa số và ký tự đặc biệt</li>
            </ul>
        `;
        
        passwordInput.parentElement.parentElement.appendChild(requirements);
    }
}

// Call on register page
if (document.getElementById('registerForm')) {
    showPasswordRequirements();
}
