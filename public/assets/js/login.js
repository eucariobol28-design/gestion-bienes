document.addEventListener('DOMContentLoaded', () => {
  const loginForm = document.getElementById('loginForm');
  if (!loginForm) return;

  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const togglePassword = document.getElementById('togglePassword');
  const eyeIcon = document.getElementById('eyeIcon');
  const submitBtn = document.getElementById('submitBtn');
  const btnText = document.getElementById('btnText');

  const generalAlert = document.getElementById('general-alert'); // opcional

  const emailErrorEl = document.getElementById('email-error');
  const passwordErrorEl = document.getElementById('password-error');

  // Toggle visibility of password
  if (togglePassword && passwordInput && eyeIcon) {
    togglePassword.addEventListener('click', () => {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      eyeIcon.classList.toggle('fa-eye');
      eyeIcon.classList.toggle('fa-eye-slash');
    });
  }

  const validateEmail = (email) => {
    return String(email)
      .toLowerCase()
      .match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
  };

  const setError = (input, errorElement, show) => {
    if (!input || !errorElement) return;
    if (show) {
      input.classList.add('input-error');
      errorElement.style.display = 'block';
    } else {
      input.classList.remove('input-error');
      errorElement.style.display = 'none';
    }
  };

  function setLoading(isLoading) {
    if (!submitBtn || !btnText) return;

    if (isLoading) {
      submitBtn.disabled = true;
      submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
      btnText.innerHTML = '<span class="loader"></span> Procesando...';
    } else {
      submitBtn.disabled = false;
      submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
      btnText.innerHTML = 'Iniciar Sesión';
    }
  }

  // Validaciones + submit real (backend)
  loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    // Ocultar alert general si existe
    if (generalAlert) generalAlert.classList.add('hidden');

    let isValid = true;

    if (!validateEmail(emailInput.value)) {
      setError(emailInput, emailErrorEl, true);
      isValid = false;
    } else {
      setError(emailInput, emailErrorEl, false);
    }

    if ((passwordInput.value || '').length < 6) {
      setError(passwordInput, passwordErrorEl, true);
      isValid = false;
    } else {
      setError(passwordInput, passwordErrorEl, false);
    }

    if (!isValid) return;

    // Simulación visual (para feedback inmediato) + luego submit real.
    setLoading(true);

    setTimeout(() => {
      const email = emailInput.value.trim();
      const pass = passwordInput.value;

      // Simulación solo para UI; el resultado final lo define el backend
      if (email === 'admin@gestion.com' && pass === 'password123') {
        btnText.innerText = '¡Acceso concedido!';
        submitBtn.classList.remove('bg-blue-600');
        submitBtn.classList.add('bg-emerald-500');
        setTimeout(() => {
          setLoading(false);
          loginForm.submit();
        }, 300);
      } else {
        // No mostramos aquí error; el backend lo hará con flash.
        setTimeout(() => {
          loginForm.submit();
        }, 200);
      }
    }, 800);
  });

  // Real-time error cleaning
  if (emailInput) {
    emailInput.addEventListener('input', () => {
      if (validateEmail(emailInput.value)) {
        setError(emailInput, emailErrorEl, false);
      }
    });
  }

  if (passwordInput) {
    passwordInput.addEventListener('input', () => {
      if ((passwordInput.value || '').length >= 6) {
        setError(passwordInput, passwordErrorEl, false);
      }
    });
  }
});

