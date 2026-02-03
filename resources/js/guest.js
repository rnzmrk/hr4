document.addEventListener('DOMContentLoaded', function() {
  // Password toggle functionality
  const togglePassword = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('password');
  const passwordIcon = document.getElementById('passwordIcon');
  
  if (togglePassword && passwordInput && passwordIcon) {
    togglePassword.addEventListener('click', function() {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      
      // Toggle icon
      if (type === 'text') {
        passwordIcon.classList.remove('bi-eye');
        passwordIcon.classList.add('bi-eye-slash');
      } else {
        passwordIcon.classList.remove('bi-eye-slash');
        passwordIcon.classList.add('bi-eye');
      }
    });
  }

  // Form submission loading state
  const loginForm = document.getElementById('loginForm');
  const loginBtn = document.getElementById('loginBtn');
  const btnText = document.getElementById('btnText');
  
  if (loginForm && loginBtn && btnText) {
    loginForm.addEventListener('submit', function() {
      loginBtn.disabled = true;
      btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Signing In...';
    });
  }

  // Auto-focus email field
  const emailInput = document.getElementById('email');
  if (emailInput) {
    emailInput.focus();
  }

  // Add input validation feedback
  const inputs = document.querySelectorAll('.form-control');
  inputs.forEach(input => {
    input.addEventListener('blur', function() {
      if (this.value && this.checkValidity()) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
      } else if (this.value) {
        this.classList.remove('is-valid');
        this.classList.add('is-invalid');
      }
    });
  });
});
