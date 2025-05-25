<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Core\Conexion;
use App\Models\ClaseModel;

class ClaseController {

    /**
     * Muestra la vista principal de clases (formulario + lista)
     */
    public function showClases(Request $request, Response $response, $args) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'maestro') {
            return $response->withHeader('Location', '/ProyectoFinalTecWeb/public/login')->withStatus(302);
        }

        $maestro_id = $_SESSION['maestro_id'];

        try {
            $pdo = (new Conexion())->getConexion();
            $claseModel = new ClaseModel($pdo);
            $clases = $claseModel->obtenerClasesPorMaestro($maestro_id);
            
            // Obtener cursos y alumnos para el formulario
            $cursos = $claseModel->obtenerTodosLosCursos();
            $alumnos = $claseModel->obtenerTodosLosAlumnos();

            ob_start();
            require_once APP_ROOT . '/Views/clases/index.php';
            $output = ob_get_clean();
            $response->getBody()->write($output);
            return $response;

        } catch (\Exception $e) {
            $response->getBody()->write('Error al cargar las clases: ' . $e->getMessage());
            return $response->withStatus(500);
        }
    }

    /**
     * API para obtener estadísticas de una clase
     */
    public function obtenerEstadisticas(Request $request, Response $response, $args) {
        $clase_id = $args['id'];
        
        try {
            $pdo = (new Conexion())->getConexion();
            $claseModel = new ClaseModel($pdo);
            $estadisticas = $claseModel->obtenerEstadisticasClase($clase_id);
            
            $response->getBody()->write(json_encode($estadisticas));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Error al obtener estadísticas',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Crea una nueva clase
     */
    public function crearClase(Request $request, Response $response, $args) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'maestro') {
            $response->getBody()->write(json_encode(['error' => 'No autorizado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $data = $request->getParsedBody();
        $maestro_id = $_SESSION['maestro_id'];
        
        // Obtener arrays de cursos y alumnos seleccionados
        $cursos = isset($data['cursos']) ? $data['cursos'] : [];
        $alumnos = isset($data['alumnos']) ? $data['alumnos'] : [];

        try {
            $pdo = (new Conexion())->getConexion();
            $claseModel = new ClaseModel($pdo);

            $clase_id = $claseModel->crearClase(
                $maestro_id,
                $data['nombre'],
                $data['descripcion'] ?? null,
                $cursos,
                $alumnos
            );

            $response->getBody()->write(json_encode([
                'success' => true,
                'clase_id' => $clase_id,
                'codigo' => $claseModel->obtenerCodigoClase($clase_id),
                'cursos_asignados' => count($cursos),
                'alumnos_asignados' => count($alumnos),
                'relaciones_creadas' => count($cursos) * count($alumnos) // Total de relaciones alumno-curso
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'No se pudo crear la clase',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Permite a un alumno unirse a una clase usando código
     */
    public function unirseClase(Request $request, Response $response, $args) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'alumno') {
            $response->getBody()->write(json_encode(['error' => 'No autorizado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $data = $request->getParsedBody();
        $alumno_id = $_SESSION['alumno_id'];
        $codigo = $data['codigo'] ?? '';

        try {
            $pdo = (new Conexion())->getConexion();
            $claseModel = new ClaseModel($pdo);

            $clase_id = $claseModel->obtenerClasePorCodigo($codigo);
            
            if (!$clase_id) {
                $response->getBody()->write(json_encode(['error' => 'Código de clase no válido']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $resultado = $claseModel->inscribirAlumno($alumno_id, $clase_id);

            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Te has unido a la clase']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'No se pudo unir a la clase',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Muestra formulario para unirse a una clase (para alumnos)
     */
    public function showUnirse(Request $request, Response $response, $args) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'alumno') {
            return $response->withHeader('Location', '/ProyectoFinalTecWeb/public/login')->withStatus(302);
        }

        ob_start();
        require_once APP_ROOT . '/Views/clases/unirse.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }
}