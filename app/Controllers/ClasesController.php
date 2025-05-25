<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Core\Conexion;
use App\Models\ClasesModel;

class ClasesController
{
    /**
     * Muestra la página con las clases del maestro
     * Ruta: GET /maestros/clases
     */
    public function showClasesPage(Request $request, Response $response, $args)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $maestro_id = $_SESSION['maestro_id'] ?? null;

        if (!$maestro_id) {
            // Si no hay maestro_id en sesión, redirigir o mostrar error
            $response->getBody()->write('Error: No se encontró información del maestro');
            return $response->withStatus(400);
        }

        try {
            $pdo = (new Conexion())->getConexion();
            $clasesModel = new ClasesModel($pdo);
            $clases = $clasesModel->findClasesByMaestro($maestro_id);

            // Pasar los datos a la vista
            ob_start();
            
            require_once APP_ROOT . '/Views/maestros/clases.php';
            
            $output = ob_get_clean();
            
            $response->getBody()->write($output);
            return $response;

        } catch (\Exception $e) {
            error_log("Error en ClasesController::showClasesPage: " . $e->getMessage());
            $response->getBody()->write('Error interno del servidor');
            return $response->withStatus(500);
        }
    }
}