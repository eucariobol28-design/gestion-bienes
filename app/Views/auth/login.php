<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carlos Emiliano Salóm de la Misión Sucre - Gestión de Bienes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background: #f3f4f6;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .bg-image {
            background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80');
            background-size: cover;
            background-position: center;
        }

        .input-error {
            border-color: #ef4444 !important;
            background-color: #fef2f2;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: none;
        }

        .loader {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 8px;
            vertical-align: middle;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="max-w-5xl w-full flex flex-col md:flex-row shadow-2xl rounded-2xl overflow-hidden bg-white">

        <!-- Lado Izquierdo: Imagen y Mensaje -->
        <div class="hidden md:flex md:w-1/2 bg-image relative">
            <div class="absolute inset-0 bg-slate-900/60 flex flex-col justify-center p-12 text-white">
                <div class="mb-6">
                    <img src="/assets/images/mision%20sucre.jpeg" alt="Logo Misión Sucre" class="w-20 h-20 rounded-full mx-auto mb-4">
                </div>
                <h1 class="text-4xl font-bold mb-4">Aldea  Universitaria  <br>Carlos Emiliano Salom</h1>
                <p class="text-lg text-slate-200">
                    La plataforma líder para la administración inteligente de bienes  de la Misión Sucre.
                </p>
                <div class="mt-8 flex items-center gap-4 text-sm opacity-80">
                    <div class="flex -space-x-2">
                        <div class="w-8 h-8 rounded-full bg-blue-500 border-2 border-white"></div>
                        <div class="w-8 h-8 rounded-full bg-emerald-500 border-2 border-white"></div>
                        <div class="w-8 h-8 rounded-full bg-orange-500 border-2 border-white"></div>
                    </div>
                    
                </div>
            </div>
        </div>

        <!-- Lado Derecho: Formulario de Login -->
        <div class="w-full md:w-1/2 p-8 lg:p-12">
            <div class="max-w-md mx-auto">
                <div class="md:hidden mb-8 text-center">
                    <img src="/assets/images/mision%20sucre.jpeg" alt="Logo Misión Sucre" class="w-16 h-16 rounded-full mx-auto mb-2">
                    <h2 class="text-2xl font-bold text-gray-800">Carrolos Emiliano</h2>
                    <p class="text-sm text-gray-500">Salón de la Misión Sucre</p>
                </div>                <div class="mb-10">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Bienvenido</h2>
                    <p class="text-gray-500">Ingrese sus credenciales para acceder al sistema.</p>
                </div>

                <!-- Mensaje de Error General -->
                <div id="general-alert" class="hidden mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span id="general-alert-text">Credenciales incorrectas. Intente de nuevo.</span>
                </div>

                <?php require __DIR__ . '/../components/flash.php'; ?>

                <form id="loginForm" method="post" action="/auth/doLogin" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">

                    <!-- Email -->
                    <div class="mb-5">
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Correo Electrónico</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" id="email" name="email"
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="usuario@empresa.com" autocomplete="username" required>
                        </div>
                        <p id="email-error" class="error-message">Por favor ingrese un correo válido.</p>
                    </div>

                    <!-- Password -->
                    <div class="mb-5">
                        <div class="flex justify-between mb-2">
                            <label for="password" class="text-sm font-semibold text-gray-700">Contraseña</label>
                            <a href="#" class="text-xs text-blue-600 hover:underline">¿Olvidó su contraseña?</a>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" id="password" name="password"
                                class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="••••••••" autocomplete="current-password" required>
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        <p id="password-error" class="error-message">La contraseña debe tener al menos 6 caracteres.</p>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center mb-8">
                        <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="remember" class="ml-2 text-sm text-gray-600">Recordar sesión en este equipo</label>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" id="submitBtn"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg shadow-blue-200 transition-all active:scale-95 flex items-center justify-center">
                        <span id="btnText">Iniciar Sesión</span>
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500">
                        ¿Necesita ayuda? <a href="#" class="text-blue-600 font-semibold hover:underline">Contacte a soporte técnico</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const loginForm = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');
            const eyeIcon = document.getElementById('eyeIcon');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const generalAlert = document.getElementById('general-alert');

            // Toggle visibility of password
            togglePassword.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                eyeIcon.classList.toggle('fa-eye');
                eyeIcon.classList.toggle('fa-eye-slash');
            });

            // Validation Helper
            const validateEmail = (email) => {
                return String(email)
                    .toLowerCase()
                    .match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
            };

            const setError = (input, errorElement, show) => {
                if (show) {
                    input.classList.add('input-error');
                    errorElement.style.display = 'block';
                } else {
                    input.classList.remove('input-error');
                    errorElement.style.display = 'none';
                }
            };

            // Form Submit Logic
            loginForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                generalAlert.classList.add('hidden');

                let isValid = true;

                // Validate Email
                if (!validateEmail(emailInput.value)) {
                    setError(emailInput, document.getElementById('email-error'), true);
                    isValid = false;
                } else {
                    setError(emailInput, document.getElementById('email-error'), false);
                }

                // Validate Password
                if (passwordInput.value.length < 6) {
                    setError(passwordInput, document.getElementById('password-error'), true);
                    isValid = false;
                } else {
                    setError(passwordInput, document.getElementById('password-error'), false);
                }

                if (!isValid) return;

                // Submit the form normally
                loginForm.submit();
            });

            // Real-time error cleaning
            emailInput.addEventListener('input', () => {
                if (validateEmail(emailInput.value)) {
                    setError(emailInput, document.getElementById('email-error'), false);
                }
            });

            passwordInput.addEventListener('input', () => {
                if (passwordInput.value.length >= 6) {
                    setError(passwordInput, document.getElementById('password-error'), false);
                }
            });
        });
    </script>

</body>
</html>


