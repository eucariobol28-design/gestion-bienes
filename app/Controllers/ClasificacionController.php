<?php
declare(strict_types=1); // Modo estricto.

namespace App\Controllers; // Namespace en Controllers.

use App\Core\Controller; // Importa clase base Controller.
use App\Core\Auth; // Importa Auth para login.
use App\Core\Csrf; // Importa CSRF.
use App\Core\Validator; // Importa Validator.
use App\Models\Categoria; // Importa modelo Categoria.
use App\Models\Ubicacion; // Importa modelo Ubicacion.

final class ClasificacionController extends Controller // Controlador final para clasificación.
{
    public function index(): void // Muestra lista de categorías y ubicaciones.
    {
        Auth::requireRole('admin'); // Solo admin puede acceder.

        $categorias = Categoria::all(); // Obtiene categorías.
        $ubicaciones = Ubicacion::all(); // Obtiene ubicaciones.

        $this->render('clasificacion/index', [ // Renderiza vista.
            'title' => 'Clasificación',
            'categorias' => $categorias,
            'ubicaciones' => $ubicaciones,
        ]);
    }

    // Métodos para Categorías
    public function createCategoria(): void // Formulario crear categoría.
    {
        Auth::requireRole('admin');
        $this->render('clasificacion/create_categoria', [
            'title' => 'Crear Categoría',
            'csrf' => Csrf::token('categoria_create'),
        ]);
    }

    public function storeCategoria(): void // Procesa creación de categoría.
    {
        Auth::requireRole('admin');

        $token = $_POST['csrf_token'] ?? null;
        if (!Csrf::validate('categoria_create', $token)) {
            http_response_code(419);
            echo 'CSRF inválido';
            exit;
        }
        Csrf::regenerate('categoria_create');

        $data = ['nombre' => trim((string)($_POST['nombre'] ?? ''))];

        $validator = new Validator();
        $rules = ['nombre' => ['required', 'string', 'max:255', 'unique:categorias:nombre']];
        if (!$validator->validate($data, $rules)) {
            $this->flash('warning', implode('<br>', array_merge(...array_values($validator->errors()))));
            $this->redirect('/clasificacion/createCategoria');
            return;
        }

        Categoria::create($data);
        $this->flash('success', 'Categoría creada');
        $this->redirect('/clasificacion');
    }

    public function editCategoria(): void // Formulario editar categoría.
    {
        Auth::requireRole('admin');

        $id = (int)($_GET['id'] ?? 0);
        $categoria = Categoria::find($id);
        if (!$categoria) {
            http_response_code(404);
            echo 'Categoría no encontrada';
            return;
        }

        $this->render('clasificacion/edit_categoria', [
            'title' => 'Editar Categoría',
            'csrf' => Csrf::token('categoria_edit'),
            'categoria' => $categoria,
        ]);
    }

    public function updateCategoria(): void // Procesa actualización.
    {
        Auth::requireRole('admin');

        $id = (int)($_POST['id'] ?? 0);
        $token = $_POST['csrf_token'] ?? null;
        if (!Csrf::validate('categoria_edit', $token)) {
            http_response_code(419);
            echo 'CSRF inválido';
            exit;
        }
        Csrf::regenerate('categoria_edit');

        $data = ['nombre' => trim((string)($_POST['nombre'] ?? ''))];

        $validator = new Validator();
        $rules = ['nombre' => ['required', 'string', 'max:255']];
        $categoria = Categoria::find($id);
        if ($data['nombre'] !== $categoria['nombre']) {
            $rules['nombre'][] = 'unique:categorias:nombre';
        }

        if (!$validator->validate($data, $rules)) {
            $this->flash('warning', implode('<br>', array_merge(...array_values($validator->errors()))));
            $this->redirect('/clasificacion/editCategoria?id=' . $id);
            return;
        }

        Categoria::update($id, $data);
        $this->flash('success', 'Categoría actualizada');
        $this->redirect('/clasificacion');
    }

    public function deleteCategoria(): void // Elimina categoría.
    {
        Auth::requireRole('admin');

        $id = (int)($_POST['id'] ?? 0);
        $token = $_POST['csrf_token'] ?? null;
        if (!Csrf::validate('categoria_delete', $token)) {
            http_response_code(419);
            echo 'CSRF inválido';
            exit;
        }
        Csrf::regenerate('categoria_delete');

        Categoria::delete($id);
        $this->flash('success', 'Categoría eliminada');
        $this->redirect('/clasificacion');
    }

    // Métodos similares para Ubicaciones (createUbicacion, storeUbicacion, etc.) - Implementar de forma análoga.
    // Para brevedad, asumir que se replican los métodos para ubicaciones con Ubicacion:: en lugar de Categoria::.
}