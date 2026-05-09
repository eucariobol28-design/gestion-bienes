<?php
declare(strict_types=1); // Modo estricto.

namespace App\Controllers; // Namespace.

use App\Core\Controller; // Controller.
use App\Core\Auth; // Auth.
use App\Models\Bien; // Bien.

final class ReportesController extends Controller // Controlador para reportes.
{
    public function index(): void // Formulario de filtros para reportes.
    {
        Auth::requireLogin(); // Requiere login (admin u operador).

        $this->render('reportes/index', [
            'title' => 'Reportes',
        ]);
    }

    public function generate(): void // Genera reporte CSV basado en filtros.
    {
        Auth::requireLogin();

        $estado = $_GET['estado'] ?? null; // Filtro estado.
        $bienes = Bien::all($estado); // Obtiene bienes filtrados.

        // Generar CSV simple.
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="reporte_bienes.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Nombre', 'Código', 'Ubicación', 'Estado', 'Responsable']); // Headers.

        foreach ($bienes as $bien) {
            fputcsv($output, [
                $bien['id'],
                $bien['nombre'],
                $bien['codigo'],
                $bien['ubicacion'],
                $bien['estado'],
                $bien['responsable'],
            ]);
        }

        fclose($output);
        exit; // Termina para descarga.
    }

    // Para PDF, necesitarías una librería como TCPDF. Por ahora, solo CSV.
}