<?php
declare(strict_types=1); // Modo estricto de tipos.

namespace App\Core; // Namespace en Core.

final class Csrf // Clase final para protección CSRF (Cross-Site Request Forgery).
{
    public const SESSION_KEY = 'csrf_tokens'; // Constante para la clave de sesión donde se almacenan tokens.

    public static function token(string $formId): string // Genera o retorna un token CSRF para un formulario específico.
    {
        if (session_status() !== PHP_SESSION_ACTIVE) { // Verifica si la sesión está activa.
            session_start(); // Inicia sesión si no lo está.
        }

        $tokens = $_SESSION[self::SESSION_KEY] ?? []; // Obtiene tokens existentes de la sesión.
        if (!isset($tokens[$formId])) { // Si no hay token para este formId.
            $tokens[$formId] = bin2hex(random_bytes(32)); // Genera un token aleatorio seguro (64 caracteres hex).
            $_SESSION[self::SESSION_KEY] = $tokens; // Almacena en sesión.
        }

        return $tokens[$formId]; // Retorna el token.
    }

    public static function validate(string $formId, ?string $token): bool // Valida un token CSRF enviado.
    {
        $tokens = $_SESSION[self::SESSION_KEY] ?? []; // Obtiene tokens de sesión.
        if (empty($tokens[$formId]) || empty($token)) { // Si falta token en sesión o enviado.
            return false; // Inválido.
        }
        return hash_equals($tokens[$formId], $token); // Compara de forma segura para evitar timing attacks.
    }

    public static function regenerate(string $formId): void // Regenera el token para un formulario (después de uso).
    {
        $tokens = $_SESSION[self::SESSION_KEY] ?? []; // Obtiene tokens.
        $tokens[$formId] = bin2hex(random_bytes(32)); // Genera nuevo token.
        $_SESSION[self::SESSION_KEY] = $tokens; // Actualiza sesión.
    }
}

