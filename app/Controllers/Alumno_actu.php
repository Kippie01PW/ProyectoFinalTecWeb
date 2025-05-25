<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../Core/Conexion.php';

use App\Core\Conexion;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

if (!isset($_POST['alumno_id']) || !isset($_POST['accion']) || empty($_POST['alumno_id']) || empty($_POST['accion'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$alumno_id = filter_var($_POST['alumno_id'], FILTER_VALIDATE_INT);
$accion = $_POST['accion'];
error_log("Debug - Acción recibida: " . $accion);
error_log("Debug - Alumno ID: " . $alumno_id);

if (!$alumno_id) {
    echo json_encode(['success' => false, 'message' => 'ID de alumno inválido']);
    exit;
}

try {
    $dbConnection = new Conexion();
    $conexion = $dbConnection->getConexion();
    
    $response = ['success' => false, 'message' => ''];
    
    switch ($accion) {
        case 'correo':
            if (!isset($_POST['correo']) || empty($_POST['correo'])) {
                $response['message'] = 'El correo electrónico es requerido';
                break;
            }
            
            $nuevo_correo = filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL);
            
            if (!$nuevo_correo) {
                $response['message'] = 'El formato del correo electrónico no es válido';
                break;
            }
            $stmt_check = $conexion->prepare("SELECT id FROM usuarios WHERE email = :email AND id != :alumno_id");
            $stmt_check->bindParam(':email', $nuevo_correo);
            $stmt_check->bindParam(':alumno_id', $alumno_id, PDO::PARAM_INT);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                $response['message'] = 'Este correo electrónico ya está en uso por otro usuario';
                break;
            }

            $stmt_user = $conexion->prepare("SELECT id FROM usuarios WHERE id = :alumno_id");
            $stmt_user->bindParam(':alumno_id', $alumno_id, PDO::PARAM_INT);
            $stmt_user->execute();

            if ($stmt_user->rowCount() === 0) {
                $response['message'] = 'Usuario no encontrado';
                break;
            }

            $stmt_update = $conexion->prepare("UPDATE usuarios SET email = :email WHERE id = :alumno_id");
            $stmt_update->bindParam(':email', $nuevo_correo);
            $stmt_update->bindParam(':alumno_id', $alumno_id, PDO::PARAM_INT);

            if ($stmt_update->execute()) {
                $response['success'] = true;
                $response['message'] = 'Correo electrónico actualizado correctamente';
            } else {
                $response['message'] = 'Error al actualizar el correo electrónico';
            }
            break;
            
        case 'contrasena':
            error_log("Debug - Entrando al case contrasena");
            error_log("Debug - POST contrasena recibida: " . (isset($_POST['contrasena']) ? 'SI' : 'NO'));
            if (!isset($_POST['contrasena']) || empty($_POST['contrasena'])) {
                $response['message'] = 'La contraseña es requerida';
                break;
            }
            
            $nueva_contrasena = $_POST['contrasena'];
            if (strlen($nueva_contrasena) < 6) {
                $response['message'] = 'La contraseña debe tener al menos 6 caracteres';
                break;
            }
            error_log("Debug - Contraseña válida, longitud: " . strlen($nueva_contrasena));
            
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/', $nueva_contrasena)) {
                $response['message'] = 'La contraseña debe contener al menos una mayúscula, una minúscula y un número';
                break;
            }
            
            $contrasena_encriptada = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
            error_log("Debug - Contraseña encriptada generada");
            error_log("Debug - Verificando usuario ID: " . $alumno_id);

            $stmt_user = $conexion->prepare("SELECT id FROM usuarios WHERE id = :alumno_id");
            $stmt_user->bindParam(':alumno_id', $alumno_id, PDO::PARAM_INT);
            $stmt_user->execute();

            if ($stmt_user->rowCount() === 0) {
                $response['message'] = 'Usuario no encontrado';
                break;
            }
            
            $stmt_update = $conexion->prepare("UPDATE usuarios SET password_hash = :password WHERE id = :alumno_id");
            $stmt_update->bindParam(':password', $contrasena_encriptada);
            $stmt_update->bindParam(':alumno_id', $alumno_id, PDO::PARAM_INT);
            
            if ($stmt_update->execute()) {
                $response['success'] = true;
                $response['message'] = 'Contraseña actualizada correctamente';
                error_log("Contraseña actualizada para usuario ID: $alumno_id en " . date('Y-m-d H:i:s'));
            } else {
                $response['message'] = 'Error al actualizar la contraseña';
            }
            break;
            
        default:
            $response['message'] = 'Acción no válida';
            break;
    }
 } catch (PDOException $e) {
    error_log("Error PDO en actualizar_alumno.php: " . $e->getMessage());
    $response = [
        'success' => false, 
        'message' => 'Error PDO: ' . $e->getMessage()
    ];
} catch (Exception $e) {
    error_log("Error general en actualizar_alumno.php: " . $e->getMessage());
    $response = [
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage()
    ];
}

echo json_encode($response);
?>