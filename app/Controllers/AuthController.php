<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\UsuarioModel;
use App\Models\AlumnoModelRegister;
use App\Models\MaestroModel;

class AuthController {


    public function showRegisterForm(Request $request, Response $response, $args)
    {
        ob_start();
        
        require_once APP_ROOT . '/Views/auth/register.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }


    public function processRegistration(Request $request, Response $response, $args)
    {
        
        $data = $request->getParsedBody();

        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $role = $data['role'] ?? '';

  
        if (empty($username) || empty($email) || empty($password) || empty($role)) {
            $responseData = ['success' => false, 'message' => 'Todos los campos son obligatorios.'];
            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $usuarioModel = new UsuarioModel();

        try {
            
            $usuarioId = $usuarioModel->createUser($username, $email, $passwordHash, $role);

            if ($usuarioId) {
                $profileCreated = false;
                
                if ($role === 'alumno') {
                    $alumnoModel = new AlumnoModelRegister();
                    $profileCreated = $alumnoModel->createAlumno($usuarioId, $username);
                } elseif ($role === 'maestro') {
                    $maestroModel = new MaestroModel();
                    $profileCreated = $maestroModel->createMaestro($usuarioId, $username);
                }

                if ($profileCreated) {
                    $responseData = ['success' => true, 'message' => '¡Registro exitoso!'];
                    $status = 201; 
                } else {
                     $responseData = ['success' => false, 'message' => 'Error al crear el perfil específico.'];
                     $status = 500;
                }

            } else {
                $responseData = ['success' => false, 'message' => 'Error al registrar el usuario. ¿Email o usuario ya existen?'];
                $status = 409; 
            }

        } catch (\PDOException $e) {
             $responseData = ['success' => false, 'message' => 'Error de base de datos.'];
             $status = 500;
             error_log("PDOException en Registro: " . $e->getMessage()); 
        } catch (\Exception $e) {
             $responseData = ['success' => false, 'message' => 'Error inesperado.'];
             $status = 500;
             error_log("Exception en Registro: " . $e->getMessage()); 
        }

        
        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    
    public function showLoginForm(Request $request, Response $response, $args)
    {
        ob_start();
        require_once APP_ROOT . '/Views/auth/login.php'; 
        $output = ob_get_clean();
        $response->getBody()->write($output);
        return $response;
    }

    
    public function processLogin(Request $request, Response $response, $args)
    {
        
        if (session_status() == PHP_SESSION_NONE) { session_start(); }

        $data = $request->getParsedBody();
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        
        if (empty($email) || empty($password)) {
            $responseData = ['success' => false, 'message' => 'Correo y contraseña son obligatorios.'];
            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $usuarioModel = new \App\Models\UsuarioModel();
        $usuario = $usuarioModel->getUserByEmail($email);

        
        if ($usuario && password_verify($password, $usuario['password_hash'])) {
           
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['username'] = $usuario['username'];
            $_SESSION['role'] = $usuario['role'];

           
            $pdo = (new \App\Core\Conexion())->getConexion(); 
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
            $redirectUrl = ""; 
            if ($usuario['role'] === 'alumno') {
                $redirectUrl = "/ProyectoFinalTecWeb/public/alumnos/cursos"; 
            } elseif ($usuario['role'] === 'maestro') {
                $redirectUrl = "/ProyectoFinalTecWeb/public/maestros/dashboard"; 
            }

    $responseData = ['success' => true, 'redirect' => $redirectUrl];
    $status = 200;

        } else {
            
            $responseData = ['success' => false, 'message' => 'Credenciales incorrectas.'];
            $status = 401; 
        }

        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
    
    public function logout(Request $request, Response $response, $args)
    {
        
        if (session_status() == PHP_SESSION_NONE) { 
            session_start(); 
        }
        
        session_unset(); 
        session_destroy(); 

        
        $baseUrl = '/ProyectoFinalTecWeb/public/';
        
        
        return $response
                ->withHeader('Location', $baseUrl) 
                ->withStatus(302);
    }
}
?>