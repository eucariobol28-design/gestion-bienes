<?php
declare(strict_types=1); // Modo estricto.

require_once __DIR__ . '/bootstrap.php'; // Carga bootstrap (config, autoloader).

use App\Core\Database; // Importa Database.

$config = require __DIR__ . '/Config/config.php'; // Carga config.
$db = new Database($config['db']); // Instancia DB (aunque usa singleton).
$pdo = $db->pdo(); // Obtiene PDO.

// Tablas
$pdo->exec('CREATE TABLE IF NOT EXISTS bienes (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nombre TEXT NOT NULL,
  codigo TEXT NOT NULL,
  ubicacion TEXT NOT NULL,
  categoria TEXT NOT NULL DEFAULT "Sin categoría",
  estado TEXT NOT NULL,
  responsable TEXT NOT NULL
)');

$columns = $pdo->query("PRAGMA table_info('bienes')")->fetchAll(PDO::FETCH_ASSOC);
$columnNames = array_column($columns, 'name');
if (!in_array('categoria', $columnNames, true)) {
    $pdo->exec('ALTER TABLE bienes ADD COLUMN categoria TEXT NOT NULL DEFAULT "Sin categoría"');
}

$pdo->exec('CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nombre TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  rol TEXT NOT NULL,
  password_hash TEXT NOT NULL
)');

$pdo->exec('CREATE TABLE IF NOT EXISTS categorias (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nombre TEXT NOT NULL UNIQUE
)');

$pdo->exec('CREATE TABLE IF NOT EXISTS ubicaciones (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nombre TEXT NOT NULL UNIQUE
)');

// Semilla datos básicos
$stmt = $pdo->prepare('SELECT id FROM categorias LIMIT 1'); // Verifica si hay categorías.
$stmt->execute();
if (!$stmt->fetch()) { // Si no, inserta algunas.
  $pdo->exec("INSERT INTO categorias (nombre) VALUES ('Electrónicos'), ('Muebles'), ('Herramientas')");
}

$stmt = $pdo->prepare('SELECT id FROM ubicaciones LIMIT 1'); // Verifica ubicaciones.
$stmt->execute();
if (!$stmt->fetch()) { // Si no, inserta algunas.
  $pdo->exec("INSERT INTO ubicaciones (nombre) VALUES ('Oficina Principal'), ('Almacén'), ('Aulas')");
}
$adminEmail = 'admin@aldea.local'; // Email del admin por defecto.
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1'); // Verifica si existe.
$stmt->execute([':email' => $adminEmail]);
if (!$stmt->fetch()) { // Si no existe.
  $insert = $pdo->prepare('INSERT INTO users (nombre, email, rol, password_hash) VALUES (:nombre, :email, :rol, :ph)'); // Inserta admin.
  $insert->execute([
    ':nombre' => 'Administrador',
    ':email' => $adminEmail,
    ':rol' => 'admin',
    ':ph' => password_hash('Admin1234!', PASSWORD_DEFAULT), // Hash de contraseña.
  ]);
}

echo "OK: Tablas creadas y seed admin (admin@aldea.local / Admin1234!)\n"; // Mensaje de éxito.

