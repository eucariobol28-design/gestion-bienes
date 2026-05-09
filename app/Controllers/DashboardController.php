<?php
declare(strict_types=1); // Modo estricto.

namespace App\Controllers; // Namespace en Controllers.

use App\Core\Controller; // Importa clase base Controller.
use App\Core\Auth; // Importa Auth para login.
use App\Core\Database; // Importa Database.

final class DashboardController extends Controller // Controlador final para dashboard.
{
    public function index(): void // Acción para mostrar dashboard.
    {
        Auth::requireLogin(); // Requiere login.

        $config = require __DIR__ . '/../Config/config.php'; // Carga config.
        $pdo = Database::getInstance($config['db']); // Obtiene PDO singleton.

        $total = (int)($pdo->query('SELECT COUNT(*) AS c FROM bienes')->fetch()['c'] ?? 0); // Cuenta total de bienes.
        $contar = $pdo->query('SELECT estado, COUNT(*) AS c FROM bienes GROUP BY estado')->fetchAll(); // Cuenta por estado.

        $this->render('dashboard/index', [ // Renderiza vista.
            'title' => 'Dashboard', // Título.
            'total' => $total, // Total bienes.
            'porEstado' => $contar, // Datos por estado.
        ]);
    }
}

