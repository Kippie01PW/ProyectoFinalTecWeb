<?php
namespace App\Controllers; 

use App\Models\UsuarioModel;
use App\Models\AlumnoModel;
use App\Models\MaestroModel;

class AuthController {
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $role = $_POST['role'];

            if (empty($username) || empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
                exit;
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $usuarioModel = new UsuarioModel();
            $usuarioId = $usuarioModel->createUser($username, $email, $passwordHash, $role);

            if ($usuarioId) {
                if ($role === 'alumno') {
                    $alumnoModel = new AlumnoModel();
                    $alumnoModel->createAlumno($usuarioId, $username);
                } else {
                    $maestroModel = new MaestroModel();
                    $maestroModel->createMaestro($usuarioId, $username);
                }

                echo json_encode(['success' => true, 'message' => '¡Registro exitoso!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al registrar.']);
            }
        } else{
            include __DIR__ . '/../Views/auth/register.php'; 
        }
    }
}
?>