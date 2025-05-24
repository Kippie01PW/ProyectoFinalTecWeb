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

    // Aquí añadiremos showLoginForm y processLogin más adelante...
}
?>