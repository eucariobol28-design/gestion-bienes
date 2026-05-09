<?php
declare(strict_types=1); // Activa el modo estricto de tipos para asegurar consistencia en tipos de datos.

namespace App\Core; // Define el namespace para la clase Database en el directorio Core.

use PDO; // Importa la clase PDO para conexiones a bases de datos.
use PDOException; // Importa PDOException para manejar errores de conexión.

final class Database // Define la clase Database como final (no heredable), implementando patrón singleton.
{
    private static ?PDO $pdo = null; // Propiedad estática privada para almacenar la instancia única de PDO (singleton). Inicializada en null.

    public static function getInstance(array $config): PDO // Método estático público para obtener la instancia de PDO. Recibe configuración como array y retorna PDO.
    {
        if (self::$pdo === null) { // Verifica si la instancia ya existe; si no, la crea (lazy loading).
            $driver = $config['driver'] ?? 'sqlite'; // Obtiene el driver de DB de la config, por defecto 'sqlite'.

            try { // Bloque try para capturar excepciones durante la conexión.
                if ($driver === 'sqlite') { // Si el driver es SQLite.
                    $dbPath = $config['database']; // Ruta al archivo de DB.
                    self::ensureParentDir($dbPath); // Asegura que el directorio padre exista.
                    $dsn = 'sqlite:' . $dbPath; // Construye el DSN (Data Source Name) para SQLite.
                    self::$pdo = new PDO($dsn, null, null, [ // Crea la conexión PDO con opciones.
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en errores.
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Modo de fetch por defecto: arrays asociativos.
                        PDO::ATTR_EMULATE_PREPARES => false, // Desactiva emulación de prepared statements para seguridad.
                    ]);
                } else { // Si no es SQLite, asume MySQL (puedes extender para otros).
                    $host = $config['host'] ?? '127.0.0.1'; // Host de la DB.
                    $dbName = $config['database'] ?? ''; // Nombre de la base de datos.
                    $user = $config['username'] ?? ''; // Usuario de la DB.
                    $pass = $config['password'] ?? ''; // Contraseña de la DB.
                    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $dbName); // Construye DSN para MySQL con charset UTF-8.
                    self::$pdo = new PDO($dsn, $user, $pass, [ // Crea conexión PDO para MySQL.
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Opciones iguales a SQLite.
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                }
            } catch (PDOException $e) { // Captura excepciones de PDO.
                throw new PDOException('Error conectando a la base de datos: ' . $e->getMessage(), (int)$e->getCode()); // Relanza con mensaje personalizado.
            }
        }

        return self::$pdo; // Retorna la instancia de PDO (siempre la misma).
    }

    private static function ensureParentDir(string $dbPath): void // Método privado estático para crear directorios si no existen (útil para SQLite).
    {
        $dir = dirname($dbPath); // Obtiene el directorio padre de la ruta del archivo DB.
        if (!is_dir($dir)) { // Si el directorio no existe.
            mkdir($dir, 0775, true); // Crea el directorio con permisos 0775 (lectura/escritura para owner/grupo, lectura para otros) y recursivo.
        }
    }

    // Para compatibilidad, mantener instancia
    public function __construct(array $config) // Constructor público para compatibilidad (aunque no se use directamente).
    {
        // No hacer nada, usar getInstance // Comentario: No inicializa nada aquí; el singleton se maneja en getInstance.
    }

    public function pdo(): PDO // Método público para obtener PDO (para compatibilidad con código antiguo).
    {
        $config = require __DIR__ . '/../Config/config.php'; // Carga la configuración desde el archivo config.php.
        return self::getInstance($config['db']); // Retorna la instancia singleton usando la config cargada.
    }
}

