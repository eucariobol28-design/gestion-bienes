<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use App\Core\Database;

$config = require __DIR__ . '/Config/config.php';
$db = new Database($config['db']);
$pdo = $db->pdo();

$driver = strtolower((string)($config['db']['driver'] ?? 'sqlite'));

// Migración compatible con SQLite y MySQL/MariaDB.
if ($driver === 'mysql') {
  // MySQL/MariaDB
  $pdo->exec("CREATE TABLE IF NOT EXISTS bienes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    codigo VARCHAR(255) NOT NULL,
    ubicacion VARCHAR(255) NOT NULL,
    categoria VARCHAR(255) NOT NULL DEFAULT 'Sin categoría',
    estado VARCHAR(255) NOT NULL,
    responsable VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB");

  $pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    rol VARCHAR(20) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CHECK (rol IN ('admin','operador'))
  ) ENGINE=InnoDB");

  $pdo->exec("CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL UNIQUE
  ) ENGINE=InnoDB");

  $pdo->exec("CREATE TABLE IF NOT EXISTS ubicaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL UNIQUE
  ) ENGINE=InnoDB");

  // Seed básico
  $stmt = $pdo->prepare('SELECT id FROM categorias LIMIT 1');
  $stmt->execute();
  if (!$stmt->fetch()) {
    $pdo->exec("INSERT INTO categorias (nombre) VALUES ('Electrónicos'), ('Muebles'), ('Herramientas')");
  }

  $stmt = $pdo->prepare('SELECT id FROM ubicaciones LIMIT 1');
  $stmt->execute();
  if (!$stmt->fetch()) {
    $pdo->exec("INSERT INTO ubicaciones (nombre) VALUES ('Oficina Principal'), ('Almacén'), ('Aulas')");
  }

  // Admin por defecto
  $adminEmail = 'admin@aldea.local';
  $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
  $stmt->execute([':email' => $adminEmail]);
  if (!$stmt->fetch()) {
    $insert = $pdo->prepare('INSERT INTO users (nombre, email, rol, password_hash) VALUES (:nombre, :email, :rol, :ph)');
    $insert->execute([
      ':nombre' => 'Administrador',
      ':email' => $adminEmail,
      ':rol' => 'admin',
      ':ph' => password_hash('Admin1234!', PASSWORD_DEFAULT),
    ]);
  }

  // Operador por defecto
  $opEmail = 'operador@aldea.local';
  $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
  $stmt->execute([':email' => $opEmail]);
  if (!$stmt->fetch()) {
    $insert = $pdo->prepare('INSERT INTO users (nombre, email, rol, password_hash) VALUES (:nombre, :email, :rol, :ph)');
    $insert->execute([
      ':nombre' => 'Operador',
      ':email' => $opEmail,
      ':rol' => 'operador',
      ':ph' => password_hash('Operador123!', PASSWORD_DEFAULT),
    ]);
  }

  echo "OK: Tablas creadas/actualizadas (MySQL) y seed admin (admin@aldea.local / Admin1234!)\n";
  exit;
}

// SQLite migration simple: crea tablas nuevas y agrega columnas si faltan.

$pdo->exec("CREATE TABLE IF NOT EXISTS bienes (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nombre TEXT NOT NULL,
  codigo TEXT NOT NULL,
  ubicacion TEXT NOT NULL,
  categoria TEXT NOT NULL DEFAULT 'Sin categoría',
  estado TEXT NOT NULL,
  responsable TEXT NOT NULL,
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
)");

// Agregar categoria/created_at a tablas antiguas
$columns = $pdo->query("PRAGMA table_info('bienes')")->fetchAll(PDO::FETCH_ASSOC);
$columnNames = array_column($columns, 'name');

if (!in_array('categoria', $columnNames, true)) {
  $pdo->exec("ALTER TABLE bienes ADD COLUMN categoria TEXT NOT NULL DEFAULT 'Sin categoría'");
}

if (!in_array('created_at', $columnNames, true)) {
  // SQLite: ALTER COLUMN no existe como en otros motores.
  // Agregamos created_at como NULL y luego lo llenamos.
  $pdo->exec("ALTER TABLE bienes ADD COLUMN created_at TEXT NULL");
  $pdo->exec("UPDATE bienes SET created_at = datetime('now') WHERE created_at IS NULL");
}

$pdo->exec("CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nombre TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  rol TEXT NOT NULL,
  password_hash TEXT NOT NULL,
  created_at TEXT NOT NULL DEFAULT (datetime('now')),
  CHECK (rol IN ('admin','operador'))
)");

$columns = $pdo->query("PRAGMA table_info('users')")->fetchAll(PDO::FETCH_ASSOC);
$columnNames = array_column($columns, 'name');

if (!in_array('created_at', $columnNames, true)) {
  $pdo->exec("ALTER TABLE users ADD COLUMN created_at TEXT NULL");
  $pdo->exec("UPDATE users SET created_at = datetime('now') WHERE created_at IS NULL");
}

// Nota: si ya existía la tabla users antigua, el CHECK(rol IN ...) no se puede
// modificar fácilmente con ALTER TABLE. Para asegurar ese constraint habría
// que recrear la tabla.
// En esta app se valida desde PHP también (UsersController y CHECK en tabla nueva).

$pdo->exec("CREATE TABLE IF NOT EXISTS categorias (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nombre TEXT NOT NULL UNIQUE
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS ubicaciones (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nombre TEXT NOT NULL UNIQUE
)");

// Semilla datos básicos
$stmt = $pdo->prepare('SELECT id FROM categorias LIMIT 1');
$stmt->execute();
if (!$stmt->fetch()) {
  $pdo->exec("INSERT INTO categorias (nombre) VALUES ('Electrónicos'), ('Muebles'), ('Herramientas')");
}

$stmt = $pdo->prepare('SELECT id FROM ubicaciones LIMIT 1');
$stmt->execute();
if (!$stmt->fetch()) {
  $pdo->exec("INSERT INTO ubicaciones (nombre) VALUES ('Oficina Principal'), ('Almacén'), ('Aulas')");
}

// Admin por defecto
$adminEmail = 'admin@aldea.local';
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
$stmt->execute([':email' => $adminEmail]);

if (!$stmt->fetch()) {
  $insert = $pdo->prepare('INSERT INTO users (nombre, email, rol, password_hash) VALUES (:nombre, :email, :rol, :ph)');
  $insert->execute([
    ':nombre' => 'Administrador',
    ':email' => $adminEmail,
    ':rol' => 'admin',
    ':ph' => password_hash('Admin1234!', PASSWORD_DEFAULT),
  ]);
}

// Operador por defecto
$opEmail = 'operador@aldea.local';
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
$stmt->execute([':email' => $opEmail]);
if (!$stmt->fetch()) {
  $insert = $pdo->prepare('INSERT INTO users (nombre, email, rol, password_hash) VALUES (:nombre, :email, :rol, :ph)');
  $insert->execute([
    ':nombre' => 'Operador',
    ':email' => $opEmail,
    ':rol' => 'operador',
    ':ph' => password_hash('Operador123!', PASSWORD_DEFAULT),
  ]);
}

echo "OK: Tablas creadas/actualizadas y seed admin (admin@aldea.local / Admin1234!)\n";

