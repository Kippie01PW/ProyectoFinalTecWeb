<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Core\Conexion;
use App\Models\PreferenciasAlumnoModel; 

class PreferenciasAlumnoController {

    public function showFormulario(Request $request, Response $response, $args) {
    if ($request->getMethod() === 'POST') {
        $data = $request->getParsedBody();
    }
    
        ob_start();
        require_once APP_ROOT . '/Views/Formulario.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }
    
    public function guardarPreferencias(Request $request, Response $response, $args) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'alumno') {
        $response->getBody()->write(json_encode(['error' => 'No autenticado']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }

    $usuario_id = $_SESSION['user_id'];

    try {
        $pdo = (new \App\Core\Conexion())->getConexion();

        $stmt = $pdo->prepare("SELECT id FROM alumno WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        $alumno = $stmt->fetch();

        if (!$alumno) {
            $response->getBody()->write(json_encode([
                'error' => 'No se encontró un alumno asociado al usuario.'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $alumno_id = $alumno['id'];

        $data = $request->getParsedBody();
        $respuestas = $data['respuestas'] ?? [];

        $modelo = new \App\Models\PreferenciasAlumnoModel($pdo);
        $modelo->guardarPreferencias($alumno_id, $respuestas);

        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    } catch (\Exception $e) {
        $error = [
            'error' => 'No se pudieron guardar las respuestas',
            'detalle' => $e->getMessage()
        ];
        $response->getBody()->write(json_encode($error));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
}

}