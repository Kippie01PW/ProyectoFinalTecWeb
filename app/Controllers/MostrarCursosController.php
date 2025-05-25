<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Core\Conexion;
use App\Models\MostrarCursosModel;

class MostrarCursosController {
    private $cursoModel;

    public function __construct() {
        // Usar tu conexión existente
        $conexion = new Conexion();
        $this->cursoModel = new MostrarCursosModel($conexion->getConnection());
    }

    public function getCursosAsignados(Request $request, Response $response, $args) {
        // Verificar que el usuario esté autenticado
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $payload = json_encode(['error' => 'Usuario no autenticado']);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        // Obtener información del alumno
        $alumno = $this->cursoModel->getAlumnoByUsuarioId($_SESSION['user_id']);
        
        if (!$alumno) {
            $payload = json_encode(['error' => 'Alumno no encontrado']);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $alumno_id = $alumno['id'];

        try {
            $cursos = $this->cursoModel->getCursosAsignados($alumno_id);
            $payload = json_encode([
                'success' => true,
                'data' => $cursos
            ]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $payload = json_encode(['error' => 'Error al obtener cursos asignados']);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getCursosCompletados(Request $request, Response $response, $args) {
        // Verificar que el usuario esté autenticado
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $payload = json_encode(['error' => 'Usuario no autenticado']);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        // Obtener información del alumno
        $alumno = $this->cursoModel->getAlumnoByUsuarioId($_SESSION['user_id']);
        
        if (!$alumno) {
            $payload = json_encode(['error' => 'Alumno no encontrado']);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $alumno_id = $alumno['id'];

        try {
            $cursos = $this->cursoModel->getCursosCompletados($alumno_id);
            $payload = json_encode([
                'success' => true,
                'data' => $cursos
            ]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $payload = json_encode(['error' => 'Error al obtener cursos completados']);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
?>