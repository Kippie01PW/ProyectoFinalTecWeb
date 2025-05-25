<?php

namespace App\Models;

use PDO;

class ClasesModel
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findClasesByMaestro(int $maestroId): array
    {
        $sql = "SELECT id, codigo, nombre, descripcion, created_at 
                FROM clases 
                WHERE maestro_id = :maestro_id 
                ORDER BY created_at DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':maestro_id', $maestroId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en ClasesModel::findClasesByMaestro: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene las clases del maestro con información de alumnos inscritos
     */
    public function findClasesWithAlumnosByMaestro(int $maestroId): array
    {
        $sql = "SELECT c.id, c.codigo, c.nombre, c.descripcion, c.created_at,
                       COUNT(ac.alumno_id) as total_alumnos
                FROM clases c 
                LEFT JOIN alumnoclase ac ON c.id = ac.clase_id
                WHERE c.maestro_id = :maestro_id 
                GROUP BY c.id, c.codigo, c.nombre, c.descripcion, c.created_at
                ORDER BY c.created_at DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':maestro_id', $maestroId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en ClasesModel::findClasesWithAlumnosByMaestro: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene los alumnos inscritos en una clase específica
     */
    public function getAlumnosByClase(int $claseId): array
    {
        $sql = "SELECT a.id, a.nombre, ac.estado, ac.fecha_asignacion
                FROM alumno a
                INNER JOIN alumnoclase ac ON a.id = ac.alumno_id
                WHERE ac.clase_id = :clase_id
                ORDER BY ac.fecha_asignacion DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':clase_id', $claseId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en ClasesModel::getAlumnosByClase: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene información detallada de una clase con sus alumnos
     */
    public function getClaseDetalleWithAlumnos(int $claseId, int $maestroId): ?array
    {
        // Primero verificar que la clase pertenece al maestro
        $sqlClase = "SELECT id, codigo, nombre, descripcion, created_at
                     FROM clases 
                     WHERE id = :clase_id AND maestro_id = :maestro_id";
        
        try {
            $stmt = $this->db->prepare($sqlClase);
            $stmt->bindParam(':clase_id', $claseId, PDO::PARAM_INT);
            $stmt->bindParam(':maestro_id', $maestroId, PDO::PARAM_INT);
            $stmt->execute();
            $clase = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$clase) {
                return null;
            }

            // Obtener alumnos de la clase
            $clase['alumnos'] = $this->getAlumnosByClase($claseId);
            $clase['total_alumnos'] = count($clase['alumnos']);
            
            return $clase;
            
        } catch (\PDOException $e) {
            error_log("Error en ClasesModel::getClaseDetalleWithAlumnos: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene estadísticas de las clases del maestro
     */
    public function getEstadisticasMaestro(int $maestroId): array
    {
        $sql = "SELECT 
                    COUNT(DISTINCT c.id) as total_clases,
                    COUNT(DISTINCT ac.alumno_id) as total_alumnos_unicos,
                    COUNT(ac.alumno_id) as total_inscripciones,
                    AVG(alumnos_por_clase.count) as promedio_alumnos_por_clase
                FROM clases c
                LEFT JOIN alumnoclase ac ON c.id = ac.clase_id
                LEFT JOIN (
                    SELECT clase_id, COUNT(alumno_id) as count
                    FROM alumnoclase
                    GROUP BY clase_id
                ) alumnos_por_clase ON c.id = alumnos_por_clase.clase_id
                WHERE c.maestro_id = :maestro_id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':maestro_id', $maestroId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'total_clases' => (int)($result['total_clases'] ?? 0),
                'total_alumnos_unicos' => (int)($result['total_alumnos_unicos'] ?? 0),
                'total_inscripciones' => (int)($result['total_inscripciones'] ?? 0),
                'promedio_alumnos_por_clase' => round((float)($result['promedio_alumnos_por_clase'] ?? 0), 1)
            ];
            
        } catch (\PDOException $e) {
            error_log("Error en ClasesModel::getEstadisticasMaestro: " . $e->getMessage());
            return [
                'total_clases' => 0,
                'total_alumnos_unicos' => 0,
                'total_inscripciones' => 0,
                'promedio_alumnos_por_clase' => 0
            ];
        }
    }
}