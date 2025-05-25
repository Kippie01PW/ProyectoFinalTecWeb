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
            
            // Obtener clases con información de alumnos
            $clases = $clasesModel->findClasesWithAlumnosByMaestro($maestro_id);
            
            // Obtener estadísticas del maestro
            $estadisticas = $clasesModel->getEstadisticasMaestro($maestro_id);

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

    /**
     * Muestra el detalle de una clase específica con sus alumnos
     * Ruta: GET /maestros/clases/{id}
     */
    public function showClaseDetalle(Request $request, Response $response, $args)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $maestro_id = $_SESSION['maestro_id'] ?? null;
        $clase_id = (int)$args['id'];

        if (!$maestro_id) {
            $response->getBody()->write('Error: No se encontró información del maestro');
            return $response->withStatus(400);
        }

        try {
            $pdo = (new Conexion())->getConexion();
            $clasesModel = new ClasesModel($pdo);
            
            // Obtener detalle de la clase con alumnos
            $clase = $clasesModel->getClaseDetalleWithAlumnos($clase_id, $maestro_id);
            
            if (!$clase) {
                $response->getBody()->write('Clase no encontrada o no tienes permisos para verla');
                return $response->withStatus(404);
            }

            // Pasar los datos a la vista
            ob_start();
            
            require_once APP_ROOT . '/Views/maestros/clase_detalle.php';
            
            $output = ob_get_clean();
            
            $response->getBody()->write($output);
            return $response;

        } catch (\Exception $e) {
            error_log("Error en ClasesController::showClaseDetalle: " . $e->getMessage());
            $response->getBody()->write('Error interno del servidor');
            return $response->withStatus(500);
        }
    }

    /**
     * API: Obtiene los alumnos de una clase específica
     * Ruta: GET /api/maestros/clases/{id}/alumnos
     */
    public function getAlumnosClase(Request $request, Response $response, $args)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $maestro_id = $_SESSION['maestro_id'] ?? null;
        $clase_id = (int)$args['id'];

        if (!$maestro_id) {
            $response->getBody()->write(json_encode(['error' => 'No autorizado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        try {
            $pdo = (new Conexion())->getConexion();
            $clasesModel = new ClasesModel($pdo);
            
            // Verificar que la clase pertenece al maestro
            $clase = $clasesModel->getClaseDetalleWithAlumnos($clase_id, $maestro_id);
            
            if (!$clase) {
                $response->getBody()->write(json_encode(['error' => 'Clase no encontrada']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'alumnos' => $clase['alumnos'],
                'total' => $clase['total_alumnos']
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            error_log("Error en ClasesController::getAlumnosClase: " . $e->getMessage());
            $response->getBody()->write(json_encode(['error' => 'Error interno del servidor']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * API: Obtiene estadísticas del maestro
     * Ruta: GET /api/maestros/estadisticas
     */
    public function getEstadisticas(Request $request, Response $response, $args)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $maestro_id = $_SESSION['maestro_id'] ?? null;

        if (!$maestro_id) {
            $response->getBody()->write(json_encode(['error' => 'No autorizado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        try {
            $pdo = (new Conexion())->getConexion();
            $clasesModel = new ClasesModel($pdo);
            
            $estadisticas = $clasesModel->getEstadisticasMaestro($maestro_id);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'estadisticas' => $estadisticas
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            error_log("Error en ClasesController::getEstadisticas: " . $e->getMessage());
            $response->getBody()->write(json_encode(['error' => 'Error interno del servidor']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}