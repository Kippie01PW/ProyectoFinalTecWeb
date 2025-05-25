<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\UsuarioModel;
use App\Models\AlumnoModelRegister;
use App\Models\MaestroModel;

class AuthController {

    /**
     * Muestra el formulario de registro.
     * Llamado por la ruta: GET /register
     */
    public function showRegisterForm(Request $request, Response $response, $args)
    {
        ob_start();
        // APP_ROOT debería estar definido por public/index.php
        require_once APP_ROOT . '/Views/auth/register.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }

    /**
     * Procesa los datos del formulario de registro.
     * Llamado por la ruta: POST /api/auth/register
     */
    public function processRegistration(Request $request, Response $response, $args)
    {
        // 1. Obtenemos los datos (Slim los parsea automáticamente)
        $data = $request->getParsedBody();

        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $role = $data['role'] ?? '';

        // 2. Validación
        if (empty($username) || empty($email) || empty($password) || empty($role)) {
            $responseData = ['success' => false, 'message' => 'Todos los campos son obligatorios.'];
            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $usuarioModel = new UsuarioModel();

        try {
            // 3. Crear Usuario
            $usuarioId = $usuarioModel->createUser($username, $email, $passwordHash, $role);

            if ($usuarioId) {
                $profileCreated = false;
                // 4. Crear Perfil (Alumno o Maestro)
                if ($role === 'alumno') {
                    $alumnoModel = new AlumnoModelRegister();
                    $profileCreated = $alumnoModel->createAlumno($usuarioId, $username);
                } elseif ($role === 'maestro') {
                    $maestroModel = new MaestroModel();
                    $profileCreated = $maestroModel->createMaestro($usuarioId, $username);
                }

                if ($profileCreated) {
                    $responseData = ['success' => true, 'message' => '¡Registro exitoso!'];
                    $status = 201; // 201 Created
                } else {
                     $responseData = ['success' => false, 'message' => 'Error al crear el perfil específico.'];
                     $status = 500;
                }

            } else {
                $responseData = ['success' => false, 'message' => 'Error al registrar el usuario. ¿Email o usuario ya existen?'];
                $status = 409; // 409 Conflict (o 400 Bad Request)
            }

        } catch (\PDOException $e) {
             $responseData = ['success' => false, 'message' => 'Error de base de datos.'];
             $status = 500;
             error_log("PDOException en Registro: " . $e->getMessage()); // Log real del error
        } catch (\Exception $e) {
             $responseData = ['success' => false, 'message' => 'Error inesperado.'];
             $status = 500;
             error_log("Exception en Registro: " . $e->getMessage()); // Log real del error
        }

        // 5. Devolver respuesta JSON
        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    /**
     * Muestra el formulario de login.
     * Ruta: GET /login
     */
    public function showLoginForm(Request $request, Response $response, $args)
    {
        ob_start();
        require_once APP_ROOT . '/Views/auth/login.php'; // Carga la vista
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }

    /**
     * Procesa los datos del formulario de login.
     * Ruta: POST /api/auth/login
     */
    public function processLogin(Request $request, Response $response, $args)
    {
        // Aseguramos que la sesión esté iniciada
        if (session_status() == PHP_SESSION_NONE) { session_start(); }

        $data = $request->getParsedBody();
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        // Validación
        if (empty($email) || empty($password)) {
            $responseData = ['success' => false, 'message' => 'Correo y contraseña son obligatorios.'];
            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $usuarioModel = new \App\Models\UsuarioModel();
        $usuario = $usuarioModel->getUserByEmail($email);

        // Verificación
        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            // ¡Éxito! Iniciar sesión y guardar datos en $_SESSION
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['username'] = $usuario['username'];
            $_SESSION['role'] = $usuario['role'];

            // MUY IMPORTANTE: Necesitamos el ID de Alumno/Maestro para nuestras APIs
            $pdo = (new \App\Core\Conexion())->getConexion(); // Obtenemos conexión
            if ($usuario['role'] === 'alumno') {
                $stmt = $pdo->prepare("SELECT id FROM alumno WHERE usuario_id = :id");
                $stmt->execute([':id' => $usuario['id']]);
                $perfil = $stmt->fetch(\PDO::FETCH_ASSOC);
                $_SESSION['alumno_id'] = $perfil ? $perfil['id'] : null;
            } elseif ($usuario['role'] === 'maestro') {
                $stmt = $pdo->prepare("SELECT id FROM maestro WHERE usuario_id = :id");
                $stmt->execute([':id' => $usuario['id']]);
                $perfil = $stmt->fetch(\PDO::FETCH_ASSOC);
                $_SESSION['maestro_id'] = $perfil ? $perfil['id'] : null;
            }

            $rolePath = ($usuario['role'] === 'alumno') ? 'alumnos' : 'maestros'; 
            $redirectUrl = "/ProyectoFinalTecWeb/public/{$rolePath}/dashboard"; 

            $responseData = ['success' => true, 'redirect' => $redirectUrl];
            $status = 200; // 200 OK

        } else {
            // Credenciales incorrectas
            $responseData = ['success' => false, 'message' => 'Credenciales incorrectas.'];
            $status = 401; // 401 Unauthorized
        }

        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
    
    public function logout(Request $request, Response $response, $args)
    {
        // Aseguramos que la sesión esté activa para poder destruirla
        if (session_status() == PHP_SESSION_NONE) { 
            session_start(); 
        }
        
        session_unset(); // Libera todas las variables de sesión
        session_destroy(); // Destruye toda la información de la sesión

        // Construimos la URL base para redirigir
        $baseUrl = '/ProyectoFinalTecWeb/public/';
        
        // Redirige a la página principal (o al login si prefieres)
        return $response
                ->withHeader('Location', $baseUrl) // Redirigimos a la raíz
                ->withStatus(302);
    }
}
?>