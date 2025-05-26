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
     * Muestra el formulario para editar una clase
     */
    public function showEditar(Request $request, Response $response, $args) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'maestro') {
            return $response->withHeader('Location', '/ProyectoFinalTecWeb/public/login')->withStatus(302);
        }

        $clase_id = $args['id'];
        $maestro_id = $_SESSION['maestro_id'];

        try {
            $pdo = (new Conexion())->getConexion();
            $claseModel = new ClaseModel($pdo);
            
            // Obtener datos de la clase
            $clase = $claseModel->obtenerClase($clase_id);
            if (!$clase || $clase['maestro_id'] != $maestro_id) {
                throw new \Exception('Clase no encontrada o no tienes permisos');
            }
            
            // Obtener datos para el formulario
            $alumnos_actuales = $claseModel->obtenerAlumnosPorClase($clase_id);
            $cursos_actuales = $claseModel->obtenerCursosPorClase($clase_id);
            $alumnos_disponibles = $claseModel->obtenerAlumnosDisponibles($clase_id);
            $cursos_disponibles = $claseModel->obtenerCursosDisponibles($clase_id);

            ob_start();
            require_once APP_ROOT . '/Views/clases/editar.php';
            $output = ob_get_clean();
            $response->getBody()->write($output);
            return $response;

        } catch (\Exception $e) {
            $response->getBody()->write('Error: ' . $e->getMessage());
            return $response->withStatus(500);
        }
    }

    /**
     * Procesa la actualización de una clase
     */
    public function actualizarClase(Request $request, Response $response, $args) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'maestro') {
            $response->getBody()->write(json_encode(['error' => 'No autorizado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $clase_id = $args['id'];
        $maestro_id = $_SESSION['maestro_id'];
        $data = $request->getParsedBody();

        // Debug: Log de datos recibidos
        error_log("=== DEBUG ACTUALIZAR CLASE ===");
        error_log("Clase ID: " . $clase_id);
        error_log("Maestro ID: " . $maestro_id);
        error_log("Datos recibidos: " . json_encode($data));

        try {
            $pdo = (new Conexion())->getConexion();
            $claseModel = new ClaseModel($pdo);

            // Verificar permisos
            $clase = $claseModel->obtenerClase($clase_id);
            if (!$clase) {
                throw new \Exception('Clase no encontrada con ID: ' . $clase_id);
            }
            
            if ($clase['maestro_id'] != $maestro_id) {
                throw new \Exception('No tienes permisos para editar esta clase');  
            }

            error_log("Clase encontrada: " . json_encode($clase));

            $resultado = [
                'success' => true,
                'message' => 'Clase actualizada correctamente',
                'cursos_agregados' => 0,
                'cursos_eliminados' => 0,
                'alumnos_agregados' => 0,
                'alumnos_eliminados' => 0,
                'info_actualizada' => false
            ];

            // Actualizar datos básicos
            if (isset($data['nombre']) && !empty(trim($data['nombre']))) {
                error_log("Actualizando nombre a: " . $data['nombre']);
                $claseModel->actualizarClase($clase_id, trim($data['nombre']), $data['descripcion'] ?? null);
                $resultado['info_actualizada'] = true;
            }

            // Procesar cursos a agregar
            if (isset($data['agregar_cursos']) && is_array($data['agregar_cursos']) && !empty($data['agregar_cursos'])) {
                error_log("Agregando cursos: " . json_encode($data['agregar_cursos']));
                $claseModel->agregarCursosAClase($clase_id, $data['agregar_cursos']);
                $resultado['cursos_agregados'] = count($data['agregar_cursos']);
            }
            
            // Procesar cursos a eliminar
            if (isset($data['eliminar_cursos']) && is_array($data['eliminar_cursos']) && !empty($data['eliminar_cursos'])) {
                error_log("Eliminando cursos: " . json_encode($data['eliminar_cursos']));
                $claseModel->eliminarCursosDeClase($clase_id, $data['eliminar_cursos']);
                $resultado['cursos_eliminados'] = count($data['eliminar_cursos']);
            }

            // Procesar alumnos a agregar
            if (isset($data['agregar_alumnos']) && is_array($data['agregar_alumnos']) && !empty($data['agregar_alumnos'])) {
                error_log("Agregando alumnos: " . json_encode($data['agregar_alumnos']));
                $claseModel->agregarAlumnosAClase($clase_id, $data['agregar_alumnos']);
                $resultado['alumnos_agregados'] = count($data['agregar_alumnos']);
            }
            
            // Procesar alumnos a eliminar
            if (isset($data['eliminar_alumnos']) && is_array($data['eliminar_alumnos']) && !empty($data['eliminar_alumnos'])) {
                error_log("Eliminando alumnos: " . json_encode($data['eliminar_alumnos']));
                $claseModel->eliminarAlumnosDeClase($clase_id, $data['eliminar_alumnos']);
                $resultado['alumnos_eliminados'] = count($data['eliminar_alumnos']);
            }

            error_log("Resultado final: " . json_encode($resultado));

            $response->getBody()->write(json_encode($resultado));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            error_log("ERROR en actualizarClase: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            $response->getBody()->write(json_encode([
                'error' => 'Error al actualizar la clase',
                'message' => $e->getMessage(),
                'clase_id' => $clase_id,
                'debug_info' => [
                    'session_maestro_id' => $_SESSION['maestro_id'] ?? 'no_set',
                    'clase_maestro_id' => isset($clase) ? $clase['maestro_id'] : 'clase_not_found'
                ]
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * API para obtener detalles completos de una clase
     */
    public function obtenerDetalles(Request $request, Response $response, $args) {
        $clase_id = $args['id'];
        
        // Debug: Log del ID recibido
        error_log("Obteniendo detalles para clase ID: " . $clase_id);
        
        try {
            $pdo = (new Conexion())->getConexion();
            $claseModel = new ClaseModel($pdo);
            
            // Verificar que la clase existe
            $clase = $claseModel->obtenerClase($clase_id);
            if (!$clase) {
                throw new \Exception("Clase no encontrada con ID: " . $clase_id);
            }
            
            // Obtener datos paso a paso con manejo de errores
            $alumnos = [];
            $cursos = [];
            $estadisticas = [
                'total_alumnos' => 0,
                'total_cursos' => 0,
                'total_asignaciones' => 0,
                'cursos_completados' => 0
            ];
            
            try {
                $alumnos = $claseModel->obtenerAlumnosPorClase($clase_id);
            } catch (\Exception $e) {
                error_log("Error obteniendo alumnos: " . $e->getMessage());
            }
            
            try {
                $cursos = $claseModel->obtenerCursosPorClase($clase_id);
            } catch (\Exception $e) {
                error_log("Error obteniendo cursos: " . $e->getMessage());
            }
            
            try {
                $estadisticas = $claseModel->obtenerEstadisticasClase($clase_id);
            } catch (\Exception $e) {
                error_log("Error obteniendo estadísticas: " . $e->getMessage());
            }
            
            $resultado = [
                'clase' => $clase,
                'alumnos' => $alumnos,
                'cursos' => $cursos,  
                'estadisticas' => $estadisticas
            ];
            
            error_log("Datos obtenidos correctamente: " . json_encode($resultado));
            
            $response->getBody()->write(json_encode($resultado));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            
        } catch (\Exception $e) {
            error_log("Error en obtenerDetalles: " . $e->getMessage());
            
            $response->getBody()->write(json_encode([
                'error' => 'Error al obtener detalles de la clase',
                'message' => $e->getMessage(),
                'clase_id' => $clase_id
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