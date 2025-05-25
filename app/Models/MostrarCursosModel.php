<?php

namespace App\Models;

use App\Core\Conexion;

class MostrarCursosModel {
    private $conn;
    
    public function __construct($conexion) {
        $this->conn = $conexion;
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
                  WHERE ac.alumno_id = :alumno_id 
                   AND ac.estado = 'asignado'
                  ORDER BY ac.fecha_asignacion DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':alumno_id', $alumno_id, \PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Error al obtener cursos asignados: " . $e->getMessage());
        }
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
                  WHERE ac.alumno_id = :alumno_id 
                   AND ac.estado = 'completado'
                  ORDER BY ac.fecha_completado DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':alumno_id', $alumno_id, \PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Error al obtener cursos completados: " . $e->getMessage());
        }
    }

    // Obtener información del alumno por usuario_id
    public function getAlumnoByUsuarioId($usuario_id) {
        $query = "SELECT id, nombre FROM alumno WHERE usuario_id = :usuario_id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id, \PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Error al obtener información del alumno: " . $e->getMessage());
        }
    }

    // Subir evidencia y marcar curso como completado
    public function subirEvidencia($asignacion_id, $alumno_id, $evidencia_url) {
        $query = "UPDATE alumnocurso 
                  SET evidencia = :evidencia_url, 
                      estado = 'completado', 
                      fecha_completado = NOW()
                  WHERE id = :asignacion_id 
                    AND alumno_id = :alumno_id 
                    AND estado = 'asignado'";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':evidencia_url', $evidencia_url, \PDO::PARAM_STR);
            $stmt->bindParam(':asignacion_id', $asignacion_id, \PDO::PARAM_INT);
            $stmt->bindParam(':alumno_id', $alumno_id, \PDO::PARAM_INT);
            
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            throw new \Exception("Error al subir evidencia: " . $e->getMessage());
        }
    }
}