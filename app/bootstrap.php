<?php
declare(strict_types=1); // Modo estricto.

// Cargar variables de entorno
if (file_exists(__DIR__ . '/../.env')) { // Si existe .env en raíz.
    require_once __DIR__ . '/../vendor/autoload.php'; // Carga autoloader de Composer (para Dotenv).
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../'); // Crea Dotenv.
    $dotenv->load(); // Carga variables env.
}

require_once __DIR__ . '/Config/config.php'; // Carga configuración.

spl_autoload_register(function (string $class): void { // Registra función de autoloading para clases.
    // App\Core\Router => app/Core/Router.php
    $prefix = 'App\\'; // Prefijo del namespace.
    $baseDir = __DIR__ . '/'; // Directorio base (app/).

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) { // Si clase no empieza con App\, ignora.
        return;
    }

    $relativeClass = substr($class, strlen($prefix)); // Obtiene parte relativa (ej. Core/Router).
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php'; // Construye ruta de archivo.

    // Soporte multiplataforma: si no existe con el caso exacto, intentamos buscar ignorando mayúsculas.
    if (!file_exists($file)) { // Si archivo no existe.
        $candidateDir = dirname($file); // Directorio candidato.
        $candidateBase = basename($file); // Nombre base.
        if (is_dir($candidateDir)) { // Si directorio existe.
            $entries = scandir($candidateDir) ?: []; // Lista archivos.
            foreach ($entries as $entry) { // Busca coincidencia ignorando case.
                if (strcasecmp($entry, $candidateBase) === 0) {
                    $file = $candidateDir . '/' . $entry; // Actualiza ruta.
                    break;
                }
            }
        }
    }

    if (file_exists($file)) { // Si archivo existe.
        require $file; // Incluye el archivo.
    }
});


