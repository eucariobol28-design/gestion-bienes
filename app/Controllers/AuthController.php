<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Auth;
use App\Models\User;

final class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        $this->render('auth/login', [
            'title' => 'Iniciar sesión',
            'csrf' => Csrf::token('login'),
        ]);
    }

    public function doLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        $token = $_POST['csrf_token'] ?? null;
        if (!Csrf::validate('login', $token)) {
            http_response_code(419);
            echo 'CSRF inválido';
            exit;
        }
        Csrf::regenerate('login');

        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->flash('warning', 'Credenciales inválidas');
            $this->redirect('/auth/login');
            return;
        }

        if (!Auth::login($email, $password)) {
            $this->flash('danger', 'Usuario o contraseña incorrectos');
            $this->redirect('/auth/login');
            return;
        }

        $this->flash('success', 'Sesión iniciada');
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->flash('info', 'Sesión cerrada');
        $this->redirect('/auth/login');
    }
}

