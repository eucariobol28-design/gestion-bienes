<?php
declare(strict_types=1); // Modo estricto.

namespace App\Models; // Namespace en Models.

use App\Core\Database; // Importa Database.

final class Ubicacion // Clase final para modelo de Ubicación.
{
    public static function all(): array // Obtiene todas las ubicaciones.
    {
        $config = require __DIR__ . '/../Config/config.php'; // Carga config.
        $pdo = Database::getInstance($config['db']); // Obtiene PDO singleton.

        $stmt = $pdo->query('SELECT id, nombre FROM ubicaciones ORDER BY nombre'); // Query ordenada.
        return $stmt->fetchAll(); // Retorna array.
    }

    public static function find(int $id): ?array // Busca ubicación por ID.
    {
        $config = require __DIR__ . '/../Config/config.php';
        $pdo = Database::getInstance($config['db']);

        $stmt = $pdo->prepare('SELECT id, nombre FROM ubicaciones WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int // Crea nueva ubicación.
    {
        $config = require __DIR__ . '/../Config/config.php';
        $pdo = Database::getInstance($config['db']);

        $stmt = $pdo->prepare('INSERT INTO ubicaciones (nombre) VALUES (:nombre)');
        $stmt->execute([':nombre' => $data['nombre']]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool // Actualiza ubicación.
    {
        $config = require __DIR__ . '/../Config/config.php';
        $pdo = Database::getInstance($config['db']);

        $stmt = $pdo->prepare('UPDATE ubicaciones SET nombre = :nombre WHERE id = :id');
        return $stmt->execute([':id' => $id, ':nombre' => $data['nombre']]);
    }

    public static function delete(int $id): bool // Elimina ubicación.
    {
        $config = require __DIR__ . '/../Config/config.php';
        $pdo = Database::getInstance($config['db']);

        $stmt = $pdo->prepare('DELETE FROM ubicaciones WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}