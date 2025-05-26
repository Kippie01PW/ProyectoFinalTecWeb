<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Core\Conexion;
use App\Models\DashboardModel;

require_once __DIR__ . '/../Models/DashboardModel.php';

class DashboardController {
    private $dashboardModel;
    
    public function __construct($database) {
        $this->dashboardModel = new DashboardModel($database);
    }
    
    /**
     * Muestra el dashboard principal del alumno
     */
    public function index() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'alumno') {
            header('Location: /ProyectoFinalTecWeb/public/login');
            exit;
        }
        
        $usuario_id = $_SESSION['user_id'];
        
        // Obtener información del perfil
        $perfil = $this->dashboardModel->getPerfilAlumno($usuario_id);
        
        if (!$perfil) {
            $_SESSION['error'] = 'Error al cargar información del perfil';
            $perfil = [
                'username' => $_SESSION['username'] ?? '',
                'email' => '',
                'nombre' => ''
            ];
        }
        
        // Obtener ID del alumno para las estadísticas
        $alumno_id = $this->getAlumnoId($usuario_id);
        
        $estadisticas = [];
        if ($alumno_id) {
            $estadisticas = $this->dashboardModel->getCursosEstadisticas($alumno_id);
        }
        
        // Cargar la vista
        $data = [
            'perfil' => $perfil,
            'estadisticas' => $estadisticas
        ];
        
        $this->loadView('dashboard/alumno', $data);
    }
    
    /**
     * API endpoint para obtener estadísticas de cursos (JSON)
     */
    public function getEstadisticas() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'alumno') {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }
        
        $usuario_id = $_SESSION['user_id'];
        $alumno_id = $this->getAlumnoId($usuario_id);
        
        if (!$alumno_id) {
            http_response_code(404);
            echo json_encode(['error' => 'Alumno no encontrado']);
            return;
        }
        
        $estadisticas = $this->dashboardModel->getCursosEstadisticas($alumno_id);
        echo json_encode($estadisticas);
    }
    
    /**
     * Actualiza el perfil del usuario
     */
    public function actualizarPerfil() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'alumno') {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $usuario_id = $_SESSION['user_id'];
        
        // Validaciones
        $errores = [];
        
        if (empty($input['nombre'])) {
            $errores[] = 'El nombre es obligatorio';
        }
        
        if (empty($input['email'])) {
            $errores[] = 'El email es obligatorio';
        } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no es válido';
        } elseif ($this->dashboardModel->emailExiste($input['email'], $usuario_id)) {
            $errores[] = 'Este email ya está en uso';
        }
        
        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => implode(', ', $errores)
            ]);
            return;
        }
        
        // Actualizar perfil
        $datos = [
            'nombre' => trim($input['nombre']),
            'email' => trim($input['email'])
        ];
        
        if ($this->dashboardModel->actualizarPerfil($usuario_id, $datos)) {
            // Actualizar sesión
            $_SESSION['username'] = $datos['nombre'];
            
            echo json_encode([
                'success' => true,
                'message' => 'Perfil actualizado correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar el perfil'
            ]);
        }
    }
    
    /**
     * Actualiza la contraseña del usuario
     */
    public function actualizarPassword() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'alumno') {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $usuario_id = $_SESSION['user_id'];
        
        // Validaciones
        if (empty($input['password'])) {
            echo json_encode([
                'success' => false,
                'message' => 'La contraseña es obligatoria'
            ]);
            return;
        }
        
        if (strlen($input['password']) < 6) {
            echo json_encode([
                'success' => false,
                'message' => 'La contraseña debe tener al menos 6 caracteres'
            ]);
            return;
        }
        
        if ($input['password'] !== $input['confirm_password']) {
            echo json_encode([
                'success' => false,
                'message' => 'Las contraseñas no coinciden'
            ]);
            return;
        }
        
        // Actualizar contraseña
        if ($this->dashboardModel->actualizarPassword($usuario_id, $input['password'])) {
            echo json_encode([
                'success' => true,
                'message' => 'Contraseña actualizada correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar la contraseña'
            ]);
        }
    }
    
    /**
     * Obtiene el ID del alumno basado en el usuario_id
     */
    private function getAlumnoId($usuario_id) {
        try {
            $query = "SELECT id FROM alumno WHERE usuario_id = :usuario_id";
            $stmt = $this->dashboardModel->db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
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
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new Exception("Vista no encontrada: " . $view);
        }
    }
}