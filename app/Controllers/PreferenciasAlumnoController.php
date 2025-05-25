<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Core\Conexion;
use App\Models\PreferenciasAlumnoModel; // Import faltante agregado

class PreferenciasAlumnoController {

    public function showFormulario(Request $request, Response $response, $args) {
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
        
        // Solo usar el ID del alumno logueado
        if (!isset($_SESSION['alumno_id'])) {
            $response->getBody()->write(json_encode(['error' => 'No autenticado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
        
        $alumno_id = $_SESSION['alumno_id'];
        $data = $request->getParsedBody();
        $respuestas = $data['respuestas'] ?? [];
        
        try {
            $pdo = (new Conexion())->getConexion();
            $modelo = new PreferenciasAlumnoModel($pdo);
            
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