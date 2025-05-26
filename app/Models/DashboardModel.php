<?php
namespace App\Models;
use App\Core\Conexion;

class DashboardModel {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Obtiene estadísticas de cursos para un alumno
     */
    public function getCursosEstadisticas($alumno_id) {
        try {
            $query = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN estado = 'asignado' THEN 1 ELSE 0 END) as asignados,
                        SUM(CASE WHEN estado = 'completado' THEN 1 ELSE 0 END) as completados
                      FROM alumnocurso 
                      WHERE alumno_id = :alumno_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':alumno_id', $alumno_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'total' => (int)$resultado['total'],
                'asignados' => (int)$resultado['asignados'],
                'completados' => (int)$resultado['completados']
            ];
            
        } catch (PDOException $e) {
            error_log("Error en getCursosEstadisticas: " . $e->getMessage());
            return [
                'total' => 0,
                'asignados' => 0,
                'completados' => 0
            ];
        }
    }
    
    /**
     * Obtiene información del perfil del alumno
     */
    public function getPerfilAlumno($usuario_id) {
        try {
            $query = "SELECT u.username, u.email, a.nombre 
                      FROM usuarios u 
                      INNER JOIN alumno a ON u.id = a.usuario_id 
                      WHERE u.id = :usuario_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en getPerfilAlumno: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualiza el perfil del usuario
     */
    public function actualizarPerfil($usuario_id, $datos) {
        try {
            $this->db->beginTransaction();
            
            // Actualizar tabla usuarios
            if (!empty($datos['email'])) {
                $query = "UPDATE usuarios SET email = :email WHERE id = :usuario_id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':email', $datos['email']);
                $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmt->execute();
            }
            
            // Actualizar tabla alumno
            if (!empty($datos['nombre'])) {
                $query = "UPDATE alumno SET nombre = :nombre WHERE usuario_id = :usuario_id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':nombre', $datos['nombre']);
                $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmt->execute();
            }
            
            $this->db->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->db->rollback();
            error_log("Error en actualizarPerfil: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualiza la contraseña del usuario
     */
    public function actualizarPassword($usuario_id, $nueva_password) {
        try {
            $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
            
            $query = "UPDATE usuarios SET password_hash = :password_hash WHERE id = :usuario_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':password_hash', $password_hash);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Error en actualizarPassword: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica si el email ya existe (para validación)
     */
    public function emailExiste($email, $usuario_id_excluir = null) {
        try {
            $query = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
            $params = [':email' => $email];
            
            if ($usuario_id_excluir) {
                $query .= " AND id != :usuario_id";
                $params[':usuario_id'] = $usuario_id_excluir;
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchColumn() > 0;
            
        } catch (PDOException $e) {
            error_log("Error en emailExiste: " . $e->getMessage());
            return true; // Por seguridad, asumir que existe
        }
    }
}