<?php
declare(strict_types=1); // Modo estricto.

namespace App\Controllers; // Namespace en Controllers.

use App\Core\Controller; // Importa Controller.
use App\Core\Auth; // Importa Auth.
use App\Core\Csrf; // Importa CSRF.
use App\Core\Validator; // Importa Validator.
use App\Models\User; // Importa User.

final class UsersController extends Controller // Controlador para usuarios.
{
    public function index(): void // Lista usuarios.
    {
        Auth::requireRole('admin'); // Solo admin.

        $search = trim((string)($_GET['search'] ?? ''));
        $role = trim((string)($_GET['role'] ?? ''));
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        $filters = ['search' => $search, 'role' => $role];
        $total = User::count($filters);
        $pages = max(1, (int)ceil($total / $perPage));
        $page = min($page, $pages);
        $users = User::search($filters, $perPage, ($page - 1) * $perPage);

        $this->render('users/index', [
            'title' => 'Usuarios',
            'csrf' => Csrf::token('user_delete'),
            'users' => $users,
            'search' => $search,
            'role' => $role,
            'page' => $page,
            'pages' => $pages,
            'total' => $total,
            'perPage' => $perPage,
        ]);
    }

    public function create(): void // Formulario crear usuario.
    {
        Auth::requireRole('admin');
        $this->render('users/create', [
            'title' => 'Crear Usuario',
            'csrf' => Csrf::token('user_create'),
        ]);
    }

    public function store(): void // Procesa creación.
    {
        Auth::requireRole('admin');

        $token = $_POST['csrf_token'] ?? null;
        if (!Csrf::validate('user_create', $token)) {
            http_response_code(419);
            echo 'CSRF inválido';
            exit;
        }
        Csrf::regenerate('user_create');

        $data = [
            'nombre' => trim((string)($_POST['nombre'] ?? '')),
            'email' => trim((string)($_POST['email'] ?? '')),
            'password' => $_POST['password'] ?? '',
            'rol' => $_POST['rol'] ?? '',
        ];

        $validator = new Validator();
        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'unique:users:email'],
            'password' => ['required', 'string', 'max:255'],
            'rol' => ['required', 'in:admin,operador'],
        ];

        if (!$validator->validate($data, $rules)) {
            $this->flash('warning', implode('<br>', array_merge(...array_values($validator->errors()))));
            $this->redirect('/users/create');
            return;
        }

        // Crear usuario con hash.
        User::create([
            'nombre' => $data['nombre'],
            'email' => $data['email'],
            'rol' => $data['rol'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
        ]);

        $this->flash('success', 'Usuario creado');
        $this->redirect('/users');
    }

    public function edit(): void // Formulario editar usuario.
    {
        Auth::requireRole('admin');

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->flash('warning', 'Usuario no válido');
            $this->redirect('/users');
            return;
        }

        $user = User::findById($id);

        if (!$user) {
            $this->flash('warning', 'Usuario no encontrado');
            $this->redirect('/users');
            return;
        }

        $this->render('users/edit', [
            'title' => 'Editar Usuario',
            'csrf' => Csrf::token('user_edit'),
            'user' => $user,
        ]);
    }

    public function update(): void // Procesa actualización.
    {
        Auth::requireRole('admin');

        $token = $_POST['csrf_token'] ?? null;
        if (!Csrf::validate('user_edit', $token)) {
            http_response_code(419);
            echo 'CSRF inválido';
            exit;
        }
        Csrf::regenerate('user_edit');

        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'nombre' => trim((string)($_POST['nombre'] ?? '')),
            'email' => trim((string)($_POST['email'] ?? '')),
            'password' => $_POST['password'] ?? '',
            'rol' => $_POST['rol'] ?? '',
        ];

        $validator = new Validator();
        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255'],
            'rol' => ['required', 'in:admin,operador'],
        ];

        if ($data['password'] !== '') {
            $rules['password'] = ['string', 'max:255'];
        }

        if (!$validator->validate($data, $rules)) {
            $this->flash('warning', implode('<br>', array_merge(...array_values($validator->errors()))));
            $this->redirect('/users/edit?id=' . $id);
            return;
        }

        if (User::existsByEmail($data['email'], $id)) {
            $this->flash('warning', 'Ya existe otro usuario con ese email');
            $this->redirect('/users/edit?id=' . $id);
            return;
        }

        $updateFields = 'nombre = :nombre, email = :email, rol = :rol';
        $params = [
            ':nombre' => $data['nombre'],
            ':email' => $data['email'],
            ':rol' => $data['rol'],
            ':id' => $id,
        ];
        if ($data['password'] !== '') {
            $updateFields .= ', password_hash = :ph';
            $params[':ph'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        User::update($id, array_merge($data, ['password_hash' => $data['password'] !== '' ? password_hash($data['password'], PASSWORD_DEFAULT) : '']));

        $this->flash('success', 'Usuario actualizado');
        $this->redirect('/users');
    }

    public function delete(): void // Elimina un usuario.
    {
        Auth::requireRole('admin');

        $token = $_POST['csrf_token'] ?? null;
        if (!Csrf::validate('user_delete', $token)) {
            http_response_code(419);
            echo 'CSRF inválido';
            exit;
        }
        Csrf::regenerate('user_delete');

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->flash('warning', 'Usuario no válido');
            $this->redirect('/users');
            return;
        }

        if (isset($_SESSION['user']['id']) && (int)$_SESSION['user']['id'] === $id) {
            $this->flash('warning', 'No puedes eliminar tu propio usuario');
            $this->redirect('/users');
            return;
        }

        User::deleteById($id);

        $this->flash('success', 'Usuario eliminado');
        $this->redirect('/users');
    }
}