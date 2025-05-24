<?php
namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\AlumnoModelRegister;
use App\Models\MaestroModel;

class AuthController {
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $role = $_POST['role'];

            if (empty($username) || empty($email) || empty($password)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
                exit;
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $usuarioModel = new UsuarioModel();
            $usuarioId = $usuarioModel->createUser($username, $email, $passwordHash, $role);

            if ($usuarioId) {
                if ($role === 'alumno') {
                    $alumnoModel = new AlumnoModelRegister();
                    $alumnoModel->createAlumno($usuarioId, $username);
                } else {
                    $maestroModel = new MaestroModel();
                    $maestroModel->createMaestro($usuarioId, $username);
                }
                session_start();
                $_SESSION['user_id'] = $usuarioId;
                $_SESSION['role'] = $role;

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'redirect' => "/PROYECTOFINALTECWEB/{$role}/dashboard"
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Error al registrar.']);
            }
            exit;
        } else {
            include __DIR__ . '/../Views/auth/register.php';
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            if (empty($email) || empty($password)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Correo y contraseña son obligatorios.']);
                exit;
            }

            $usuarioModel = new UsuarioModel();
            $usuario = $usuarioModel->getUserByEmail($email);

            if ($usuario && password_verify($password, $usuario['password_hash'])) {
                session_start();
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['role'] = $usuario['role'];

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'redirect' => "/PROYECTOFINALTECWEB/{$usuario['role']}/dashboard"
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas.']);
            }
            exit;
        } else {
            include __DIR__ . '/../Views/auth/login.php';
        }
    }
}
?>