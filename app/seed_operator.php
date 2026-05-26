<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use App\Models\User;

$email = 'operador@aldea.local';
$nombre = 'Operador';
$password = 'Operador123!';
$rol = 'operador';

// Verificar si ya existe
if (User::findByEmail($email)) {
    echo "Usuario con email {$email} ya existe\n";
    exit(0);
}

$id = User::create([
    'nombre' => $nombre,
    'email' => $email,
    'rol' => $rol,
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
]);

if ($id > 0) {
    echo "OK: Usuario operador creado (id={$id}, email={$email})\n";
    exit(0);
}

echo "Error: no se pudo crear el usuario operador\n";
exit(1);
