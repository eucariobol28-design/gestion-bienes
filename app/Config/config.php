<?php
declare(strict_types=1); // Modo estricto.

// Cargar variables de entorno si existe .env
if (file_exists(__DIR__ . '/../../.env')) { // Verifica si existe archivo .env en raíz.
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../'); // Crea instancia de Dotenv.
    $dotenv->load(); // Carga variables de entorno.
}

return [ // Retorna array de configuración.
    'app_name' => $_ENV['APP_NAME'] ?? 'Registro y Control de Bienes - Aldea Universitaria', // Nombre de app desde env o default.
    'app_env' => $_ENV['APP_ENV'] ?? 'development', // Entorno (dev/prod).
    'db' => [ // Config DB.
        'driver' => $_ENV['DB_DRIVER'] ?? 'sqlite', // Driver: sqlite o mysql.
        'database' => $_ENV['DB_DRIVER'] === 'sqlite' ? (__DIR__ . '/../../storage/' . ($_ENV['DB_DATABASE'] ?? 'database.sqlite')) : ($_ENV['DB_DATABASE'] ?? ''), // Ruta para sqlite, nombre para mysql.
        'host' => $_ENV['DB_HOST'] ?? '127.0.0.1', // Host DB.
        'username' => $_ENV['DB_USERNAME'] ?? null, // Usuario DB.
        'password' => $_ENV['DB_PASSWORD'] ?? null, // Contraseña DB.
    ],
    'security' => [ // Config seguridad.
        'csrf_key' => $_ENV['CSRF_KEY'] ?? 'cambia-esta-clave-por-una-aleatoria-y-segura', // Clave CSRF (cambiar en prod).
    ],
];

