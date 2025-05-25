<?php
namespace App\Controllers;

session_start();
header('Content-Type: application/json');


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Core\Conexion;
use App\Models\MostrarCursosModel;

class MostarCursosController {
    private $cursoModel;

    public function __construct() {
        // Usar tu conexión existente
        $conexion = new Conexion(); // Ajusta según el nombre de tu clase
        $this->cursoModel = new CursoModel($conexion->getConnection());
    }

    public function handleRequest() {
        // Verificar que el usuario esté autenticado
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Usuario no autenticado']);
            return;
        }

        $action = $_GET['action'] ?? '';

        // Obtener información del alumno
        $alumno = $this->cursoModel->getAlumnoByUsuarioId($_SESSION['user_id']);
        
        if (!$alumno) {
            http_response_code(404);
            echo json_encode(['error' => 'Alumno no encontrado']);
            return;
        }

        $alumno_id = $alumno['id'];

        switch ($action) {
            case 'asignados':
                try {
                    $cursos = $this->cursoModel->getCursosAsignados($alumno_id);
                    echo json_encode([
                        'success' => true,
                        'data' => $cursos
                    ]);
                } catch (Exception $e) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Error al obtener cursos asignados']);
                }
                break;
                
            case 'completados':
                try {
                    $cursos = $this->cursoModel->getCursosCompletados($alumno_id);
                    echo json_encode([
                        'success' => true,
                        'data' => $cursos
                    ]);
                } catch (Exception $e) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Error al obtener cursos completados']);
                }
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['error' => 'Acción no válida']);
        }
    }
}

// Manejar la petición
$controller = new CursoController();
$controller->handleRequest();
?>