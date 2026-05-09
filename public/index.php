<?php
declare(strict_types=1); // Modo estricto.

session_start(); // Inicia sesión para manejo de usuario.

require_once __DIR__ . '/../app/bootstrap.php'; // Carga bootstrap (config, autoloader).

use App\Core\Router; // Importa Router.

try { // Bloque try para capturar excepciones.
    $router = new Router(); // Instancia router.
    $router->dispatch(); // Despacha la solicitud a controlador/acción.
} catch (Throwable $e) { // Captura cualquier error.
    error_log($e->getMessage()); // Registra error en log.
    http_response_code(500); // Código HTTP 500.
    $file = __DIR__ . '/../app/Views/layouts/500.php'; // Ruta a vista 500.
    if (is_file($file)) { // Si existe vista.
        require $file; // Muestra vista 500.
    } else {
        echo '500 - Error interno del servidor'; // Mensaje fallback.
    }
}


