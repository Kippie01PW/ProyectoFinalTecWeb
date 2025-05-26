<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Core\Conexion;
use App\Models\DashboardModel;
use \PDO;
use \Exception;

// require_once __DIR__ . '/../Models/DashboardModel.php'; // Esta línea ya no es necesaria si usas Composer autoload

class DashboardController {
    private $dashboardModel;
    private $db; // Añadimos una propiedad para la conexión PDO si la necesitamos directamente

    // Modificamos el constructor para que también cree la conexión
    public function __construct() { //
        $conexion = new Conexion(); // Creamos una nueva instancia de Conexion
        $this->db = $conexion->getConexion(); // Obtenemos la conexión PDO real
        $this->dashboardModel = new DashboardModel($this->db); // Pasamos la conexión al modelo
    }
    
    /**
     * Muestra el dashboard principal del alumno con estadísticas
     */
    public function index(Request $request, Response $response, $args) {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'alumno') {
            return $response->withHeader('Location', '/ProyectoFinalTecWeb/public/login')->withStatus(302);
        }
        
        $usuario_id = $_SESSION['user_id'];
        
        $nombreUsuario = $_SESSION['username'] ?? 'Alumno';

        // Obtener ID del alumno para las estadísticas
        $alumno_id = $this->getAlumnoId($usuario_id);
        
        $estadisticas = [];
        if ($alumno_id) {
            $estadisticas = $this->dashboardModel->getCursosEstadisticas($alumno_id);
        }
        
        $data = [
            'perfil' => ['nombre' => $nombreUsuario],
            'estadisticas' => $estadisticas
        ];
        
        ob_start();
        $this->loadView('dashboard/alumno', $data);
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }
    
    /**
     * API endpoint para obtener estadísticas de cursos (JSON)
     */
    public function getEstadisticas(Request $request, Response $response, $args) {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'alumno') {
            $response->getBody()->write(json_encode(['error' => 'No autorizado']));
            return $response->withStatus(401);
        }
        
        $usuario_id = $_SESSION['user_id'];
        $alumno_id = $this->getAlumnoId($usuario_id);
        
        if (!$alumno_id) {
            $response->getBody()->write(json_encode(['error' => 'Alumno no encontrado']));
            return $response->withStatus(404);
        }
        
        $estadisticas = $this->dashboardModel->getCursosEstadisticas($alumno_id);
        $response->getBody()->write(json_encode($estadisticas));
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    /**
     * Obtiene el ID del alumno basado en el usuario_id
     */
    private function getAlumnoId($usuario_id) {
        try {
            // Usamos la conexión que ya creamos en el constructor del controlador
            $db = $this->db; 
            $query = "SELECT id FROM alumno WHERE usuario_id = :usuario_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id, \PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ? $result['id'] : null;
            
        } catch (Exception $e) {
            error_log("Error en getAlumnoId: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Carga una vista con datos
     */
    private function loadView($view, $data = []) {
        extract($data);
        $viewPath = APP_ROOT . '/Views/' . $view . '.php'; 
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new Exception("Vista no encontrada: " . $view);
        }
    }
}