<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Auth;
use App\Models\Categoria;
use App\Models\Ubicacion;
use App\Models\Bien;

final class BienesController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();

        $estado = $_GET['estado'] ?? null;
        $bienes = Bien::all($estado);

        $this->render('bienes/index', [
            'title' => 'Bienes',
            'bienes' => $bienes,
            'estado' => $estado,
            'csrf' => Csrf::token('bien_delete'),
        ]);
    }

    public function create(): void
    {
        Auth::requireLogin();
        $this->render('bienes/create', [
            'title' => 'Registrar Bien',
            'csrf' => Csrf::token('bien_create'),
            'categorias' => Categoria::all(),
            'ubicaciones' => Ubicacion::all(),
            'bien' => ['nombre' => '', 'codigo' => '', 'ubicacion' => '', 'categoria' => '', 'estado' => '', 'responsable' => ''],
        ]);
    }

    public function store(): void
    {
        Auth::requireLogin();

        $token = $_POST['csrf_token'] ?? null;
        if (!Csrf::validate('bien_create', $token)) {
            http_response_code(419);
            echo 'CSRF inválido';
            exit;
        }
        Csrf::regenerate('bien_create');

        $data = [
            'nombre' => trim((string)($_POST['nombre'] ?? '')),
            'codigo' => trim((string)($_POST['codigo'] ?? '')),
            'ubicacion' => trim((string)($_POST['ubicacion'] ?? '')),
            'categoria' => trim((string)($_POST['categoria'] ?? '')),
            'estado' => trim((string)($_POST['estado'] ?? '')),
            'responsable' => trim((string)($_POST['responsable'] ?? '')),
        ];

        foreach (['nombre','codigo','ubicacion','categoria','estado','responsable'] as $f) {
            if ($data[$f] === '') {
                $this->flash('warning', 'Todos los campos son requeridos');
                $this->redirect('/bienes/create');
                return;
            }
        }

        Bien::create($data);
        $this->flash('success', 'Bien registrado correctamente');
        $this->redirect('/bienes');
    }

    public function edit(): void
    {
        Auth::requireLogin();

        $id = (int)($_GET['id'] ?? 0);
        $bien = Bien::find($id);
        if (!$bien) {
            http_response_code(404);
            echo 'Bien no encontrado';
            return;
        }

        $this->render('bienes/edit', [
            'title' => 'Editar Bien',
            'csrf' => Csrf::token('bien_edit'),
            'categorias' => Categoria::all(),
            'ubicaciones' => Ubicacion::all(),
            'bien' => $bien,
        ]);
    }

    public function update(): void
    {
        Auth::requireLogin();

        $id = (int)($_POST['id'] ?? 0);
        $token = $_POST['csrf_token'] ?? null;
        if (!Csrf::validate('bien_edit', $token)) {
            http_response_code(419);
            echo 'CSRF inválido';
            exit;
        }
        Csrf::regenerate('bien_edit');

        $data = [
            'nombre' => trim((string)($_POST['nombre'] ?? '')),
            'codigo' => trim((string)($_POST['codigo'] ?? '')),
            'ubicacion' => trim((string)($_POST['ubicacion'] ?? '')),
            'categoria' => trim((string)($_POST['categoria'] ?? '')),
            'estado' => trim((string)($_POST['estado'] ?? '')),
            'responsable' => trim((string)($_POST['responsable'] ?? '')),
        ];

        foreach (['nombre','codigo','ubicacion','categoria','estado','responsable'] as $f) {
            if ($data[$f] === '') {
                $this->flash('warning', 'Todos los campos son requeridos');
                $this->redirect('/bienes/edit?id=' . $id);
                return;
            }
        }

        Bien::update($id, $data);
        $this->flash('success', 'Bien actualizado');
        $this->redirect('/bienes');
    }

    public function delete(): void
    {
        Auth::requireLogin();

        $id = (int)($_POST['id'] ?? 0);
        $token = $_POST['csrf_token'] ?? null;
        if (!Csrf::validate('bien_delete', $token)) {
            http_response_code(419);
            echo 'CSRF inválido';
            exit;
        }
        Csrf::regenerate('bien_delete');

        Bien::delete($id);
        $this->flash('success', 'Bien eliminado');
        $this->redirect('/bienes');
    }
}

