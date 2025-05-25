<?php
namespace App\Models;
use App\Core\Conexion;


class MostarCursosModel {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    // Obtener cursos asignados para un alumno
    public function getCursosAsignados($alumno_id) {
        $query = "SELECT 
                    ac.id as asignacion_id,
                    c.id as curso_id,
                    c.titulo,
                    c.descripcion,
                    c.enlace_externo,
                    cat.nombre as categoria,
                    cl.nombre as clase_nombre,
                    ac.fecha_asignacion
                  FROM alumnocurso ac
                  INNER JOIN cursos c ON ac.curso_id = c.id
                  INNER JOIN clases cl ON ac.clase_id = cl.id
                  LEFT JOIN categoriascurso cat ON c.categoria_id = cat.id
                  WHERE ac.alumno_id = ? 
                  AND ac.estado = 'asignado'
                  ORDER BY ac.fecha_asignacion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $alumno_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Obtener cursos completados para un alumno
    public function getCursosCompletados($alumno_id) {
        $query = "SELECT 
                    ac.id as asignacion_id,
                    c.id as curso_id,
                    c.titulo,
                    c.descripcion,
                    cat.nombre as categoria,
                    cl.nombre as clase_nombre,
                    ac.evidencia,
                    ac.fecha_completado,
                    ac.fecha_asignacion
                  FROM alumnocurso ac
                  INNER JOIN cursos c ON ac.curso_id = c.id
                  INNER JOIN clases cl ON ac.clase_id = cl.id
                  LEFT JOIN categoriascurso cat ON c.categoria_id = cat.id
                  WHERE ac.alumno_id = ? 
                  AND ac.estado = 'completado'
                  ORDER BY ac.fecha_completado DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $alumno_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Obtener información del alumno por usuario_id
    public function getAlumnoByUsuarioId($usuario_id) {
        $query = "SELECT id, nombre FROM alumno WHERE usuario_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
}
?>