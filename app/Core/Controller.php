<?php
declare(strict_types=1); // Modo estricto.

namespace App\Core; // Namespace en Core.

abstract class Controller // Clase abstracta base para todos los controladores, proporciona métodos comunes.
{
    protected function render(string $view, array $data = [], string $layout = 'layouts/header'): void // Renderiza una vista con datos y layout.
    {
        extract($data, EXTR_SKIP); // Extrae variables del array $data al scope local (con EXTR_SKIP para evitar sobrescribir).

        if ($layout !== '') {
            // header
            require __DIR__ . '/../Views/' . $layout . '.php'; // Incluye el layout de header.
        }
        // view
        require __DIR__ . '/../Views/' . $view . '.php'; // Incluye la vista específica.
        if ($layout !== '') {
            // footer
            require __DIR__ . '/../Views/layouts/footer.php'; // Incluye el footer.
        }
    }

    protected function flash(string $type, string $message): void // Almacena un mensaje flash en sesión (para mostrar una vez).
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message]; // Guarda tipo (ej. 'success') y mensaje.
    }

    protected function redirect(string $path): void // Redirige a una URL y termina la ejecución.
    {
        header('Location: ' . $path); // Envía header de redirección.
        exit; // Sale del script.
    }
}

