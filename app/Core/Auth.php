<?php
declare(strict_types=1); // Activa el modo estricto de tipos para consistencia.

namespace App\Core; // Namespace para la clase Auth en Core.

use App\Models\User; // Importa el modelo User para consultas de autenticación.

final class Auth // Clase final para manejar autenticación y roles, usando sesiones.
{
    public const ROLE_ADMIN = 'admin'; // Constante para el rol de administrador.
    public const ROLE_OPERATOR = 'operador'; // Constante para el rol de operador.

    public static function user(): ?array // Método estático para obtener datos del usuario logueado.
    {
        return $_SESSION['user'] ?? null; // Retorna el array de usuario de la sesión, o null si no existe.
    }

    public static function check(): bool // Verifica si hay un usuario logueado.
    {
        return !empty($_SESSION['user']); // Retorna true si la sesión tiene datos de usuario.
    }

    public static function role(): ?string // Obtiene el rol del usuario logueado.
    {
        return $_SESSION['user']['rol'] ?? null; // Retorna el rol desde la sesión, o null.
    }

    public static function requireLogin(): void // Requiere que el usuario esté logueado; redirige si no.
    {
        if (!self::check()) { // Si no está logueado.
            header('Location: /auth/login'); // Redirige a la página de login.
            exit; // Termina la ejecución.
        }
    }

    public static function requireRole(string $role): void // Requiere un rol específico; deniega acceso si no coincide.
    {
        self::requireLogin(); // Primero verifica login.
        if ((self::role() ?? '') !== $role) { // Si el rol no coincide.
            http_response_code(403); // Código HTTP 403 (prohibido).
            echo '403 - Acceso denegado'; // Mensaje de error.
            exit; // Termina.
        }
    }

    public static function login(string $email, string $password): bool // Intenta loguear al usuario con email y contraseña.
    {
        $user = User::findByEmail($email); // Busca al usuario por email en la DB.
        if (!$user) { // Si no se encuentra.
            return false; // Retorna false.
        }

        if (!password_verify($password, $user['password_hash'] ?? '')) { // Verifica la contraseña hasheada.
            return false; // Si no coincide, false.
        }

        $_SESSION['user'] = [ // Almacena datos del usuario en la sesión.
            'id' => (int)$user['id'], // ID como entero.
            'nombre' => $user['nombre'], // Nombre.
            'email' => $user['email'], // Email.
            'rol' => $user['rol'], // Rol.
        ];

        return true; // Login exitoso.
    }

    public static function logout(): void // Cierra la sesión del usuario.
    {
        $_SESSION = []; // Limpia la sesión.
        if (ini_get('session.use_cookies')) { // Si se usan cookies para sesiones.
            $params = session_get_cookie_params(); // Obtiene parámetros de la cookie de sesión.
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool)$params['secure'], (bool)$params['httponly']); // Borra la cookie.
        }
        session_destroy(); // Destruye la sesión.
    }
}

