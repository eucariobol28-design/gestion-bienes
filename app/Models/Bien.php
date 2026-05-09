<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Bien
{
    public static function all(?string $estado = null): array
    {
        $config = require __DIR__ . '/../Config/config.php';
        $pdo = Database::getInstance($config['db']);

        $sql = 'SELECT id, nombre, codigo, ubicacion, categoria, estado, responsable FROM bienes';
        $params = [];

        if ($estado !== null && $estado !== '') {
            $sql .= ' WHERE estado = :estado';
            $params[':estado'] = $estado;
        }

        $sql .= ' ORDER BY id DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $config = require __DIR__ . '/../Config/config.php';
        $pdo = Database::getInstance($config['db']);

        $stmt = $pdo->prepare('SELECT id, nombre, codigo, ubicacion, categoria, estado, responsable FROM bienes WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $config = require __DIR__ . '/../Config/config.php';
        $pdo = Database::getInstance($config['db']);

        $stmt = $pdo->prepare('
            INSERT INTO bienes (nombre, codigo, ubicacion, categoria, estado, responsable)
            VALUES (:nombre, :codigo, :ubicacion, :categoria, :estado, :responsable)
        ');

        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':codigo' => $data['codigo'],
            ':ubicacion' => $data['ubicacion'],
            ':categoria' => $data['categoria'],
            ':estado' => $data['estado'],
            ':responsable' => $data['responsable'],
        ]);

        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $config = require __DIR__ . '/../Config/config.php';
        $pdo = Database::getInstance($config['db']);

        $stmt = $pdo->prepare('
            UPDATE bienes
            SET nombre = :nombre,
                codigo = :codigo,
                ubicacion = :ubicacion,
                categoria = :categoria,
                estado = :estado,
                responsable = :responsable
            WHERE id = :id
        ');

        return $stmt->execute([
            ':id' => $id,
            ':nombre' => $data['nombre'],
            ':codigo' => $data['codigo'],
            ':ubicacion' => $data['ubicacion'],
            ':categoria' => $data['categoria'],
            ':estado' => $data['estado'],
            ':responsable' => $data['responsable'],
        ]);
    }

    public static function delete(int $id): bool
    {
        $config = require __DIR__ . '/../Config/config.php';
        $pdo = Database::getInstance($config['db']);

        $stmt = $pdo->prepare('DELETE FROM bienes WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}

