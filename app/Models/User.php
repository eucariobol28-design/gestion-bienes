<?php
declare(strict_types=1); // Modo estricto.

namespace App\Models; // Namespace en Models.

use App\Core\Database; // Importa Database para conexiones.

final class User // Clase final para modelo de Usuario.
{
    public static function findByEmail(string $email): ?array // Busca usuario por email.
    {
        $config = require __DIR__ . '/../Config/config.php'; // Carga config.
        $pdo = Database::getInstance($config['db']); // Obtiene instancia PDO singleton.

        $stmt = $pdo->prepare('SELECT id, nombre, email, rol, password_hash FROM users WHERE email = :email LIMIT 1'); // Query preparada.
        $stmt->execute([':email' => $email]); // Ejecuta con email.

        $row = $stmt->fetch(); // Obtiene fila.
        return $row ?: null; // Retorna array o null.
    }

    public static function all(): array
    {
        $config = require __DIR__ . '/../Config/config.php';
        $pdo = Database::getInstance($config['db']);
        $stmt = $pdo->query('SELECT id, nombre, email, rol FROM users ORDER BY nombre');
        return $stmt->fetchAll();
    }

    public static function search(array $filters, int $limit, int $offset): array
    {
        $config = require __DIR__ . '/../Config/config.php';
        $pdo = Database::getInstance($config['db']);

        $params = [];
        $where = self::buildFilterSql($filters, $params);
        $sql = 'SELECT id, nombre, email, rol FROM users' . $where . ' ORDER BY nombre LIMIT :limit OFFSET :offset';
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function count(array $filters): int
    {
        $config = require __DIR__ . '/../Config/config.php';
        $pdo = Database::getInstance($config['db']);

        $params = [];
        $where = self::buildFilterSql($filters, $params);
        $stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM users' . $where);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return isset($row['total']) ? (int)$row['total'] : 0;
    }

    private static function buildFilterSql(array $filters, array &$params): string
    {
        $clauses = [];
        $search = trim((string)($filters['search'] ?? ''));
        if ($search !== '') {
            $clauses[] = '(nombre LIKE :search OR email LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }
        $role = trim((string)($filters['role'] ?? ''));
        if ($role !== '') {
            $clauses[] = 'rol = :role';
            $params[':role'] = $role;
        }
        return $clauses ? ' WHERE ' . implode(' AND ', $clauses) : '';
    }

    public static function findById(int $id): ?array
    {
        $config = require __DIR__ . '/../Config/config.php';
        $pdo = Database::getInstance($config['db']);
        $stmt = $pdo->prepare('SELECT id, nombre, email, rol FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function existsByEmail(string $email, int $excludeId = 0): bool
    {
        $config = require __DIR__ . '/../Config/config.php';
        $pdo = Database::getInstance($config['db']);
        $sql = 'SELECT 1 FROM users WHERE email = :email';
        $params = [':email' => $email];
        if ($excludeId > 0) {
            $sql .= ' AND id <> :id';
            $params[':id'] = $excludeId;
        }
        $stmt = $pdo->prepare($sql . ' LIMIT 1');
        $stmt->execute($params);
        return (bool)$stmt->fetch();
    }

    public static function create(array $data): int
    {
        $config = require __DIR__ . '/../Config/config.php';
        $pdo = Database::getInstance($config['db']);
        $stmt = $pdo->prepare('INSERT INTO users (nombre, email, rol, password_hash) VALUES (:nombre, :email, :rol, :ph)');
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':email' => $data['email'],
            ':rol' => $data['rol'],
            ':ph' => $data['password_hash'],
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $config = require __DIR__ . '/../Config/config.php';
        $pdo = Database::getInstance($config['db']);
        $fields = 'nombre = :nombre, email = :email, rol = :rol';
        $params = [
            ':id' => $id,
            ':nombre' => $data['nombre'],
            ':email' => $data['email'],
            ':rol' => $data['rol'],
        ];
        if (!empty($data['password_hash'])) {
            $fields .= ', password_hash = :ph';
            $params[':ph'] = $data['password_hash'];
        }
        $stmt = $pdo->prepare("UPDATE users SET {$fields} WHERE id = :id");
        return $stmt->execute($params);
    }

    public static function deleteById(int $id): bool
    {
        $config = require __DIR__ . '/../Config/config.php';
        $pdo = Database::getInstance($config['db']);
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}

